<?php

namespace Acreage\Tests\Fields;

use Acreage\Fields\Checkbox;

class CheckboxTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers Acreage\Fields\Checkbox::to_array
   */
  public function test_to_array_with_no_default() {
    $subject = new Checkbox( array(), 'name', 'checkbox',
                          array( 'boxes' => array(
                            'box1' => 'First Checkbox',
                            'box2' => 'Second Checkbox'
                          ), 'label' => 'Checkbox Field' ) );

    $this->assertSame( array( 'name' => 'name',
                              'type' => 'checkbox',
                              'value' => array(),
                              'label' => 'Checkbox Field',
                              'valid' => null,
                              'validated' => false,
                              'errors' => array(),
                              'boxes' => array(
                                array( 'name'    => 'name[]',
                                       'id'      => 'name_0',
                                       'value'   => 'box1',
                                       'checked' => false,
                                       'label'   => 'First Checkbox' ),
                                array( 'name'    => 'name[]',
                                       'id'      => 'name_1',
                                       'value'   => 'box2',
                                       'checked' => false,
                                       'label'   => 'Second Checkbox' )
                        )),
                        $subject->to_array() );
  }

  /**
   * @covers Acreage\Fields\Checkbox::to_array
   */
  public function test_to_array_with_default_as_string() {
    $subject = new Checkbox( array(), 'name', 'checkbox',
                          array( 'default' => 'box2',
                                 'boxes' => array(
                                 'box1' => 'First Checkbox',
                                 'box2' => 'Second Checkbox'
                              )
                          ) );

    $this->assertSame( array( 'name' => 'name',
                              'type' => 'checkbox',
                              'value' => array('box2'),
                              'label' => false,
                              'valid' => null,
                              'validated' => false,
                              'errors' => array(),
                              'boxes' => array(
                                array( 'name'    => 'name[]',
                                       'id'      => 'name_0',
                                       'value'   => 'box1',
                                       'checked' => false,
                                       'label'   => 'First Checkbox' ),
                                array( 'name'    => 'name[]',
                                       'id'      => 'name_1',
                                       'value'   => 'box2',
                                       'checked' => true,
                                       'label'   => 'Second Checkbox' )
                        )),
                        $subject->to_array() );
  }

  /**
   * @covers Acreage\Fields\Checkbox::to_array
   */
  public function test_to_array_with_default_as_array() {
    $subject = new Checkbox( array(), 'name', 'checkbox',
                          array( 'default' => array('box1', 'box2'),
                                 'boxes' => array(
                                 'box1' => 'First Checkbox',
                                 'box2' => 'Second Checkbox'
                              )
                          ) );

    $this->assertSame( array( 'name' => 'name',
                              'type' => 'checkbox',
                              'value' => array('box1', 'box2'),
                              'label' => false,
                              'valid' => null,
                              'validated' => false,
                              'errors' => array(),
                              'boxes' => array(
                                array( 'name'    => 'name[]',
                                       'id'      => 'name_0',
                                       'value'   => 'box1',
                                       'checked' => true,
                                       'label'   => 'First Checkbox' ),
                                array( 'name'    => 'name[]',
                                       'id'      => 'name_1',
                                       'value'   => 'box2',
                                       'checked' => true,
                                       'label'   => 'Second Checkbox' )
                        )),
                        $subject->to_array() );
  }

}
