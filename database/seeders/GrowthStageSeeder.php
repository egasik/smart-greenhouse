<?php
namespace Database\Seeders;
use App\Models\GrowthStageDefinition;
use Illuminate\Database\Seeder;
class GrowthStageSeeder extends Seeder
{
    public function run(): void
    {
        collect([['seed','Посев'],['germination','Прорастание'],['seedling','Всходы'],['vegetative','Вегетативный рост'],['budding','Бутонизация'],['flowering','Цветение'],['fruiting','Плодоношение'],['harvest','Сбор урожая']])
            ->each(fn ($stage, $index) => GrowthStageDefinition::updateOrCreate(['code' => $stage[0]], ['name' => $stage[1], 'sort_order' => $index + 1]));
    }
}
