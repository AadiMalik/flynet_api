<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'domain',
        'owner',
        'email',
        'phone',
        'address',
        'website',
        'logo',
        'subscription_package_id',
        'subscription_status',
        'subscription_end_date',
        'is_active',
        'createdby_id',
        'updatedby_id',
        'deletedby_id',
    ];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/app/public/' .  $this->logo);
        }

        return null;
    }
    public function subscription_package()
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }
}
