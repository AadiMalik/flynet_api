<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_type',
        'is_active',
        'createdby_id',
        'updatedby_id',
        'deletedby_id',
    ];

    public function features()
    {
        return $this->belongsToMany(
            SubscriptionPackageFeature::class,  // related model
            'package_feature',                  // pivot table
            'package_id',                       // current model key in pivot
            'feature_id'                        // related model key in pivot
        );
    }
}
