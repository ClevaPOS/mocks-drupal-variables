<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 08/05/2016
 * Time: 5:47 PM.
 */
namespace Sikofitt\Tests\Mocks\Drupal;

use Sikofitt\Mocks\Drupal\Variables;

class VariablesTest extends \PHPUnit_Framework_TestCase
{
    private $object;
    private $keyedArrayData;
    private $variables;

    public function setUp()
    {
        $this->object = new \stdClass();
        $this->object->objectVariableName1 = 'objectVariableValue1';
        $this->object->objectVariableName2 = 'objectVariableValue2';
        $this->object->objectVariableName3 = 'objectVariableValue3';

        $this->keyedArrayData = array(
        'subVariableName1' => 'subVariableValue1',
        'subVariableName2' => 'subVariableValue2',
        'subVariableName3' => 'subVariableValue3',
      );
        $testData = array(
        'variableName1' => serialize('variableValue1'),
        'variableName2' => serialize('variableValue2'),
        'variableName3' => serialize(range(0, 20)),
        'variableName4' => serialize($this->keyedArrayData),
        'variableObjectName1' => serialize($this->object),
      );
        $this->variables = new Variables($testData);
    }

    /**
     * @covers Sikofitt\Mocks\Drupal\Variables::variable_get
     */
    public function testVariableGet()
    {
        $this->assertSame('variableValue1', variable_get('variableName1'));
        $this->assertSame('variableValue2', variable_get('variableName2'));
        $this->assertSame(range(0, 20), variable_get('variableName3'));
        $this->assertNotSame(range(1, 20), variable_get('variableName3'));
        $this->assertSame($this->keyedArrayData, variable_get('variableName4'));
        $this->assertArrayHasKey('subVariableName1', variable_get('variableName4'));
        $this->assertArraySubset($this->keyedArrayData, variable_get('variableName4'));
        $this->assertEquals($this->object, variable_get('variableObjectName1'));
        $this->assertNotSame('variableValue1', array());
        $this->assertSame(0, variable_get('thisVariableDoesntExist', 0));
        $this->assertNull(variable_get('thisVariableDoesntExist'));
    }

    /**
     * @covers Sikofitt\Mocks\Drupal\Variables::variable_set
     */
    public function testVariableSet()
    {
        $this->assertNull(variable_set('variableName5', 'variableValue5'));
        variable_set('variableName6', 'variableValue6');
        $this->assertSame('variableValue6', variable_get('variableName6'));
        variable_set('variableName7', $this->object);
        $this->assertEquals($this->object, variable_get('variableName7'));
    }

    /**
     * @covers Sikofitt\Mocks\Drupal\Variables::variable_del
     */
    public function testVariableDel()
    {
      $this->assertNull(variable_del('variableName7'));
      $this->assertNull(variable_get('variableName7'));
      variable_del('variableName6');
      $this->assertSame(0, variable_get('variableName6', 0));
      $this->assertSame('variableValue1', variable_get('variableName1'));
      variable_del('variableName1');
      variable_set('variableName1', 'variableDelTest1');
      $this->assertSame('variableDelTest1', variable_get('variableName1'));

    }

    /**
     * @covers Sikofitt\Mocks\Drupal\Variables::createTempDirectory
     * @covers Sikofitt\Mocks\Drupal\Variables::touchConfigFile
     * @covers Sikofitt\Mocks\Drupal\Variables::parseConfig
     * @covers Sikofitt\Mocks\Drupal\Variables::writeConfig
     */
    public function testTempFile()
    {
        $this->assertFileExists(sys_get_temp_dir().Variables::VARIABLE_NAMESPACE.Variables::VARIABLE_TEMP_FILE_NAME);
        $this->assertFileEquals(sys_get_temp_dir().'/sikofitt/mocks/drupal/config.tmp', sys_get_temp_dir().Variables::VARIABLE_NAMESPACE.Variables::VARIABLE_TEMP_FILE_NAME);
        $this->assertJsonStringEqualsJsonFile(
          sys_get_temp_dir().Variables::VARIABLE_NAMESPACE.Variables::VARIABLE_TEMP_FILE_NAME,
          json_encode($this->variables->getArrayCopy())
      );
    }
}
