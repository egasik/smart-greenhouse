<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Crop extends Model { use HasFactory; protected $fillable = ['name', 'description']; public function varieties() { return $this->hasMany(Variety::class); } public function plantings() { return $this->hasManyThrough(Planting::class, Variety::class); } }
