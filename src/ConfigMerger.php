<?php
namespace Czeeb;

use Czeeb\ConfigMerger\Exception\FileNotFound;

use Symfony\Component\Yaml\Yaml;
use Zend\Stdlib;

class ConfigMerger
{
    /**
     * The level where you switch to inline YAML
     * @var int
     */
    const YAML_INLINE = 0;

    /**
     * The amount of spaces to use for indentation of nested nodes.
     * @var int
     */
    const YAML_INDENT = 4;

    /**
     * True if an exception must be thrown on invalid types false otherwise
     * @var boolean
     */
    const YAML_EXCEPTION = true;

    private $config;

    public function __construct()
    {
        $this->config = array();
    }

    /**
     * Adds a config to be merged with the existing config.
     *
     * @param string $config_filename Filename of config file to be merged into the existing config.
     * @param boolean $local If ConfigMerger should automatically look for and merge in a local version
     *     of the config as well
     *
     * @return void
     */
    public function addConfig($config_filename, $local = false)
    {
        $new_config = $this->readConfigFile($config_filename);

        $this->config = \Zend\Stdlib\ArrayUtils::merge($this->getConfigArray(), $new_config);

        if ($local) {
            $path_parts = pathinfo($config_filename);

            $local_config_filename = $path_parts['dirname'] . '/' .
                                     $path_parts['filename'] .
                                     '.local.' .
                                     $path_parts['extension'];

            try {
                $this->addConfig($local_config_filename);
            } catch (FileNotFound $e) {
                // We do nothing, it is acceptable for the local version not to exist.
            }
        }
    }

    /**
     * Returns config as array.
     *
     * @return array
     */
    public function getConfigArray()
    {
        return $this->config;
    }

    /**
     * Returns config as YAML.
     *
     * @return string Formatted as YAML.
     */
    public function getConfigYaml()
    {
        return Yaml::dump($this->config, self::YAML_INLINE, self::YAML_INDENT, self::YAML_EXCEPTION);
    }

    /**
     * Returns config as JSON.
     *
     * @return string Formatted as JSON.
     */
    public function getConfigJson()
    {
        return json_encode($this->config);
    }

    /**
     * Reads config file
     *
     * @param string $config_filename
     *
     * @return void
     *
     * @throws FileNotFound
     */
    private static function readConfigFile($config_filename)
    {
        if (!file_exists($config_filename)) {
            throw new FileNotFound($config_filename);
        }

        $config = Yaml::parse(file_get_contents($config_filename), self::YAML_EXCEPTION);

        return $config;
    }
}
