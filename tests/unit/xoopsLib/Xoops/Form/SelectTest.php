<?php
namespace Xoops\Form;

require_once(dirname(__FILE__).'/../../../init_new.php');

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-08-18 at 21:59:25.
 */

/**
 * PHPUnit special settings :
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */

class SelectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Select
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Select('Caption', 'name', 'value');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Xoops\Form\Select::isMultiple
     */
    public function testIsMultiple()
    {
        $value = $this->object->isMultiple();
        $this->assertFalse($value);

        $aSelect = new Select('Caption', 'name', 'value', 1, true);
        $value = $aSelect->isMultiple();
        $this->assertTrue($value);
    }

    /**
     * @covers Xoops\Form\Select::getSize
     */
    public function testGetSize()
    {
        $value = $this->object->getSize();
        $this->assertSame(1, $value);
    }

    /**
     * @covers Xoops\Form\Select::addOption
     * @covers Xoops\Form\Select::getOptions
     */
    public function testAddOption()
    {
        $this->object->addOption('opt_key', 'opt_name');
        $this->object->addOption('opt_just_key');
        $value = $this->object->getOptions();
        $this->assertTrue(is_array($value));
        $this->assertSame('opt_name', $value['opt_key']);
        $this->assertSame('opt_just_key', $value['opt_just_key']);
    }

    /**
     * @covers Xoops\Form\Select::addOptionArray
     * @covers Xoops\Form\Select::getOptions
     */
    public function testAddOptionArray()
    {
        $this->object->addOptionArray(array('opt_key' => 'opt_name', 'opt_just_key' => null));
        $value = $this->object->getOptions();
        $this->assertTrue(is_array($value));
        $this->assertSame('opt_name', $value['opt_key']);
        $this->assertSame('opt_just_key', $value['opt_just_key']);
    }

    /**
     * @covers Xoops\Form\Select::addOptionGroup
     */
    public function testAddOptionGroup()
    {
        $groups = array('grp_key' => 'grp_name', 'grp_key1' => 'grp_name1');
        $this->object->addOptionGroup('opt_grp_name', $groups);
        $value = $this->object->get('option');
        $this->assertTrue(is_array($value));
        $this->assertSame($groups, $value['opt_grp_name']);
    }

    /**
     * @covers Xoops\Form\Select::render
     * @covers Xoops\Form\Select::renderOption
     * @covers Xoops\Form\Element::themeDecorateElement
     */
    public function testRender()
    {
        $this->object->addOptionArray(array('opt_key' => 'opt_name', 'opt_just_key' => null));
        $this->object->setValue('opt_key');
        $value = $this->object->render();
        $this->assertTrue(is_string($value));
        $this->assertTrue(false !== strpos($value, '<select'));
        $this->assertTrue(false !== strpos($value, 'name="name"'));
        $this->assertTrue(false !== strpos($value, 'size="1"'));
        $this->assertTrue(false !== strpos($value, 'title="Caption"'));
        $this->assertTrue(false !== strpos($value, 'id="name"'));
        $this->assertTrue(false !== strpos($value, '<option'));
        $this->assertTrue(false !== strpos($value, 'value="opt_key"'));
        $this->assertTrue(false !== strpos($value, '>opt_name</option>'));
        $this->assertTrue(false !== strpos($value, 'value="opt_just_key"'));
        $this->assertTrue(false !== strpos($value, '>opt_just_key</option>'));

        $this->object = new Select('Caption', 'name', 'value'); // reset object
        $groups = array('grp_key' => 'grp_name', 'grp_key1' => 'grp_name1');
        $this->object->addOptionGroup('opt_grp_name', $groups);
        $value = $this->object->render();
        $this->assertTrue(is_string($value));
        $this->assertTrue(false !== strpos($value, '<select'));
        $this->assertTrue(false !== strpos($value, 'name="name"'));
        $this->assertTrue(false !== strpos($value, 'size="1"'));
        $this->assertTrue(false !== strpos($value, 'title="Caption"'));
        $this->assertTrue(false !== strpos($value, 'id="name"'));
        $this->assertTrue(false !== strpos($value, '<optgroup'));
        $this->assertTrue(false !== strpos($value, 'label="opt_grp_name"'));
        $this->assertTrue(false !== strpos($value, '<option'));
        $this->assertTrue(false !== strpos($value, 'value="grp_key"'));
        $this->assertTrue(false !== strpos($value, '>grp_name</option>'));
        $this->assertTrue(false !== strpos($value, 'value="grp_key1"'));
        $this->assertTrue(false !== strpos($value, '>grp_name1</option>'));

    }

    /**
     * @covers Xoops\Form\Select::renderValidationJS
     */
    public function testRenderValidationJS()
    {
        $value = $this->object->renderValidationJS();
        $this->assertSame("",$value);

        $this->object->setRequired(true);
        $value = $this->object->renderValidationJS();
        $this->assertTrue(is_string($value));
        $this->assertTrue(false !== strpos($value, 'window.alert'));
    }

    /**
     * @covers Xoops\Form\Select::__construct
     * @covers Xoops\Form\Select::render
     * @covers Xoops\Form\Select::renderValidationJS
     */
    public function test__construct()
    {
        $oldWay = new Select('mycaption', 'myname', 'opt1');
        $oldWay->addOption('opt1', 'optname1');
        $oldWay->addOption('opt2', 'optname2');
        $newWay = new Select([
            'caption' => 'mycaption',
            'name' => 'myname',
            'value' => 'opt1',
            'option' => [
                'opt1' => 'optname1',
                'opt2' => 'optname2',
            ]
        ]);
        $this->assertEquals($oldWay->render(), $newWay->render());
        $this->assertEquals($oldWay->renderValidationJS(), $newWay->renderValidationJS());
    }
}
