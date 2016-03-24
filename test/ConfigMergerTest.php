<?php

namespace Czeeb;

use Czeeb\ConfigMerger\Exception\FileNotFound;

use org\bovigo\vfs\vfsStream;
use Symfony\Component\Yaml\Yaml;

class ConfigMergerTest extends \PHPUnit_Framework_TestCase
{
    private $root;

    private $config_file;

    private $config_contents = array (
        'colour' => 'green',
        'season' => 'autumn',
        'files' => array (
            array (
                'file' => '/tmp/foo.txt',
                'type' => 'foofile',
            ),
        ),
    );

    private $config_local_contents = array (
        'colour' => 'blue',
        'country' => 'Canada',
        'files' => array (
            array (
                'file' => '/tmp/bar.txt',
                'type' => 'barfile',
            ),
        ),
    );

    private $config_contents_merged = array (
        'colour' => 'blue',
        'season' => 'autumn',
        'country' => 'Canada',
        'files' => array (
            array (
                'file' => '/tmp/foo.txt',
                'type' => 'foofile',
            ),
            array (
                'file' => '/tmp/bar.txt',
                'type' => 'barfile',
            ),
        ),
    );

    public function setUp()
    {
        $this->root = vfsStream::setup('root');

        $this->config_file = vfsStream::newFile('config.yml')->at($this->root);
        $this->config_file->setContent(Yaml::dump(
            $this->config_contents,
            ConfigMerger::YAML_INLINE,
            ConfigMerger::YAML_INDENT
        ));
    }

    public function testAddConfigExceptionThrownIfConfigDoesNotExist()
    {
        $this->expectException(FileNotFound::class);

        $a = new ConfigMerger();
        $a->addConfig('vfs://root/doesnotexist.yml');
    }

    public function testAddConfigConfigFileParsedWithoutExceptions()
    {
        $a = new ConfigMerger();
        $a->addConfig($this->root->getChild('root/config.yml')->url());
    }

    public function testAddConfigNoExceptionThrownIfLocalConfigDoesNotExist()
    {
        $a = new ConfigMerger();
        $a->addConfig($this->root->getChild('root/config.yml')->url(), true);
    }

    public function testGetConfigArray()
    {
        $a = new ConfigMerger();
        $a->addConfig($this->root->getChild('root/config.yml')->url());
        $config = $a->getConfigArray();

        $this->assertEquals($this->config_contents, $config);
    }

    public function testGetConfigYaml()
    {
        $a = new ConfigMerger();
        $a->addConfig($this->root->getChild('root/config.yml')->url());

        $config = $a->getConfigYaml();
        $this->assertEquals(Yaml::dump(
            $this->config_contents,
            ConfigMerger::YAML_INLINE,
            ConfigMerger::YAML_INDENT
        ), $config);
    }

    public function testGetConfigJson()
    {
        $a = new ConfigMerger();
        $a->addConfig($this->root->getChild('root/config.yml')->url());

        $config = $a->getConfigJson();
        $this->assertEquals(json_encode($this->config_contents), $config);
    }

    public function testConfigWithLocalCorrectlyMerged()
    {
        $this->config_file = vfsStream::newFile('config.local.yml')->at($this->root);
        $this->config_file->setContent(Yaml::dump(
            $this->config_local_contents,
            ConfigMerger::YAML_INLINE,
            ConfigMerger::YAML_INDENT
        ));

        $a = new ConfigMerger();
        $a->addConfig($this->root->getChild('root/config.yml')->url(), true);

        $config = $a->getConfigArray();

        $this->assertEquals($this->config_contents_merged, $config);
    }
}
