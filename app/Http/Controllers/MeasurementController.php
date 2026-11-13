<?php
namespace App\Http\Controllers;
use App\Models\Greenhouse;
use App\Models\Measurement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class MeasurementController extends Controller
{
    private function rules(int $greenhouseId): array
    {
        return ['planting_id' => ['nullable', Rule::exists('plantings', 'id')->where('greenhouse_id', $greenhouseId)], 'temperature' => ['nullable', 'numeric', 'between:-50,100'], 'illumination' => ['nullable', 'numeric', 'min:0'], 'measured_at' => ['required', 'date']];
    }
    public function store(Request $request, Greenhouse $greenhouse)
    {
        abort_unless($greenhouse->user_id === $request->user()->id, 403);
        $data = $request->validate($this->rules($greenhouse->id));
        if (($data['temperature'] ?? null) === null && ($data['illumination'] ?? null) === null) return back()->withErrors(['measurement' => 'Укажите температуру или освещённость.'])->withInput();
        $greenhouse->measurements()->create($data);
        return redirect()->route('greenhouses.show', $greenhouse)->with('success', 'Показатели добавлены.');
    }
    public function update(Request $request, Measurement $measurement)
    {
        abort_unless($measurement->greenhouse->user_id === $request->user()->id, 403);
        $data = $request->validate($this->rules($measurement->greenhouse_id));
        if (($data['temperature'] ?? null) === null && ($data['illumination'] ?? null) === null) return back()->withErrors(['measurement' => 'Укажите температуру или освещённость.'])->withInput();
        $measurement->update($data);
        return back()->with('success', 'Показатели обновлены.');
    }
    public function destroy(Request $request, Measurement $measurement)
    {
        abort_unless($measurement->greenhouse->user_id === $request->user()->id, 403);
        $measurement->delete();
        return back()->with('success', 'Запись показателей удалена.');
    }
}
