<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class GrowthStage extends Model { use HasFactory; protected $fillable = ['planting_id', 'growth_stage_definition_id', 'condition', 'recorded_at', 'comment']; protected $casts = ['recorded_at' => 'datetime']; public function planting() { return $this->belongsTo(Planting::class); } public function definition() { return $this->belongsTo(GrowthStageDefinition::class, 'growth_stage_definition_id'); } }
