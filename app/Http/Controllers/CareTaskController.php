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
        if ($greenhouse->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'planting_id' => [
                'required',
                Rule::exists('plantings', 'id')
                    ->where('greenhouse_id', $greenhouse->id),
            ],
            'type' => ['required', 'string', 'max:100'],
            'planned_at' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $data['status'] = 'planned';

        CareTask::create($data);

        return redirect()
            ->route('greenhouses.show', $greenhouse)
            ->with('success', 'Задача по уходу добавлена.');
    }

    public function complete(Request $request, CareTask $careTask)
    {
        $careTask->load('planting.greenhouse');

        if ($careTask->planting->greenhouse->user_id !== $request->user()->id) {
            abort(403);
        }

        $careTask->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Задача отмечена как выполненная.');
    }
}