<?php

namespace App\Http\Controllers;

use App\Models\Planting;
use Illuminate\Http\Request;

class GrowthStageController extends Controller
{
    public function store(Request $request, Planting $planting)
    {
        if ($planting->greenhouse->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'stage' => ['required', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:50'],
            'recorded_at' => ['required', 'date'],
            'comment' => ['nullable', 'string'],
        ]);

        $planting->growthStages()->create($data);

        return redirect()
            ->route('greenhouses.show', $planting->greenhouse)
            ->with('success', 'Этап роста добавлен.');
    }
}
