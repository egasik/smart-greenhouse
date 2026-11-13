<?php

namespace Tests\Feature;

use App\Models\CatalogRequest;
use App\Models\GrowthStageDefinition;
use App\Models\User;
use App\Models\Variety;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogRequestWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_crop_request_without_selecting_growth_stages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('catalog-requests.store'), [
                'crop_name' => 'Баклажан',
                'variety_name' => 'Алмаз',
            ])
            ->assertRedirect(route('crops.index'));

        $this->assertDatabaseHas('catalog_requests', [
            'requested_by' => $user->id,
            'crop_name' => 'Баклажан',
            'variety_name' => 'Алмаз',
            'status' => 'pending',
        ]);
    }

    public function test_user_sees_own_catalog_requests(): void
    {
        $user = User::factory()->create();
        CatalogRequest::create([
            'requested_by' => $user->id,
            'crop_name' => 'Баклажан',
            'variety_name' => 'Алмаз',
        ]);

        $this->actingAs($user)
            ->get(route('crops.index'))
            ->assertOk()
            ->assertSee('Баклажан')
            ->assertSee('Алмаз')
            ->assertSee('На проверке');
    }

    public function test_admin_assigns_growth_stages_when_approving_request(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $stage = GrowthStageDefinition::create([
            'code' => 'seed',
            'name' => 'Посев',
            'sort_order' => 1,
        ]);
        $catalogRequest = CatalogRequest::create([
            'requested_by' => $user->id,
            'crop_name' => 'Баклажан',
            'variety_name' => 'Алмаз',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.catalog-requests.approve', $catalogRequest), [
                'stage_ids' => [$stage->id],
            ])
            ->assertRedirect();

        $catalogRequest->refresh();
        $variety = Variety::where('name', 'Алмаз')->firstOrFail();

        $this->assertSame('approved', $catalogRequest->status);
        $this->assertSame($admin->id, $catalogRequest->reviewed_by);
        $this->assertTrue($variety->growthStages->contains($stage));
    }

    public function test_admin_cannot_approve_request_without_growth_stages(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $catalogRequest = CatalogRequest::create([
            'requested_by' => $user->id,
            'crop_name' => 'Баклажан',
            'variety_name' => 'Алмаз',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.catalog.index'))
            ->patch(route('admin.catalog-requests.approve', $catalogRequest), [])
            ->assertRedirect(route('admin.catalog.index'))
            ->assertSessionHasErrors('stage_ids');

        $this->assertDatabaseHas('catalog_requests', [
            'id' => $catalogRequest->id,
            'status' => 'pending',
        ]);
    }

    public function test_regular_user_cannot_open_admin_catalog(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.catalog.index'))
            ->assertForbidden();
    }

    public function test_admin_can_reject_request_with_comment(): void
    {
        $requester = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $catalogRequest = CatalogRequest::create([
            'requested_by' => $requester->id,
            'crop_name' => 'Редис',
            'variety_name' => 'Жара',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.catalog-requests.reject', $catalogRequest), [
                'review_comment' => 'Требуется уточнение названия.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('catalog_requests', [
            'id' => $catalogRequest->id,
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
            'review_comment' => 'Требуется уточнение названия.',
        ]);
    }

    public function test_admin_can_add_catalog_entry_and_assign_fixed_stages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seed = GrowthStageDefinition::create([
            'code' => 'seed',
            'name' => 'Посев',
            'sort_order' => 1,
        ]);
        $harvest = GrowthStageDefinition::create([
            'code' => 'harvest',
            'name' => 'Сбор урожая',
            'sort_order' => 8,
        ]);

        $this->actingAs($admin)->post(route('admin.catalog.store'), [
            'crop_name' => 'Перец',
            'variety_name' => 'Калифорнийское чудо',
            'description' => 'Овощная культура',
            'stage_ids' => [$seed->id, $harvest->id],
        ])->assertRedirect();

        $variety = Variety::where('name', 'Калифорнийское чудо')->firstOrFail();

        $this->assertSame('Перец', $variety->crop->name);
        $this->assertEqualsCanonicalizing(
            [$seed->id, $harvest->id],
            $variety->growthStages()->pluck('growth_stage_definitions.id')->all(),
        );
    }
}
