<?php

namespace Acreage\Tests\Fields;

use Acreage\Fields\Input;
use \Symfony\Component\HttpFoundation\Request;

class InputTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers Acreage\Fields\Input::attributes
   */
  public function test_attributes() {
    $subject = new Input( array(), 'subject', 'text' );

    $attrs = $subject->attributes();

    $this->assertSame( array( 'type' => 'text',
                              'name' => 'subject',
                              'value' => null,
                              'class' => 'form-input' ),
                       $attrs );
  }

  /**
   * @covers Acreage\Fields\Input::attributes
   */
  public function test_attributes_after_handling_data() {
    $subject = new Input( array(), 'subject', 'text' );
    $mock    = new Request( array( 'subject' => 'value' ));

    $subject->get_data( $mock );
    $attrs = $subject->attributes();

    $this->assertSame( array( 'type' => 'text',
                              'name' => 'subject',
                              'value' => 'value',
                              'class' => 'form-input' ),
                       $attrs );
  }

  /**
   * @covers Acreage\Fields\Input::attributes
   */
  public function test_attributes_after_failing_validation() {
    $subject = new Input( array(), 'subject', 'text' );

    $subject->add_validation( array('invalid') );
    $attrs = $subject->attributes();

    $this->assertSame( array( 'type' => 'text',
                              'name' => 'subject',
                              'value' => null,
                              'class' => 'form-input field-error' ),
                       $attrs );
  }

}
