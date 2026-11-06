<?php
namespace App\Http\Controllers;
use App\Models\CatalogRequest;
use App\Models\Crop;
use App\Models\GrowthStageDefinition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class CropController extends Controller
{
    public function index()
    {
        $crops = Crop::with(['varieties.growthStages'])->orderBy('name')->get();
        $stages = GrowthStageDefinition::orderBy('sort_order')->get();
        return view('crops.index', compact('crops', 'stages'));
    }

    public function requestStore(Request $request)
    {
        $data = $request->validate([
            'crop_id' => ['nullable', 'exists:crops,id'],
            'crop_name' => ['nullable', 'string', 'max:100', 'required_without:crop_id'],
            'variety_name' => ['required', 'string', 'max:100'],
            'stage_ids' => ['required', 'array', 'min:1'],
            'stage_ids.*' => ['integer', Rule::exists('growth_stage_definitions', 'id')],
        ]);

        $duplicate = CatalogRequest::query()
            ->where('status', 'pending')
            ->where('variety_name', $data['variety_name'])
            ->when($data['crop_id'] ?? null, fn ($query, $cropId) => $query->where('crop_id', $cropId))
            ->when(! ($data['crop_id'] ?? null), fn ($query) => $query->where('crop_name', $data['crop_name']))
            ->exists();

        if ($duplicate) return back()->withErrors(['catalog' => 'Такая заявка уже ожидает проверки администратора.'])->withInput();

        $catalogRequest = CatalogRequest::create([
            'requested_by' => $request->user()->id,
            'crop_id' => $data['crop_id'] ?? null,
            'crop_name' => $data['crop_name'] ?? null,
            'variety_name' => $data['variety_name'],
        ]);
        $catalogRequest->growthStages()->sync($data['stage_ids']);
        return redirect()->route('crops.index')->with('success', 'Заявка отправлена администратору на проверку.');
    }
}
