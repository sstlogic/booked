<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

abstract class ExceptionHandler
{
    /**
     * @var ExceptionHandler $handler
     */
    private static $handler;

    public static function SetExceptionHandler(ExceptionHandler $handler)
    {
        self::$handler = $handler;
    }

    public abstract function HandleException($exception);

    public static function Handle($exception)
    {
//        die($exception->getMessage());
//        die($exception->getTraceAsString());
//        die(join('\n', $exception->getTrace()));

        Log::Error('Uncaught exception without stack trace', ['exception' => $exception]);

        if (isset(self::$handler)) {
            self::$handler->HandleException($exception);
        }
    }
}

class WebExceptionHandler extends ExceptionHandler
{
    /**
     * @var callback
     */
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function HandleException($exception)
    {
        call_user_func($this->callback);
    }
}

set_exception_handler(['ExceptionHandler', 'Handle']);
