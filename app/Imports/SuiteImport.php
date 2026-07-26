<?php

namespace App\Imports;

use App\Models\SuiteData;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;


class SuiteImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts
{
    public function model(array $row)
{
    // Skip jika shipment_id kosong
     
    if (empty($row['shipment_id'])) {
        return null;
    }

    return new SuiteData([

        'date_id' => $row['date_id'] ?? null,

        'shipment_id' => $row['shipment_id'] ?? null,

        'lmhub_station_name' => $row['lmhub_station_name'] ?? null,

        'inbound_group' => $row['inbound_group'] ?? null,

        'delivered_time' => $this->emptyToNull($row['delivered_time'] ?? null),

        'transported_time' => $this->emptyToNull($row['transported_time'] ?? null),

        'assigned_delivering_time' => $this->emptyToNull($row['assigned_delivering_time'] ?? null),

        'on_hold_count' => $row['on_hold_count'] ?? 0,

        'assigned_time' => $this->emptyToNull($row['assigned_time'] ?? null),

        'last_on_hold_timestamp' => $this->emptyToNull($row['last_on_hold_timestamp'] ?? null),

        'addr_zone_name' => $row['addr_zone_name'] ?? null,

        'driver_id' => $row['driver_id'] ?? null,

'within_cutoff_delivered' =>
    $this->yesNoToBool($row['within_cutoff_delivered'] ?? null),

'within_cutoff_assigned' =>
    $this->yesNoToBool($row['within_cutoff_assigned'] ?? null),

'within_assigned_delivering' =>
    $this->yesNoToBool($row['within_assigned_delivering'] ?? null),

'is_lmhub_delivery_transfer' =>
    $this->yesNoToBool($row['is_lmhub_delivery_transfer'] ?? null),

        'status' => $row['status'] ?? null,
    ]);
}

    private function emptyToNull($value)
    {
        
        $value = trim((string)$value);

        if ($value === '') {
            return null;
        }

        return $value;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
   private function yesNoToBool($value)
{
    $value = strtolower(trim((string)$value));

    if ($value === 'yes') {
        return 1;
    }

    if ($value === 'no') {
        return 0;
    }

    if ($value === '1') {
        return 1;
    }

    if ($value === '0') {
        return 0;
    }

    return null;
}
}
