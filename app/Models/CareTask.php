<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CareTask extends Model { use HasFactory; public const TYPES = ['watering', 'fertilizing', 'ventilation', 'treatment', 'other']; protected $fillable = ['planting_id', 'type', 'planned_at', 'status', 'description', 'completed_at']; protected $casts = ['planned_at' => 'datetime', 'completed_at' => 'datetime']; public function planting() { return $this->belongsTo(Planting::class); } }
