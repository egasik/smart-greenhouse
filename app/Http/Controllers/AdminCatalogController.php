<?php
namespace App\Http\Controllers;
use App\Models\CatalogRequest;
use App\Models\Crop;
use App\Models\GrowthStageDefinition;
use App\Models\Variety;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class AdminCatalogController extends Controller
{
    public function index()
    {
        $requests = CatalogRequest::with(['requester', 'crop', 'growthStages'])->where('status', 'pending')->latest()->get();
        $crops = Crop::with('varieties.growthStages')->orderBy('name')->get();
        $stages = GrowthStageDefinition::orderBy('sort_order')->get();
        return view('admin.catalog.index', compact('requests', 'crops', 'stages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'crop_name' => ['required', 'string', 'max:100'],
            'variety_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'stage_ids' => ['required', 'array', 'min:1'],
            'stage_ids.*' => ['integer', Rule::exists('growth_stage_definitions', 'id')],
        ]);
        DB::transaction(function () use ($data) {
            $crop = Crop::firstOrCreate(['name' => $data['crop_name']], ['description' => $data['description'] ?? null]);
            $variety = Variety::firstOrCreate(['crop_id' => $crop->id, 'name' => $data['variety_name']]);
            $variety->growthStages()->sync($data['stage_ids']);
        });
        return back()->with('success', 'Культура и сорт добавлены в общий справочник.');
    }

    public function approve(Request $request, CatalogRequest $catalogRequest)
    {
        abort_if($catalogRequest->status !== 'pending', 422, 'Заявка уже обработана.');
        DB::transaction(function () use ($request, $catalogRequest) {
            $crop = $catalogRequest->crop ?: Crop::firstOrCreate(['name' => $catalogRequest->crop_name]);
            $variety = Variety::firstOrCreate(['crop_id' => $crop->id, 'name' => $catalogRequest->variety_name]);
            $variety->growthStages()->sync($catalogRequest->growthStages->pluck('id'));
            $catalogRequest->update(['status' => 'approved', 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);
        });
        return back()->with('success', 'Заявка одобрена. Сорт теперь доступен всем пользователям.');
    }

    public function reject(Request $request, CatalogRequest $catalogRequest)
    {
        $data = $request->validate(['review_comment' => ['nullable', 'string', 'max:1000']]);
        abort_if($catalogRequest->status !== 'pending', 422, 'Заявка уже обработана.');
        $catalogRequest->update(['status' => 'rejected', 'reviewed_by' => $request->user()->id, 'reviewed_at' => now(), 'review_comment' => $data['review_comment'] ?? null]);
        return back()->with('success', 'Заявка отклонена.');
    }
}
