<?php
/**
 * This file is part of mocks-drupal-variables.
 *
 * @file bootstrap.php
 *
 * R. Eric Wheeler <reric@ee.stanford.edu>
 *
 * 8/5/16 / 10:19 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sikofitt\Mocks\Drupal\Variables;

define('CONFIG_PATH', __DIR__ . '/storage');

$config = new Variables([]);

if(!function_exists('variable_get'))
{
  function variable_get($variableName, $default = null)
  {
    $config = new Variables([]);
    return $config->variable_get($variableName, $default);

  }
}

if(!function_exists('variable_set'))
{
  function variable_set($variableName, $variableValue)
  {
    $config = new Variables([]);
    return $config->variable_set($variableName, $variableValue);
  };

}

if(!function_exists('variable_del'))
{
  function variable_del($variableName)
  {
    $config = new Variables([]);
    return $config->variable_del($variableName);
  }
}