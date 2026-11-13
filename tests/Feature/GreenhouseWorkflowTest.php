<?php

namespace Tests\Feature;

use App\Models\CareTask;
use App\Models\Crop;
use App\Models\Greenhouse;
use App\Models\GrowthStageDefinition;
use App\Models\Planting;
use App\Models\User;
use App\Models\Variety;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GreenhouseWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_update_and_delete_own_greenhouse(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('greenhouses.store'), [
            'name' => 'Теплица № 1',
            'location' => 'Участок',
            'note' => 'Тестовая теплица',
        ])->assertRedirect(route('dashboard'));

        $greenhouse = Greenhouse::where('user_id', $user->id)->firstOrFail();

        $this->actingAs($user)->patch(route('greenhouses.update', $greenhouse), [
            'name' => 'Теплица № 2',
            'location' => 'Сад',
            'note' => null,
        ])->assertRedirect();

        $this->assertDatabaseHas('greenhouses', ['id' => $greenhouse->id, 'name' => 'Теплица № 2']);

        $this->actingAs($user)
            ->delete(route('greenhouses.destroy', $greenhouse))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('greenhouses', ['id' => $greenhouse->id]);
    }

    public function test_user_cannot_open_or_change_another_users_greenhouse(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $greenhouse = Greenhouse::create(['user_id' => $owner->id, 'name' => 'Чужая теплица']);

        $this->actingAs($otherUser)->get(route('greenhouses.show', $greenhouse))->assertForbidden();
        $this->actingAs($otherUser)->patch(route('greenhouses.update', $greenhouse), [
            'name' => 'Изменённая',
        ])->assertForbidden();
        $this->actingAs($otherUser)->delete(route('greenhouses.destroy', $greenhouse))->assertForbidden();
    }

    public function test_user_can_manage_planting_with_fixed_statuses(): void
    {
        [$user, $greenhouse, $variety] = $this->catalogContext();

        $this->actingAs($user)->post(route('plantings.store', $greenhouse), [
            'variety_id' => $variety->id,
            'planting_date' => '2026-11-14',
            'quantity' => 12,
        ])->assertRedirect(route('greenhouses.show', $greenhouse));

        $planting = Planting::where('greenhouse_id', $greenhouse->id)->firstOrFail();
        $this->assertSame('active', $planting->status);

        $this->actingAs($user)->patch(route('plantings.update', $planting), [
            'variety_id' => $variety->id,
            'planting_date' => '2026-11-14',
            'quantity' => 10,
            'status' => 'cancelled',
        ])->assertRedirect();

        $this->assertDatabaseHas('plantings', ['id' => $planting->id, 'status' => 'cancelled']);

        $this->actingAs($user)->patch(route('plantings.update', $planting), [
            'variety_id' => $variety->id,
            'planting_date' => '2026-11-14',
            'quantity' => 10,
            'status' => 'unknown',
        ])->assertSessionHasErrors('status');
    }

    public function test_measurements_require_temperature_or_illumination_and_belong_to_greenhouse(): void
    {
        [$user, $greenhouse, $variety] = $this->catalogContext();
        $planting = Planting::create([
            'greenhouse_id' => $greenhouse->id,
            'variety_id' => $variety->id,
            'planting_date' => '2026-11-14',
            'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('measurements.store', $greenhouse), [
            'planting_id' => $planting->id,
            'temperature' => 24.75,
            'illumination' => 18500.5,
            'measured_at' => '2026-11-14 10:00:00',
        ])->assertRedirect(route('greenhouses.show', $greenhouse));

        $this->assertDatabaseHas('measurements', [
            'greenhouse_id' => $greenhouse->id,
            'planting_id' => $planting->id,
            'temperature' => 24.75,
            'illumination' => 18500.5,
        ]);

        $this->actingAs($user)->post(route('measurements.store', $greenhouse), [
            'measured_at' => '2026-11-14 11:00:00',
        ])->assertSessionHasErrors('measurement');
    }

    public function test_only_stages_assigned_to_variety_can_be_recorded(): void
    {
        [$user, $greenhouse, $variety] = $this->catalogContext();
        $allowed = GrowthStageDefinition::create(['code' => 'seed', 'name' => 'Посев', 'sort_order' => 1]);
        $forbidden = GrowthStageDefinition::create(['code' => 'harvest', 'name' => 'Сбор урожая', 'sort_order' => 8]);
        $variety->growthStages()->attach($allowed);
        $planting = Planting::create([
            'greenhouse_id' => $greenhouse->id,
            'variety_id' => $variety->id,
            'planting_date' => '2026-11-14',
            'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('growth-stages.store', $planting), [
            'growth_stage_definition_id' => $allowed->id,
            'condition' => 'good',
            'recorded_at' => '2026-11-14 10:00:00',
        ])->assertRedirect(route('greenhouses.show', $greenhouse));

        $this->assertDatabaseHas('growth_stages', [
            'planting_id' => $planting->id,
            'growth_stage_definition_id' => $allowed->id,
        ]);

        $this->actingAs($user)->post(route('growth-stages.store', $planting), [
            'growth_stage_definition_id' => $forbidden->id,
            'recorded_at' => '2026-11-14 11:00:00',
        ])->assertSessionHasErrors('growth_stage_definition_id');
    }

    public function test_user_can_plan_complete_and_delete_care_task(): void
    {
        [$user, $greenhouse, $variety] = $this->catalogContext();
        $planting = Planting::create([
            'greenhouse_id' => $greenhouse->id,
            'variety_id' => $variety->id,
            'planting_date' => '2026-11-14',
            'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('care-tasks.store', $greenhouse), [
            'planting_id' => $planting->id,
            'type' => 'watering',
            'planned_at' => '2026-11-15 09:00:00',
            'description' => 'Полить растение',
        ])->assertRedirect(route('greenhouses.show', $greenhouse));

        $task = CareTask::where('planting_id', $planting->id)->firstOrFail();

        $this->actingAs($user)->patch(route('care-tasks.complete', $task))->assertRedirect();
        $this->assertSame('completed', $task->refresh()->status);
        $this->assertNotNull($task->completed_at);

        $this->actingAs($user)->delete(route('care-tasks.destroy', $task))->assertRedirect();
        $this->assertDatabaseMissing('care_tasks', ['id' => $task->id]);
    }

    /**
     * @return array{User, Greenhouse, Variety}
     */
    private function catalogContext(): array
    {
        $user = User::factory()->create();
        $greenhouse = Greenhouse::create(['user_id' => $user->id, 'name' => 'Основная теплица']);
        $crop = Crop::create(['name' => 'Томат']);
        $variety = Variety::create(['crop_id' => $crop->id, 'name' => 'Черри']);

        return [$user, $greenhouse, $variety];
    }
}
