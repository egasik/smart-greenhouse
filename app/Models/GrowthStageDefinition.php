<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class GrowthStageDefinition extends Model { use HasFactory; protected $fillable = ['code', 'name', 'sort_order']; public function varieties() { return $this->belongsToMany(Variety::class, 'variety_growth_stage'); } }
