<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camera extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'slug',
        'name',
        'ip_address',
        'protocol',
        'manufacturer',
        'stream_url',
        'username',
        'password',
        'port',
        'location',
        'longitude',
        'latitude',
        'status',
        'business_id',
        'location_id',

        'createdby_id',
        'updatedby_id',
        'deletedby_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $hidden = [
        'createdby_id',
        'updatedby_id',
        'deletedby_id',
        'deleted_at'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
