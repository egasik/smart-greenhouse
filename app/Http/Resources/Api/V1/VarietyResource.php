<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VarietyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'crop' => $this->whenLoaded('crop', fn () => ['id' => $this->crop->id, 'name' => $this->crop->name]),
            'growth_stages' => GrowthStageResource::collection($this->whenLoaded('growthStages')),
        ];
    }
}
