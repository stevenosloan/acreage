<?php

namespace Acreage\Tests\Fields;

use Acreage\Fields\Base;
use \Symfony\Component\HttpFoundation\Request;

class BaseTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers Acreage\Fields\Base::__construct
   */
  public function test_default_values_are_set() {
    $subject = new Base( array(), 'subject', 'text', array('default' => 'value') );

    $this->assertSame( 'value',
                        $subject->value );
  }


  /**
   * @covers Acreage\Fields\Base::get_data
   */
  public function test_get_data_returns_data_with_request_data() {
    $subject = new Base( array(), 'subject', 'text' );
    $mock    = new Request( array(), array('subject' => 'value') );

    $value = $subject->get_data($mock);

    $this->assertSame('value',
                      $value );
  }

  /**
   * @covers Acreage\Fields\Base::get_data
   */
  public function test_get_data_sets_data_with_request_data() {
    $subject = new Base( array(), 'subject', 'text' );
    $mock    = new Request( array(), array('subject' => 'value') );

    $subject->get_data($mock);

    $this->assertSame('value',
                      $subject->value );

  }

  /**
   * @covers Acreage\Fields\Base::get_data
   */
  public function test_get_data_returns_data_with_query_data() {
    $subject = new Base( array(), 'subject', 'text' );
    $mock    = new Request( array('subject' => 'value') );

    $value = $subject->get_data($mock);

    $this->assertSame('value',
                      $value );
  }

  /**
   * @covers Acreage\Fields\Base::get_data
   */
  public function test_get_data_sets_data_with_query_data() {
    $subject = new Base( array(), 'subject', 'text' );
    $mock    = new Request( array('subject' => 'value') );

    $subject->get_data($mock);

    $this->assertSame('value',
                      $subject->value );

  }

  /**
   * @covers Acreage\Fields\Base::get_data
   */
  public function test_get_data_prefers_request_data_to_query_data() {
    $subject = new Base( array(), 'subject', 'text' );
    $mock    = new Request( array('subject' => 'query'), array('subject' => 'request') );

    $subject->get_data($mock);

    $this->assertSame('request',
                      $subject->value );
  }

  /**
   * @covers Acreage\Fields\Base::get_data
   */
  public function test_default_values_are_overriden_with_request() {
    $subject = new Base( array(), 'subject', 'text', array('default' => 'value') );
    $mock    = new Request( array('subject' => 'not_default') );

    $subject->get_data($mock);

    $this->assertSame( 'not_default',
                        $subject->value );
  }



  /**
   * @covers Acreage\Fields\Base::add_validation
   */
  public function test_add_validation_with_no_errors() {
    $subject = new Base( array(), 'subject', 'text' );
    $errors  = array();

    $subject->add_validation( $errors );

    $this->assertTrue( $subject->validated );
    $this->assertTrue( $subject->valid );
  }


  /**
   * @covers Acreage\Fields\Base::add_validation
   */
  public function test_add_validation_with_errors() {
    $subject = new Base( array(), 'subject', 'text' );
    $errors  = array('invalid field');

    $subject->add_validation( $errors );

    $this->assertTrue( $subject->validated );
    $this->assertFalse( $subject->valid );
    $this->assertSame( $subject->errors,
                       $errors );
  }


  /**
   * @covers Acreage\Fields\Base::to_array
   */
  public function test_to_array() {
    $subject = new Base( array(), 'name', 'text', array('default' => 'default'));

    $this->assertSame( array( 'name'   => 'name',
                              'type'   => 'text',
                              'value'  => 'default',
                              'valid'  => null,
                              'errors' => array(),
                              'validated' => false ),
                       $subject->to_array() );
  }


}
