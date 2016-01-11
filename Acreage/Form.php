<?php

namespace Acreage;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class Form {

  public $field_type_map = array(
    'hidden'   => 'Acreage\Fields\Hidden',
    'text'     => 'Acreage\Fields\Input',
    'email'    => 'Acreage\Fields\Input',
    'textarea' => 'Acreage\Fields\Textarea',
    'radio'    => 'Acreage\Fields\Radio',
    'checkbox' => 'Acreage\Fields\Checkbox',
    'file'     => 'Acreage\Fields\File'
  );

  public $is_rendered     = false;
  public $validated       = false;
  public $is_valid        = false;
  public $request_handled = false;
  public $check_files     = false;

  public function __construct( $config = array() ) {
    $this->config   = (array) $config;
    $this->fields   = array();
    $this->data     = array();
    $this->set_defaults();
    $this->set_attributes();
    $this->validator = $this->config['validator'];
    $this->set_template('form');
  }

  # configure form
  # -----------------------------------------------

  public function set_template($template) {
    $this->template = $template;

    return $this;
  }

  public function set_defaults( $defaults = array() ) {
    $this->defaults = $defaults;

    return $this;
  }

  public function set_attributes( $attributes = array() ) {
    $this->attributes = array_merge( array( 'method' => 'POST',
                                            'class'  => 'form' ),
                                     $attributes );

    return $this;
  }

  public function success_message($msg) {
    $this->success_message = $msg;

    return $this;
  }

  public function add_field( $name, $type, $options = array(), $constraints = array() ) {
    if( array_key_exists($name, $this->defaults) ) {
      $options = array_merge(array('default' => $this->defaults[$name]), $options);
    }

    array_push( $this->fields,
                new $this->field_type_map[$type]( $this->config,
                                                  $name,
                                                  $type,
                                                  $options,
                                                  $constraints ));

    return $this;
  }

  public function add_radio( $name, Array $values, $options = array(), $constraints = array() ) {
    if( array_key_exists($name, $this->defaults) ) {
      $options = array_merge(array('default' => $this->defaults[$name]), $options);
    }
    array_push( $this->fields,
                new $this->field_type_map['radio']( $this->config, $name, 'radio',
                                                    array_merge( $options,
                                                                 array( 'values' => $values )),
                                                    $constraints ));

    return $this;
  }

  public function add_checkbox( $name, Array $values, $options = array(), $constraints = array() ) {
    if( array_key_exists($name, $this->defaults) ) {
      $options = array_merge(array('default' => $this->defaults[$name]), $options);
    }
    array_push( $this->fields,
                new $this->field_type_map['checkbox']( $this->config, $name, 'checkbox',
                                                    array_merge( $options,
                                                                 array( 'values' => $values )),
                                                    $constraints ));

    return $this;
  }

  public function add_file( $name, $options = array(), $constraints = array() ) {
    array_push( $this->fields,
                new $this->field_type_map['file']( $this->config, $name, 'file',
                                                   $options,
                                                   $constraints + array(new Assert\File( array('binaryFormat' => false,
                                                                                               'maxSize' => '2048k'))) ));

    return $this;
  }

  public function cleanup() {
    foreach( $this->fields as $field ) {
      $field->cleanup();
    }

    return $this;
  }


  # for templating

  public function render() {
    return $this->config['mustache']->render( $this->template, $this );
  }

  public function render_form() {
    if( !$this->is_rendered ) {
      $this->rendered_form = Render::tag( 'form', array_merge(
        $this->attributes,
        array( 'content' => implode('', array(
          $this->render_fields(),
          $this->render_submit()
      )))));

      $this->is_rendered = true;
    }

    return $this->rendered_form;
  }

  public function render_fields() {
    $output = array();
    foreach( $this->fields as $field ) {
      if( array_key_exists($field->name, $this->data) ) {
        $field->value = $this->data[$field->name];
      } elseif ( array_key_exists($field->name, $this->defaults) &&
                 !isset($field->value) ) {
        $field->value = $this->defaults[$field->name];
      }

      array_push( $output, $field->render() );
    }

    return implode('', $output);
  }

  public function render_submit() {
    return Render::tag( 'div', array(
      'class' => 'form-group',
      'content' => Render::tag( 'input', array(
        'type' => 'submit',
        'name' => 'submit',
        'value' => 'Submit',
        'class' => 'btn'
      ))));
  }

  # request
  # -------------------------------------------------

  public function handle_request(Request $request) {
    $this->data = $this->get_data($request);

    if( $request->getMethod() == $this->attributes['method'] ) {
      $this->request_handled = true;
      $this->validate_fields();
    }

    return $this;
  }

  public function valid() {
    if( !$this->validated && $this->request_handled ) {
      $this->validate_fields();
    }

    return $this->is_valid || !$this->request_handled;
  }

  private function validate_fields() {
    $this->validated = true;
    $this->is_valid  = true;

    foreach( $this->fields as $idx => $field ) {
      $errors = $this->validator->validateValue( $this->data[$field->name], $field->constraints );
      $this->fields[$idx] = $field->add_validation($errors);
      if( !$field->valid ) {
        $this->is_valid = false;
      }
    }
  }

  private function get_data(Request $request) {
    $data = array();
    foreach( $this->fields as $field ) {
      $data[$field->name] = $field->get_data($request);
    }

    return $data;
  }

}
