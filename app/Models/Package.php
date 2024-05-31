<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
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
}
