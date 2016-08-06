<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 08/05/2016
 * Time: 5:47 PM
 */

namespace Sikofitt\Tests\Mocks\Drupal;


class VariablesTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function testVariableGet()
    {
        variable_set('test', 'variable');
        $this->assertTrue(true);
    }
}
