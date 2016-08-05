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
use Noodlehaus\ConfigInterface;
use Symfony\Component\Yaml\Yaml;
use werx\Config\Container;
use werx\Config\Providers\JsonProvider;


/**
 * Class Variables
 *
 * @package Sikofitt\Mocks\Drupal
 */
class Variables extends AbstractConfig {

  /**
   * @param $data
   *
   * @return $this
   */
  public function setData($data) {
    $this->data = $data;
    return $this;
  }

  /**
   * @return array|null
   */
  public function getData() {
    return $this->data;
  }

  private function writeConfig($values)
  {
      file_put_contents(CONFIG_PATH . '/config.json', $values);

  }

  private function getConfig()
  {
    $config = new Config(CONFIG_PATH . '/config.json');

    $this->setData($config->all());
  }

  public function variable_set($variableName, $variableValue)
  {
    $this->getConfig();
    $this->set($variableName, serialize($variableValue));
     $this->writeConfig(json_encode($this->all(), JSON_PRETTY_PRINT));
  }


  public function variable_get($variableName, $default = null)
  {
    $this->getConfig();
    $variableValue = $this->get($variableName, $default);

    return unserialize($variableValue);
  }

  public function variable_del($variableName)
  {

    $this->getConfig();
    if($this->offsetExists($variableName))
    {
      unset($this->data[$variableName]);
    }
    $this->writeConfig(json_encode($this->all(), JSON_PRETTY_PRINT));
  }

  private function is_serialized( $data ) {
    // if it isn't a string, it isn't serialized
    if ( !is_string( $data ) )
      return false;
    $data = trim( $data );
    if ( 'N;' == $data )
      return true;
    if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
      return false;
    switch ( $badions[1] ) {
      case 'a' :
      case 'O' :
      case 's' :
        if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
          return true;
        break;
      case 'b' :
      case 'i' :
      case 'd' :
        if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
          return true;
        break;
    }
    return false;
  }
}