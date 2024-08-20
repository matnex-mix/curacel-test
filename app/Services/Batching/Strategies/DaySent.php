<?php

namespace App\Services\Batching\Strategies;

use App\Models\Batch;
use App\Models\Order;
use App\Services\Batching\BatchingStrategy;

class DaySent implements BatchingStrategy
{

    public static $name = 'day_sent';

    public function getBatch(Order $order): Batch
    {
        // Compute the batch name
        $batchDate = $order->created_at->format('M Y');
        $batchName = ucwords($order->provider_name ." ". $batchDate);

        // try fetching the batch
        $batch = Batch::where('name', $batchName)
            ->where('hmo_id', $order->hmo_id)
            ->first();

        // create one if it doesn't exist
        if( !$batch ){
            $batch = Batch::create([
                'name' => $batchName,
                'batching_strategy' => self::$name,
                'hmo_id' => $order->hmo_id
            ]);
        }

        return $batch;
    }
}
