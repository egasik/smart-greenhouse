<?php
namespace App\Http\Controllers;
use App\Models\CatalogRequest;
use App\Models\Crop;
use Illuminate\Http\Request;

class CropController extends Controller
{
    public function index(Request $request)
    {
        $crops = Crop::with(['varieties.growthStages'])->orderBy('name')->get();
        $myRequests = CatalogRequest::query()
            ->with(['crop', 'reviewer'])
            ->where('requested_by', $request->user()->id)
            ->latest()
            ->get();

        return view('crops.index', compact('crops', 'myRequests'));
    }

    public function requestStore(Request $request)
    {
        $data = $request->validate([
            'crop_id' => ['nullable', 'exists:crops,id'],
            'crop_name' => ['nullable', 'string', 'max:100', 'required_without:crop_id'],
            'variety_name' => ['required', 'string', 'max:100'],
        ]);

        $cropName = isset($data['crop_name']) ? trim($data['crop_name']) : null;
        $varietyName = trim($data['variety_name']);
        $cropId = $data['crop_id'] ?? null;

        $duplicate = CatalogRequest::query()
            ->where('status', 'pending')
            ->where('variety_name', $varietyName)
            ->when($cropId, fn ($query) => $query->where('crop_id', $cropId))
            ->when(! $cropId, fn ($query) => $query->where('crop_name', $cropName))
            ->exists();

        if ($duplicate) {
            return back()
                ->withErrors(['catalog' => 'Такая заявка уже ожидает проверки администратора.'])
                ->withInput();
        }

        CatalogRequest::create([
            'requested_by' => $request->user()->id,
            'crop_id' => $cropId,
            'crop_name' => $cropId ? null : $cropName,
            'variety_name' => $varietyName,
        ]);

        return redirect()->route('crops.index')->with('success', 'Заявка отправлена администратору на проверку.');
    }
}
