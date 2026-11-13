<?php
namespace App\Http\Controllers;
use App\Models\CareTask;
use App\Models\Greenhouse;
use App\Models\Variety;
use Illuminate\Http\Request;
class GreenhouseController extends Controller
{
    public function index(Request $request) { $filters = $request->validate(['search' => ['nullable','string','max:100']]); $greenhouses = $request->user()->greenhouses()->withCount('plantings')->when($filters['search'] ?? null, fn ($query, $search) => $query->where(fn ($nested) => $nested->where('name','like',"%{$search}%")->orWhere('location','like',"%{$search}%")))->latest()->get(); return view('dashboard', compact('greenhouses','filters')); }
    public function store(Request $request) { $data = $request->validate(['name' => ['required','string','max:100'], 'location' => ['nullable','string','max:150'], 'note' => ['nullable','string']]); $request->user()->greenhouses()->create($data); return redirect()->route('dashboard')->with('success','Теплица добавлена.'); }
    public function show(Request $request, Greenhouse $greenhouse)
    {
        abort_unless($greenhouse->user_id === $request->user()->id, 403);
        $filters = $request->validate(['task_status' => ['nullable','in:planned,completed,cancelled'], 'date_from' => ['nullable','date'], 'date_to' => ['nullable','date','after_or_equal:date_from']]);
        $greenhouse->load(['plantings.variety.crop', 'plantings.growthStages' => fn ($q) => $q->with('definition')->latest('recorded_at')]);
        $varieties = Variety::with(['crop', 'growthStages'])->join('crops','crops.id','=','varieties.crop_id')->orderBy('crops.name')->orderBy('varieties.name')->select('varieties.*')->get();
        $measurements = $greenhouse->measurements()->with('planting.variety.crop')->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('measured_at','>=',$date))->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('measured_at','<=',$date))->latest('measured_at')->get();
        $careTasks = CareTask::whereHas('planting', fn ($q) => $q->where('greenhouse_id', $greenhouse->id))->with('planting.variety.crop')->when($filters['task_status'] ?? null, fn ($query, $status) => $query->where('status',$status))->orderBy('status')->orderBy('planned_at')->get();
        return view('greenhouses.show', compact('greenhouse','varieties','measurements','careTasks','filters'));
    }
    public function destroy(Request $request, Greenhouse $greenhouse) { abort_unless($greenhouse->user_id === $request->user()->id,403); $greenhouse->delete(); return redirect()->route('dashboard')->with('success','Теплица удалена.'); }
    public function update(Request $request, Greenhouse $greenhouse) { abort_unless($greenhouse->user_id === $request->user()->id,403); $data = $request->validate(['name' => ['required','string','max:100'], 'location' => ['nullable','string','max:150'], 'note' => ['nullable','string']]); $greenhouse->update($data); return back()->with('success','Данные теплицы обновлены.'); }
}
