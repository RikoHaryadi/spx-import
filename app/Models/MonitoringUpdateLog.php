<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringUpdateLog extends Model
{
    protected $table = 'monitoring_update_logs';

    protected $fillable = [

        'order_id',

        'driver_id',
        'driver_name',

        'status',

        'received_time',
        'current_station_received_time',
        'delivering_time',
        'delivered_time',

        'on_hold_time',
        'on_hold_reason',

        'reschedule_date',

        'order_account',
        'payment_method',

        'current_station',

        'operation_date',

        'data_source',

        'imported_at',
    ];
}