<?php

namespace Czeeb\ConfigMerger\Exception;

use Czeeb\ConfigMerger\Exception;

class FileNotFound extends Exception
{
    /**
     * The default exception message that will be used if none provided
     *
     * @var string
     */
    const MESSAGE = 'File not found';

    /**
     * Constructor
     *
     * @param string $filename
     */
    public function __construct($filename, $code = 0, Exception $previous = null)
    {
        $message = sprintf('File not found: %s', $filename);

        parent::__construct($message, $code, $previous);
    }
}
