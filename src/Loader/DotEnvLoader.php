<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 11/22/16
 * Time: 9:44 AM
 */

namespace Configula\Loader;

use Configula\ConfigValues;
use Dflydev\DotAccessData\Data;
use Dotenv\Dotenv;

/**
 * Class DotEnvLoader
 * @package FandF\Config
 */
class DotEnvLoader
{
    /**
     * @var string
     */
    private $basePath;

    /**
     * @var bool
     */
    private $overload;

    /**
     * @var string
     */
    private $prefix;

    /**
     * DotEnvLoader constructor.
     *
     * @param string $basePath
     * @param string $prefix Prefix for environment variables (ignore all others)
     * @param bool $overload If TRUE, will cause .env values to override existing environment values if both exist
     */
    public function __construct(string $basePath, string $prefix = '', bool $overload = false)
    {
        $this->basePath = $basePath;
        $this->overload = $overload;
        $this->prefix   = $prefix;
    }

    /**
     * Load configuration
     */
    public function load(): ConfigValues
    {
        $dotEnv = new Dotenv($this->basePath);
        $this->overload ? $dotEnv->overload() : $dotEnv->load();

        $configValues = new Data();

        foreach ($_ENV as $valName => $valVal) {

            if ($this->prefix && $this->prefix != substr($valName, 0, strlen($this->prefix))) {
                continue;
            }

            $valPath = strtolower(str_replace('_', '.', substr($valName, strlen($this->prefix))));
            $configValues->set($valPath, $this->prepareVal($valVal));
        }

        return new ConfigValues($configValues->export());
    }

    /**
     * Prepare string value
     *
     * @param mixed $value
     * @return mixed
     */
    private function prepareVal($value)
    {
        if (is_string($value)) {
            switch (strtolower($value)) {
                case 'null':
                    return null;
                case 'false':
                    return false;
                case 'true':
                    return true;
                default:
                    return $value;
            }
        }
        else {
            return $value;
        }
    }
}