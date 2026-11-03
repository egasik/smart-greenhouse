<?php

namespace App\Http\Controllers;

use App\Models\Greenhouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MeasurementController extends Controller
{
    public function store(Request $request, Greenhouse $greenhouse)
    {
        if ($greenhouse->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'planting_id' => [
                'nullable',
                Rule::exists('plantings', 'id')
                    ->where('greenhouse_id', $greenhouse->id),
            ],
            'temperature' => ['nullable', 'numeric', 'between:-50,100'],
            'illumination' => ['nullable', 'numeric', 'min:0'],
            'measured_at' => ['required', 'date'],
        ]);

        if ($data['temperature'] === null && $data['illumination'] === null) {
            return back()
                ->withErrors(['measurement' => 'Укажите температуру или освещённость.'])
                ->withInput();
        }

        $greenhouse->measurements()->create($data);

        return redirect()
            ->route('greenhouses.show', $greenhouse)
            ->with('success', 'Показатели добавлены.');
    }
}