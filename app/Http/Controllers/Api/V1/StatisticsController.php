<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\GrowthStageDefinition;
use App\Models\Planting;
use App\Models\Variety;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(['data' => [
            'crops_count' => Crop::count(),
            'varieties_count' => Variety::count(),
            'growth_stages_count' => GrowthStageDefinition::count(),
            'active_plantings_count' => Planting::where('status', 'active')->count(),
        ], 'generated_at' => now()->toIso8601String()]);
    }
}
