<?php

use App\Models\EducationGrade;
use App\Models\EducationLevel;
use App\Models\Semester;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Monolog\Level;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Semester::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(EducationGrade::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(EducationLevel::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('price');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
