<?php


namespace Configula\fixtures;

use Configula\Loader\FileLoader;
use Error;

class ErrorTriggeringFileLoader extends FileLoader
{

    /**
     * Parse the contents
     *
     * @param  string $rawFileContents
     * @return array
     */
    protected function parse(string $rawFileContents): array
    {
        throw new Error('This is a test');
    }
}
