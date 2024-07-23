<?php

use App\Models\City;
use App\Models\District;
use App\Models\Package;
use App\Models\PaymentMethod;
use App\Models\Subdistrict;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('student_name');
            $table->string('student_email');
            $table->string('student_phone');
            $table->string('school_name');
            $table->boolean('is_valid')->default(false);
            $table->foreignIdFor(Package::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(City::class);
            $table->foreignIdFor(District::class);
            $table->foreignIdFor(Subdistrict::class);
            $table->string('postal_code');
            $table->string('address')->nullable();
            $table->foreignIdFor(PaymentMethod::class)->constrained()->cascadeOnDelete();
            $table->string('payment_receipt');
            $table->integer('total_payment');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
