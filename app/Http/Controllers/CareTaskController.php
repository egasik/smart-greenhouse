<?php
namespace App\Http\Controllers;
use App\Models\CareTask;
use App\Models\Greenhouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class CareTaskController extends Controller
{
    public function store(Request $request, Greenhouse $greenhouse)
    {
        abort_unless($greenhouse->user_id === $request->user()->id, 403);
        $data = $request->validate(['planting_id' => ['required', Rule::exists('plantings', 'id')->where('greenhouse_id', $greenhouse->id)], 'type' => ['required', Rule::in(CareTask::TYPES)], 'planned_at' => ['required', 'date'], 'description' => ['nullable', 'string']]);
        CareTask::create($data + ['status' => 'planned']);
        return redirect()->route('greenhouses.show', $greenhouse)->with('success', 'Задача по уходу добавлена.');
    }
    public function complete(Request $request, CareTask $careTask)
    {
        $careTask->load('planting.greenhouse'); abort_unless($careTask->planting->greenhouse->user_id === $request->user()->id, 403);
        $careTask->update(['status' => 'completed', 'completed_at' => now()]);
        return back()->with('success', 'Задача отмечена как выполненная.');
    }
}
