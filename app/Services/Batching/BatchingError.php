<?php

namespace App\Services\Batching;

use Exception;
use Throwable;

class BatchingError extends Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null) {
        $message = "BATCHING ERROR: $message";

        parent::__construct($message, $code, $previous);
    }
}
