<?php
namespace App\Http\Controllers;
use App\Models\Planting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class GrowthStageController extends Controller
{
    public function store(Request $request, Planting $planting)
    {
        abort_unless($planting->greenhouse->user_id === $request->user()->id, 403);
        $data = $request->validate(['growth_stage_definition_id' => ['required', 'exists:growth_stage_definitions,id'], 'condition' => ['nullable', Rule::in(['good', 'attention', 'critical'])], 'recorded_at' => ['required', 'date'], 'comment' => ['nullable', 'string']]);
        $allowed = $planting->variety->growthStages()->whereKey($data['growth_stage_definition_id'])->exists();
        if (! $allowed) return back()->withErrors(['growth_stage_definition_id' => 'Этот этап не предусмотрен для выбранного сорта.'])->withInput();
        $planting->growthStages()->create($data);
        return redirect()->route('greenhouses.show', $planting->greenhouse)->with('success', 'Этап роста добавлен.');
    }
}
