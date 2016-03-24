<?php
namespace Czeeb;

use Zend\Stdlib;

class ConfigMerger
{
    public function __construct()
    {
        $this->config = array();
    }

    public function addConfig($config_filename, $local = false)
    {
        $new_config = $this->readconfigFile($config_filename);

        $this->config = \Zend\Stdlib\ArrayUtils::merge($this->getConfig(), $new_config);
    }

    public function getConfig($type = 'array')
    {
        switch($type) {
            case "array":
                return $this->config;
                break;
            case "yml":
                return yaml_emit($this->config);
                break;
            case "yaml":
                return yaml_emit($this->config);
                break;
            case "json":
                return json_encode($this->config);
                break;
            default:
                throw new \Exception("$type is unsupported");
        }
    }

    private static function readConfigFile($config_filename)
    {
        if (!file_exists($config_filename)) {
            throw new \Exception('Config filename does not exist on the filesystem: ' . $config);
        }

        $config = yaml_parse_file($config_filename);

        if (!$config) {
            throw new \Exception('Something went horribly wrong parsing the config file: ' . $config_filename);
        }

        return $config;
    }
}
