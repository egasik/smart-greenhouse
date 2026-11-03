<?php

namespace App\Http\Controllers;

use App\Models\Greenhouse;
use App\Models\Planting;
use Illuminate\Http\Request;

class PlantingController extends Controller
{
    public function store(Request $request, Greenhouse $greenhouse)
    {
        if ($greenhouse->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'crop_id' => ['required', 'exists:crops,id'],
            'planting_date' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $greenhouse->plantings()->create($data);

        return redirect()
            ->route('greenhouses.show', $greenhouse)
            ->with('success', 'Посадка добавлена.');
    }

    public function destroy(Request $request, Planting $planting)
    {
        if ($planting->greenhouse->user_id !== $request->user()->id) {
            abort(403);
        }

        $greenhouse = $planting->greenhouse;
        $planting->delete();

        return redirect()
            ->route('greenhouses.show', $greenhouse)
            ->with('success', 'Посадка удалена.');
    }
}