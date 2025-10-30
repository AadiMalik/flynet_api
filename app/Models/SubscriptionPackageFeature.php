<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackageFeature extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'createdby_id',
        'updatedby_id',
        'deletedby_id',
    ];
    public function packages()
    {
        return $this->belongsToMany(
            SubscriptionPackage::class,
            'package_feature',
            'feature_id',
            'package_id'
        );
    }
}
