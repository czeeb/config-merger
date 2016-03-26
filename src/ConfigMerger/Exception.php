<?php

namespace Czeeb\ConfigMerger;

abstract class Exception extends \RuntimeException
{
    /**
     * The default exception message that will be used if none is provided
     *
     * @var
     */
    const MESSAGE = '';

    /**
     * Constructor
     *
     * @param string $message
     * @param int $code
     * @param \Exception $previous (Optional)
     */

    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        parent::__construct(
            $message = static::MESSAGE,
            $code,
            $previous
        );
    }
}
