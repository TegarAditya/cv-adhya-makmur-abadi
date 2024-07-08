<?php

namespace App\Models;

use App\Enums\PackageActionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_id',
        'package_id',
        'action',
        'quantity',
    ];

    protected $casts = [
        'action' =>  PackageActionEnum::class,
    ];
}
