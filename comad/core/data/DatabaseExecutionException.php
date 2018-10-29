<?php

/**
 * A custom exception in case an error occurs during a database request.
 *
 * Class DatabaseExecutionException
 */
class DatabaseExecutionException extends Exception
{

    /**
     * DatabaseExecutionException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('internal database execution error', $code, $previous);
    }

}