<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    protected $fillable = [

        'hub_code',
        'hub_name',
        'city',
        'region',
        'is_active'

    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function hub()
{
    return $this->belongsTo(Hub::class);
}
}
