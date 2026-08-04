<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringTracking extends Model
{
    protected $table = 'monitoring_tracking';

    protected $fillable = [

        'order_id',

        'driver_id',
        'driver_name',

        'hub_id',

        'received_time',
        'current_station_received_time',
        'delivering_time',
        'delivered_time',

        'on_hold_time',
        'on_hold_reason',

        'reschedule_date',

        'status',

        'operation_date',

        'order_account',
        'payment_method',

        'current_station',

    ];
}