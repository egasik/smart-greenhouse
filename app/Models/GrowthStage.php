<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrowthStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'planting_id',
        'stage',
        'status',
        'recorded_at',
        'comment',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function planting()
    {
        return $this->belongsTo(Planting::class);
    }
}