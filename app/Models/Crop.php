<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'variety',
        'description',
    ];

    public function plantings()
    {
        return $this->hasMany(Planting::class);
    }
}