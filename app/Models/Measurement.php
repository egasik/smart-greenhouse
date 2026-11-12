<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Measurement extends Model { use HasFactory; protected $fillable = ['greenhouse_id', 'planting_id', 'temperature', 'illumination', 'measured_at']; protected $casts = ['temperature' => 'float', 'illumination' => 'float', 'measured_at' => 'datetime']; public function greenhouse() { return $this->belongsTo(Greenhouse::class); } public function planting() { return $this->belongsTo(Planting::class); } }
