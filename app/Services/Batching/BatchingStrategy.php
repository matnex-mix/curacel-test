<?php

namespace App\Services\Batching;

use App\Models\Batch;
use App\Models\Order;

interface BatchingStrategy
{

    /**
     * @param Order $order
     * @return Batch
     * @throws BatchingError
     */
    public function getBatch(Order $order) : Batch;

}
