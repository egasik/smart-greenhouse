<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Planting extends Model { use HasFactory; protected $fillable = ['greenhouse_id', 'variety_id', 'planting_date', 'quantity', 'status']; protected $casts = ['planting_date' => 'date']; public function greenhouse() { return $this->belongsTo(Greenhouse::class); } public function variety() { return $this->belongsTo(Variety::class); } public function growthStages() { return $this->hasMany(GrowthStage::class); } public function measurements() { return $this->hasMany(Measurement::class); } public function careTasks() { return $this->hasMany(CareTask::class); } }
