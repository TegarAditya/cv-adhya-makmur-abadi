<?php

namespace App\Models;

use App\Enums\PackageActionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'semester_id',
        'education_grade_id',
        'education_level_id',
        'name',
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function educationGrade()
    {
        return $this->belongsTo(EducationGrade::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function stocks()
    {
        return $this->hasMany(PackageStock::class);
    }

    public function getRemainingStock(): int
    {
        $addition = $this->stocks()
            ->where('action', PackageActionEnum::ADDITION)
            ->sum('quantity');

        $subtraction = $this->stocks()
            ->where('action', PackageActionEnum::REDUCTION)
            ->sum('quantity');

        $out = $this->orders()->where('is_valid', true)->count();

        return $addition - $subtraction - $out;
    }
}
