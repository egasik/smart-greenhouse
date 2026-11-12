<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Variety extends Model { use HasFactory; protected $fillable = ['crop_id', 'name']; public function crop() { return $this->belongsTo(Crop::class); } public function plantings() { return $this->hasMany(Planting::class); } public function growthStages() { return $this->belongsToMany(GrowthStageDefinition::class, 'variety_growth_stage')->orderBy('sort_order'); } }
