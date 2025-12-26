<?php

namespace App\Traits;

use App\AOP\LoggingAspect;

trait Loggable
{
    public function callWithLogging($method, ...$args)
    {
        $class = get_class($this);
        $start = microtime(true);

        LoggingAspect::logBefore($class, $method, ["action" => $method]);

        $result = $this->$method(...$args);
        $time = round((microtime(true) - $start) * 1000, 2);

        LoggingAspect::logAfter($class, $method, null, $time);

        return $result;
    }
}
