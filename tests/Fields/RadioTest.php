<?php

namespace Acreage\Tests\Fields;

use Acreage\Fields\Radio;

class RadioTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers Acreage\Fields\Input::to_array
   */
  public function test_to_array_with_no_default() {
    $subject = new Radio( array(), 'name', 'radio',
                          array( 'radios' => array(
                            'rdio1' => 'First Radio',
                            'rdio2' => 'Second Radio'
                          ), 'label' => 'Radio Field' ) );

    $this->assertSame( array( 'name' => 'name',
                              'type' => 'radio',
                              'value' => null,
                              'label' => 'Radio Field',
                              'valid' => null,
                              'validated' => false,
                              'errors' => array(),
                              'radios' => array(
                                array( 'name'    => 'name',
                                       'id'      => 'name_0',
                                       'value'   => 'rdio1',
                                       'checked' => false,
                                       'label'   => 'First Radio' ),
                                array( 'name'    => 'name',
                                       'id'      => 'name_1',
                                       'value'   => 'rdio2',
                                       'checked' => false,
                                       'label'   => 'Second Radio' )
                        )),
                        $subject->to_array() );
  }

  /**
   * @covers Acreage\Fields\Input::to_array
   */
  public function test_to_array_with_default() {
    $subject = new Radio( array(), 'name', 'radio',
                          array( 'default' => 'rdio2',
                                 'radios' => array(
                                 'rdio1' => 'First Radio',
                                 'rdio2' => 'Second Radio'
                              )
                          ) );

    $this->assertSame( array( 'name' => 'name',
                              'type' => 'radio',
                              'value' => 'rdio2',
                              'label' => false,
                              'valid' => null,
                              'validated' => false,
                              'errors' => array(),
                              'radios' => array(
                                array( 'name'    => 'name',
                                       'id'      => 'name_0',
                                       'value'   => 'rdio1',
                                       'checked' => false,
                                       'label'   => 'First Radio' ),
                                array( 'name'    => 'name',
                                       'id'      => 'name_1',
                                       'value'   => 'rdio2',
                                       'checked' => true,
                                       'label'   => 'Second Radio' )
                        )),
                        $subject->to_array() );
  }

}
