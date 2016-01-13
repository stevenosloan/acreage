<?php

namespace Acreage\Tests\Render;

use Acreage\Form;
use Acreage\Render\Context;

class ContextTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers Acreage\Render\Context::__construct()
   */
  public function test_construct_sets_form_as_given() {
    $subject = new Context('form');
    $this->assertSame( 'form', $subject->form );
  }

  /**
   * @covers Acreage\Render\Context::create()
   */
  public function test_create_returns_context() {
    $mock_config = array('validator' => 'validator');
    $subject     = Form::create_from_data( $mock_config,
                                           array( 'fields' => array( array( 'name' => 'field1',
                                                                            'type' => 'text' ))) );

    $this->assertSame( array( 'attributes' => array('method' => 'POST'),
                              'fields'     => array(
                                                array( 'name'   => 'field1',
                                                       'type'   => 'text',
                                                       'value'  => null,
                                                       'valid'  => null,
                                                       'errors' => array(),
                                                       'validated' => false ) ),
                              'valid'      => false,
                              'validated'  => false),
                       Context::create($subject) );

  }

}
