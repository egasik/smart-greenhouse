<?php
namespace App\Http\Controllers;
use App\Models\CareTask;
use App\Models\Greenhouse;
use App\Models\Variety;
use Illuminate\Http\Request;
class GreenhouseController extends Controller
{
    public function index(Request $request) { $greenhouses = $request->user()->greenhouses()->withCount('plantings')->latest()->get(); return view('dashboard', compact('greenhouses')); }
    public function store(Request $request) { $data = $request->validate(['name' => ['required','string','max:100'], 'location' => ['nullable','string','max:150'], 'note' => ['nullable','string']]); $request->user()->greenhouses()->create($data); return redirect()->route('dashboard')->with('success','Теплица добавлена.'); }
    public function show(Request $request, Greenhouse $greenhouse)
    {
        abort_unless($greenhouse->user_id === $request->user()->id, 403);
        $greenhouse->load(['plantings.variety.crop', 'plantings.growthStages' => fn ($q) => $q->with('definition')->latest('recorded_at')]);
        $varieties = Variety::with(['crop', 'growthStages'])->join('crops','crops.id','=','varieties.crop_id')->orderBy('crops.name')->orderBy('varieties.name')->select('varieties.*')->get();
        $measurements = $greenhouse->measurements()->with('planting.variety.crop')->latest('measured_at')->get();
        $careTasks = CareTask::whereHas('planting', fn ($q) => $q->where('greenhouse_id', $greenhouse->id))->with('planting.variety.crop')->orderBy('status')->orderBy('planned_at')->get();
        return view('greenhouses.show', compact('greenhouse','varieties','measurements','careTasks'));
    }
    public function destroy(Request $request, Greenhouse $greenhouse) { abort_unless($greenhouse->user_id === $request->user()->id,403); $greenhouse->delete(); return redirect()->route('dashboard')->with('success','Теплица удалена.'); }
}
