<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'payment_method_id',
        'package_id',
        'student_name',
        'student_email',
        'student_phone',
        'school_name',
        'is_valid',
        'payment_receipt',
        'city_id',
        'district_id',
        'subdistrict_id',
        'postal_code',
        'address',
    ];

    protected $casts = [
        'is_valid' => 'boolean',
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function subdistrict()
    {
        return $this->belongsTo(Subdistrict::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
}
