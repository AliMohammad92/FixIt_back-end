<?php

namespace App\AOP;

use Illuminate\Support\Facades\Log;

class LoggingAspect
{
    public static function logBefore($class, $method, $args)
    {
        Log::info("Entering $class::$method", ['args' => $args]);
    }

    public static function logAfter($class, $method, $result, $time)
    {
        Log::info("Exiting $class::$method", [
            'result' => $result,
            'execution_time_ms' => $time
        ]);
    }
}
