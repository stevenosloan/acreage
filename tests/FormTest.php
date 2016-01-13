<?php

namespace Acreage\Tests;

use Acreage\Form;
use \Symfony\Component\HttpFoundation\Request;

class FormTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers Acreage::Form::__construct
   */
  public function test_sets_values_from_builder() {
    $mock_validator = 'config.validator';
    $mock_config    = array('validator' => $mock_validator);

    $subject = new Form( $mock_config );

    $this->assertSame( $mock_config, $subject->config );
    $this->assertSame( array(), $subject->fields );
    $this->assertSame( array(), $subject->data );
    $this->assertSame( array(), $subject->defaults );
    $this->assertSame( array( 'method' => 'POST' ),
                       $subject->attributes );
    $this->assertSame( $mock_validator, $subject->validator );
  }



  /**
   * @covers Acreage::Form::set_defaults
   */
  public function test_set_defaults_sets_defaults_and_returns_self() {
    $mock_config = array('validator' => 'config.validator');
    $subject     = new Form( $mock_config );
    $defaults    = array('default' => 'value');

    $resp = $subject->set_defaults( $defaults );

    $this->assertSame( $subject, $resp );
    $this->assertSame( $defaults, $subject->defaults );
  }


  /**
   * @covers Acreage::Form::set_attributes
   */
  public function test_set_attributes_merges_attributes_and_returns_self() {
    $mock_config = array('validator' => 'config.validator');
    $subject     = new Form( $mock_config );

    $resp = $subject->set_attributes( array('method' => 'GET') );

    $this->assertSame( $subject, $resp );
    $this->assertSame( array( 'method' => 'GET' ),
                       $subject->attributes );

  }


  /**
   * @covers Acreage::Form::success_message
   */
  public function test_success_message_sets_message_and_returns_self() {
    $mock_config = array('validator' => 'config.validator');
    $subject     = new Form( $mock_config );
    $message     = 'Hodor!';

    $resp = $subject->success_message($message);

    $this->assertSame( $message, $subject->success_message );
    $this->assertSame( $subject, $resp );
  }


  /**
   * @covers Acreage::Form::add_field
   */
  public function test_add_field_adds_a_basic_field_and_returns_self() {
    $mock_config = array('validator' => 'config.validator');
    $subject     = new Form( $mock_config );

    $resp = $subject->add_field( 'name', 'text' );

    $this->assertSame( $subject, $resp );
    $this->assertInstanceOf( 'Acreage\Fields\Input', $subject->fields[0] );

    $field = $subject->fields[0];

    $this->assertSame( 'name', $field->name );
    $this->assertSame( 'text', $field->type );
    $this->assertSame( null, $field->value );
  }

  /**
   * @covers Acreage::Form::add_field
   */
  public function test_add_field_adds_a_field_with_a_default_if_default_set_on_form() {
    $mock_config = array('validator' => 'config.validator');
    $subject     = new Form( $mock_config );

    $resp = $subject->set_defaults( array('name' => 'default') )
                    ->add_field( 'name', 'text' );

    $this->assertSame( $subject, $resp );
    $this->assertInstanceOf( 'Acreage\Fields\Input', $subject->fields[0] );

    $this->assertSame( $subject->defaults, array('name' => 'default') );

    $field = $subject->fields[0];

    $this->assertSame( 'name', $field->name );
    $this->assertSame( 'text', $field->type );
    $this->assertSame( 'default', $field->value );
  }



  /**
   * @covers Acreage::Form::handle_request
   */
  public function test_handle_request_sets_data_and_validates_fields() {
    $mock_config = array( 'validator' => new MockValidator());
    $subject     = new Form( $mock_config );
    $request     = new Request( array('name' => 'value') );

    $resp = $subject->set_attributes(array('method' => 'GET'))
                    ->add_field('name', 'text')
                    ->handle_request( $request );

    $this->assertSame( array('name' => 'value'),
                       $subject->data );
    $this->assertTrue( $subject->request_handled );
    $this->assertSame( $subject, $resp );
  }

  /**
   * @covers Acreage::Form::handle_request
   */
  public function test_handle_request_sets_data_but_doesnt_validate_with_non_matching_method() {
    $mock_config = array( 'validator' => new MockValidator());
    $subject     = new Form( $mock_config );
    $request     = new Request( array('name' => 'value') );

    $resp = $subject->add_field('name', 'text')
                    ->handle_request( $request );

    $this->assertSame( array('name' => 'value'),
                       $subject->data );
    $this->assertFalse( $subject->request_handled );
    $this->assertSame( $subject, $resp );
  }

  /**
   * @covers Acreage::Form::handle_request
   */
  public function test_handle_request_runs_validation_on_fields_with_matching_method() {
    $mock_config = array( 'validator' => new MockValidator());
    $subject     = new Form( $mock_config );
    $request     = new Request( array('one' => 'one',
                                       'two' => 'two') );

    $resp = $subject->set_attributes(array('method' => 'GET'))
                    ->add_field('one', 'text')
                    ->add_field('two', 'text')
                    ->handle_request( $request );

    $this->assertTrue( $subject->valid() );
    $this->assertTrue( $subject->request_handled );
    $this->assertSame( $subject, $resp );

    foreach( $subject->fields as $field ) {
      $this->assertTrue( $field->validated );
      $this->assertTrue( $field->valid );
    }
  }

  /**
   * @covers Acreage::Form::handle_request
   */
  public function test_handle_request_runs_validation_on_fields_with_matching_method_and_is_invalid_if_invalid() {
    $mock_config = array( 'validator' => new MockValidator(array('two' => array('invalid'))));
    $subject     = new Form( $mock_config );
    $request     = new Request( array('one' => 'one',
                                       'two' => 'two') );

    $resp = $subject->set_attributes(array('method' => 'GET'))
                    ->add_field('one', 'text')
                    ->add_field('two', 'text')
                    ->handle_request( $request );

    $this->assertFalse( $subject->valid() );
    $this->assertTrue( $subject->request_handled );
    $this->assertSame( $subject, $resp );

    foreach( $subject->fields as $field ) {
      $this->assertTrue( $field->validated );
      if( $field->name == "one" ) {
        $this->assertTrue( $field->valid );
      } else {
        $this->assertFalse( $field->valid );
      }
    }
  }


  /**
   * @covers Acreage::Form::create_from_data
   */
  public function test_create_from_data_generates_form_from_data() {
    $mock_config = array('validator'  => 'mock.validator');
    $form_data   = array('attributes' => array('class'  => 'form yo',
                                               'method' => 'GET'),
                         'defaults'   => array('field1' => 'default'),
                         'fields'     => array( array('name' => 'field1',
                                                      'type' => 'text'),
                                                array('name' => 'field2',
                                                      'type' => 'email')));

    $subject = Form::create_from_data( $mock_config, $form_data );

    $this->assertInstanceOf( 'Acreage\Form', $subject );

    $this->assertSame('form yo', $subject->attributes['class']);
    $this->assertSame('GET', $subject->attributes['method']);

    $field1 = $subject->fields[0];
    $this->assertSame( 'default', $field1->value );
    $this->assertSame( 'text', $field1->type );

    $field2 = $subject->fields[1];
    $this->assertSame( null, $field2->value );
    $this->assertSame( 'email', $field2->type );
  }



  /**
   * @covers Acreage::Form::add_field
   */
  public function test_add_field_adds_correct_type_for_input_fields() {
    $mock_config = array('validator'  => 'mock.validator');
    $subject     = new Form( $mock_config );

    $subject->add_field('text', 'text');
    $subject->add_field('email', 'email');
    $subject->add_field('number', 'number');

    $this->assertInstanceOf('Acreage\Fields\Input', $subject->fields[0]);
    $this->assertInstanceOf('Acreage\Fields\Input', $subject->fields[1]);
    $this->assertInstanceOf('Acreage\Fields\Input', $subject->fields[2]);
  }

  /**
   * @covers Acreage::Form::add_field
   */
  public function test_add_field_adds_correct_type_for_hidden_fields() {
    $mock_config = array('validator'  => 'mock.validator');
    $subject     = new Form( $mock_config );

    $subject->add_field('field', 'hidden');

    $this->assertInstanceOf('Acreage\Fields\Hidden', $subject->fields[0]);
  }

  /**
   * @covers Acreage::Form::add_field
   */
  public function test_add_field_adds_correct_type_for_checkboxes() {
    $mock_config = array('validator'  => 'mock.validator');
    $subject     = new Form( $mock_config );

    $subject->add_field('field', 'checkbox');

    $this->assertInstanceOf('Acreage\Fields\Checkbox', $subject->fields[0]);
  }

  /**
   * @covers Acreage::Form::add_field
   */
  public function test_add_field_adds_correct_type_for_radios() {
    $mock_config = array('validator'  => 'mock.validator');
    $subject     = new Form( $mock_config );

    $subject->add_field('field', 'radio');

    $this->assertInstanceOf('Acreage\Fields\Radio', $subject->fields[0]);
  }

  /**
   * @covers Acreage::Form::add_field
   */
  public function test_add_field_adds_correct_type_for_files() {
    $mock_config = array('validator'  => 'mock.validator');
    $subject     = new Form( $mock_config );

    $subject->add_field('field', 'file');

    $this->assertInstanceOf('Acreage\Fields\File', $subject->fields[0]);
  }

}

class MockValidator {

  /**
   * Pass in an array of $value => array($errors) you expect
   * the validator to apply
   *
   * if no values are passed, the response will always be
   * valid
   */
  public function __construct( $returns=array() ) {
    $this->returns = $returns;
  }

  public function validateValue( $value, $field_constraints ) {
    if( array_key_exists( $value, $this->returns ) ) {
      return $this->returns[$value];
    } else {
      return array();
    }
  }

}
