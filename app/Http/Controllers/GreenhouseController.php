<?php

namespace App\Http\Controllers;

use App\Models\CareTask;
use App\Models\Crop;
use App\Models\Greenhouse;
use Illuminate\Http\Request;

class GreenhouseController extends Controller
{
    public function index(Request $request)
    {
        $greenhouses = $request->user()
            ->greenhouses()
            ->withCount('plantings')
            ->latest()
            ->get();

        return view('dashboard', compact('greenhouses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:150'],
            'note' => ['nullable', 'string'],
        ]);

        $request->user()->greenhouses()->create($data);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Теплица добавлена.');
    }

    public function show(Request $request, Greenhouse $greenhouse)
    {
        if ($greenhouse->user_id !== $request->user()->id) {
            abort(403);
        }

        $greenhouse->load([
            'plantings.crop',
            'plantings.growthStages' => function ($query) {
                $query->latest('recorded_at');
            },
        ]);

        $crops = Crop::orderBy('name')->get();

        $measurements = $greenhouse->measurements()
            ->with('planting.crop')
            ->latest('measured_at')
            ->get();

        $careTasks = CareTask::whereHas('planting', function ($query) use ($greenhouse) {
            $query->where('greenhouse_id', $greenhouse->id);
        })
            ->with('planting.crop')
            ->orderBy('status')
            ->orderBy('planned_at')
            ->get();

        return view('greenhouses.show', compact(
            'greenhouse',
            'crops',
            'measurements',
            'careTasks'
        ));
    }

    public function destroy(Request $request, Greenhouse $greenhouse)
    {
        if ($greenhouse->user_id !== $request->user()->id) {
            abort(403);
        }

        $greenhouse->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Теплица удалена.');
    }
}

