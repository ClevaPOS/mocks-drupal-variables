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

/**
 * Class Variables
 *
 * @package Sikofitt\Mocks\Drupal
 */
class Variables extends \ArrayObject
{

  /**
   *
   */
  const VARIABLE_NAMESPACE = '/sikofitt/mocks/drupal/';

  /**
   *
   */
  const VARIABLE_TEMP_FILE_NAME = 'config.tmp';

  /**
   * @var
   */
  private $config;

  /**
   * Variables constructor.
   */
  public function __construct($data = null)
    {
        if (!$this->tempDirectoryExists()) {
            $this->createTempDirectory();
        }
        if (!$this->tempFileExists()) {
            $this->touchConfigFile();
        }
        if($data !== null && (is_array($data) || is_object($data)))
        {
          $this->exchangeArray($data);
          $this->writeConfig();
        }
        $this->parseConfig();
        parent::__construct($this->config);
    }

  /**
   * @param      $variableName
   * @param null $default
   *
   * @return mixed|null
   */
  public function variable_get($variableName, $default = null)
  {
    if ($this->offsetExists($variableName)) {
      return unserialize($this->offsetGet($variableName));
    } else {
      return $default;
    }
  }

  /**
   * @param $variableName
   * @param $variableValue
   *
   * @return null
   */
  public function variable_set($variableName, $variableValue)
  {
    $this->offsetSet($variableName, serialize($variableValue));
    $this->writeConfig();
    return null;
  }

  /**
   * @param $variableName
   *
   * @return null
   */
  public function variable_del($variableName)
  {
    if ($this->offsetExists($variableName)) {
      $this->offsetUnset($variableName);
      $this->writeConfig();
    }
    return null;
  }

  /**
   * @return bool
   */
  public function tempDirectoryExists()
    {
        return file_exists(sys_get_temp_dir().self::VARIABLE_NAMESPACE);
    }

  /**
   * @return bool
   */
  private function tempFileExists()
    {
        return file_exists(
      sys_get_temp_dir().self::VARIABLE_NAMESPACE.self::VARIABLE_TEMP_FILE_NAME);
    }

  /**
   * @return void
   */
  private function createTempDirectory()
    {
        try {
            mkdir(sys_get_temp_dir().self::VARIABLE_NAMESPACE, 0775, true);
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }

  /**
   * @return void
   */
  private function touchConfigFile()
    {
        try {
            touch(sys_get_temp_dir().self::VARIABLE_NAMESPACE.self::VARIABLE_TEMP_FILE_NAME);
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }

  /**
   * @return void
   */
  private function parseConfig()
    {
        if (null === $this->config = json_decode(
        file_get_contents(
          sys_get_temp_dir().self::VARIABLE_NAMESPACE.self::VARIABLE_TEMP_FILE_NAME))
    ) {
            $this->config = array();
        }
    }

  /**
   * @return void
   */
  private function writeConfig()
    {
        $data = json_encode($this->getArrayCopy());
        try {
            file_put_contents(
        sys_get_temp_dir().self::VARIABLE_NAMESPACE.self::VARIABLE_TEMP_FILE_NAME,
        $data);
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }
}
