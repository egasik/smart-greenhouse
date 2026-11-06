<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CatalogRequest extends Model { use HasFactory; protected $fillable = ['requested_by', 'crop_id', 'crop_name', 'variety_name', 'status', 'reviewed_by', 'reviewed_at', 'review_comment']; protected $casts = ['reviewed_at' => 'datetime']; public function requester() { return $this->belongsTo(User::class, 'requested_by'); } public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); } public function crop() { return $this->belongsTo(Crop::class); } public function growthStages() { return $this->belongsToMany(GrowthStageDefinition::class, 'catalog_request_growth_stage')->orderBy('sort_order'); } }
