<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringSummary extends Model
{
      protected $table = 'monitoring_summary';

    protected $fillable = [

        'hub_id',

        'operation_date',

        'driver_id',

        'driver_name',

        'total',

        'delivered',

        'onhold',

        'remaining',

        'progress',

        'status_label',

        'status_color',

    ];
}