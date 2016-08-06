<?php
/**
 * This file is part of mocks-drupal-variables.
 *
 * @file Variables.php
 *
 * R. Eric Wheeler <reric@ee.stanford.edu>
 *
 * 8/5/16 / 10:15 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sikofitt\Mocks\Drupal;

use Noodlehaus\AbstractConfig;
use Noodlehaus\Config;
use Noodlehaus\Exception\EmptyDirectoryException;
use SebastianBergmann\CodeCoverage\Report\Html\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;


/**
 * Class Variables
 *
 * @package Sikofitt\Mocks\Drupal
 */
class Variables extends AbstractConfig
{
    const VARIABLE_TEMP_DIR = '/sikofitt/mocks/drupal';
    const VARIABLE_NAMESPACE = '/sikofitt/mocks/drupal';

    public function tempDirectoryExists()
    {

        return file_exists(sys_get_temp_dir() . self::VARIABLE_TEMP_DIR);

    }

    private function tempFileExists()
    {
        return file_exists(sys_get_temp_dir() . self::VARIABLE_TEMP_DIR . '/config.yml');
    }

    private function createTempDirectory()
    {
        mkdir(sys_get_temp_dir() . '/sikofitt/mocks/drupal', 0775, true);

    }

    private function touchConfigFile()
    {
        touch(sys_get_temp_dir() . self::VARIABLE_TEMP_DIR . '/config.yml');
    }

    private function parseConfig()
    {
        if (null === $config = Yaml::parse(file_get_contents(sys_get_temp_dir() . self::VARIABLE_TEMP_DIR . '/config.yml'))) {
            $config = [];
        }
        return $config;
    }

    public function __construct()
    {
        if (!$this->tempDirectoryExists()) {
            $this->createTempDirectory();
        }
        if (!$this->tempFileExists()) {
            $this->touchConfigFile();
        }
        $config = $this->parseConfig();


        parent::__construct($config);
    }

    /**
     * @return array|null
     */
    public function getData()
    {
        return $this->data;
    }

    public function variable_set($variableName, $variableValue)
    {
        $this->set($variableName, serialize($variableValue));
        $this->writeConfig();

    }

    private function cleanTempDirectory()
    {
        $tempDirectory = new Filesystem();
        $tempDirectory->remove(sys_get_temp_dir() . '/sikofitt');

        return false === $tempDirectory->exists(sys_get_temp_dir() . '/sikofitt');
    }

    private function makeTempDirectory()
    {
        $tempDirectory = new Filesystem();
        $tempDirectory->mkdir(sys_get_temp_dir() . self::VARIABLE_NAMESPACE);
        return $tempDirectory->exists(sys_get_temp_dir() . self::VARIABLE_TEMP_DIR);
    }

    private function writeConfig()
    {
        $yaml = Yaml::dump($this->all());
        file_put_contents(sys_get_temp_dir() . self::VARIABLE_TEMP_DIR . '/config.yml', $yaml);


    }

    public function variable_get($variableName, $default = null)
    {

        $variableValue = $this->get($variableName, $default);
        return unserialize($variableValue);
    }

    public function variable_del($variableName)
    {

        if ($this->offsetExists($variableName)) {
            unset($this->data[$variableName]);
        }
        $this->writeConfig();
    }

    private function resetConfig()
    {
        $config = new Config(CONFIG_PATH);
        $this->setData($config->all());
    }

    /**
     * @param $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    private function is_serialized($data)
    {
        // if it isn't a string, it isn't serialized
        if (!is_string($data))
            return false;
        $data = trim($data);
        if ('N;' == $data)
            return true;
        if (!preg_match('/^([adObis]):/', $data, $badions))
            return false;
        switch ($badions[1]) {
            case 'a' :
            case 'O' :
            case 's' :
                if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
                    return true;
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
                    return true;
                break;
        }
        return false;
    }
}