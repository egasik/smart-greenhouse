<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CropResource;
use App\Http\Resources\Api\V1\GrowthStageResource;
use App\Http\Resources\Api\V1\VarietyResource;
use App\Models\Crop;
use App\Models\GrowthStageDefinition;
use App\Models\Variety;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CatalogController extends Controller
{
    public function crops(Request $request): AnonymousResourceCollection
    {
        $data = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return CropResource::collection(Crop::query()
            ->with(['varieties.growthStages'])
            ->when($data['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate($data['per_page'] ?? 20)
            ->withQueryString());
    }

    public function crop(Crop $crop): CropResource
    {
        return new CropResource($crop->load(['varieties.growthStages']));
    }

    public function varieties(Request $request): AnonymousResourceCollection
    {
        $data = $request->validate([
            'crop_id' => ['nullable', 'integer', 'exists:crops,id'],
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return VarietyResource::collection(Variety::query()
            ->with(['crop', 'growthStages'])
            ->when($data['crop_id'] ?? null, fn ($query, $cropId) => $query->where('crop_id', $cropId))
            ->when($data['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate($data['per_page'] ?? 20)
            ->withQueryString());
    }

    public function variety(Variety $variety): VarietyResource
    {
        return new VarietyResource($variety->load(['crop', 'growthStages']));
    }

    public function growthStages(): AnonymousResourceCollection
    {
        return GrowthStageResource::collection(GrowthStageDefinition::query()->orderBy('sort_order')->get());
    }

    public function storeCrop(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => ['required','string','max:100','unique:crops,name'], 'description' => ['nullable','string']]);
        return (new CropResource(Crop::create($data)))->response()->setStatusCode(201);
    }

    public function updateCrop(Request $request, Crop $crop): CropResource
    {
        $data = $request->validate(['name' => ['sometimes','required','string','max:100', Rule::unique('crops','name')->ignore($crop)], 'description' => ['nullable','string']]);
        $crop->update($data);
        return new CropResource($crop->fresh()->load('varieties.growthStages'));
    }

    public function destroyCrop(Crop $crop): Response
    {
        $crop->delete();
        return response()->noContent();
    }

    public function storeVariety(Request $request): JsonResponse
    {
        $data = $this->validateVariety($request);
        $stageIds = $data['stage_ids'];
        unset($data['stage_ids']);
        $variety = Variety::create($data);
        $variety->growthStages()->sync($stageIds);
        return (new VarietyResource($variety->load(['crop','growthStages'])))->response()->setStatusCode(201);
    }

    public function updateVariety(Request $request, Variety $variety): VarietyResource
    {
        $data = $this->validateVariety($request, $variety);
        $stageIds = $data['stage_ids'];
        unset($data['stage_ids']);
        $variety->update($data);
        $variety->growthStages()->sync($stageIds);
        return new VarietyResource($variety->fresh()->load(['crop','growthStages']));
    }

    public function destroyVariety(Variety $variety): Response
    {
        $variety->delete();
        return response()->noContent();
    }

    private function validateVariety(Request $request, ?Variety $variety = null): array
    {
        $nameRule = Rule::unique('varieties','name')->where('crop_id', $request->integer('crop_id'));
        if ($variety) {
            $nameRule->ignore($variety);
        }

        return $request->validate([
            'crop_id' => ['required','integer','exists:crops,id'],
            'name' => ['required','string','max:100', $nameRule],
            'stage_ids' => ['required','array','min:1'],
            'stage_ids.*' => ['integer','distinct','exists:growth_stage_definitions,id'],
        ]);
    }
}
