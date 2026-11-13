<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\GrowthStageDefinition;
use App\Models\Greenhouse;
use App\Models\Planting;
use App\Models\User;
use App\Models\Variety;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_is_available_without_authentication(): void
    {
        $crop = Crop::create(['name' => 'Томат', 'description' => 'Овощная культура']);
        $variety = Variety::create(['crop_id' => $crop->id, 'name' => 'Черри']);
        $stage = GrowthStageDefinition::create(['code' => 'seed', 'name' => 'Семя', 'sort_order' => 1]);
        $variety->growthStages()->attach($stage);

        $this->getJson('/api/v1/crops')->assertOk()
            ->assertJsonPath('data.0.name', 'Томат')
            ->assertJsonPath('data.0.varieties.0.name', 'Черри')
            ->assertJsonPath('data.0.varieties.0.growth_stages.0.code', 'seed');
    }

    public function test_search_validation_and_json_404_work(): void
    {
        Crop::create(['name' => 'Томат']);
        Crop::create(['name' => 'Огурец']);
        $this->getJson('/api/v1/crops?search=Томат')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/crops?per_page=101')->assertUnprocessable()->assertJsonValidationErrors('per_page');
        $this->getJson('/api/v1/crops/999')->assertNotFound();
    }

    public function test_catalog_mutations_require_api_key_and_return_correct_statuses(): void
    {
        config(['services.greenhouse_api.key' => 'test-secret']);
        $this->postJson('/api/v1/crops', ['name' => 'Томат'])->assertUnauthorized();

        $response = $this->withHeader('X-API-Key', 'test-secret')
            ->postJson('/api/v1/crops', ['name' => 'Томат'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Томат');

        $cropId = $response->json('data.id');
        $this->withHeader('X-API-Key', 'test-secret')
            ->patchJson("/api/v1/crops/{$cropId}", ['name' => 'Томат овощной'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Томат овощной');
        $this->withHeader('X-API-Key', 'test-secret')
            ->deleteJson("/api/v1/crops/{$cropId}")
            ->assertNoContent();
    }

    public function test_statistics_endpoint_returns_current_aggregate_values(): void
    {
        $user = User::factory()->create();
        $greenhouse = Greenhouse::create(['user_id' => $user->id, 'name' => 'Теплица']);
        $crop = Crop::create(['name' => 'Томат']);
        $variety = Variety::create(['crop_id' => $crop->id, 'name' => 'Черри']);
        GrowthStageDefinition::create(['code' => 'seed', 'name' => 'Посев', 'sort_order' => 1]);
        Planting::create([
            'greenhouse_id' => $greenhouse->id,
            'variety_id' => $variety->id,
            'planting_date' => '2026-11-14',
            'quantity' => 2,
        ]);

        $this->getJson('/api/v1/statistics')
            ->assertOk()
            ->assertJsonPath('data.crops_count', 1)
            ->assertJsonPath('data.varieties_count', 1)
            ->assertJsonPath('data.growth_stages_count', 1)
            ->assertJsonPath('data.active_plantings_count', 1);
    }
}
