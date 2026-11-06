<?php
namespace Database\Seeders;
use App\Models\Crop;
use App\Models\GrowthStageDefinition;
use App\Models\Variety;
use Illuminate\Database\Seeder;
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $all = GrowthStageDefinition::pluck('id');
        $withoutFlowering = GrowthStageDefinition::whereNotIn('code', ['budding','flowering','fruiting'])->pluck('id');
        foreach ([['Томат','Бычье сердце',$all],['Томат','Черри',$all],['Огурец','Герман F1',$all],['Перец сладкий','Калифорнийское чудо',$all],['Салат','Айсберг',$withoutFlowering]] as [$cropName,$varietyName,$stages]) {
            $crop = Crop::firstOrCreate(['name' => $cropName]);
            $variety = Variety::firstOrCreate(['crop_id' => $crop->id, 'name' => $varietyName]);
            $variety->growthStages()->sync($stages);
        }
    }
}
