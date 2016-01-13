<?php

namespace Acreage;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class Form {

  /**
   *  list of field types that have a special
   *  class for their construction. otherwise use
   *  the generic "input" class"
   */
  public $field_type_map = array(
    'default'  => 'Acreage\Fields\Input',
    'hidden'   => 'Acreage\Fields\Hidden',
    'textarea' => 'Acreage\Fields\Textarea',
    'radio'    => 'Acreage\Fields\Radio',
    'checkbox' => 'Acreage\Fields\Checkbox',
    'file'     => 'Acreage\Fields\File'
  );

  public $validated       = false;
  public $is_valid        = false;
  public $request_handled = false;
  public $check_files     = false;

  /**
   *  create the form from an array of data
   */
  public static function create_from_data( $config, $data ) {
    $form = new Form( $config );

    if( array_key_exists('attributes', $data) ) {
      $form->set_attributes( $data['attributes'] );
    }

    if( array_key_exists('defaults', $data) ) {
      $form->set_defaults( $data['defaults'] );
    }

    foreach( $data['fields'] as $field_src ) {
      $field = array_merge( array( 'options'     => array(),
                                   'constraints' => array() ),
                            $field_src );
      $form->add_field( $field['name'], $field['type'], $field['options'], $field['constraints'] );
    }

    return $form;
  }

  public function __construct( $config = array() ) {
    $this->config   = (array) $config;
    $this->fields   = array();
    $this->data     = array();
    $this->set_defaults();
    $this->set_attributes();
    $this->validator = $this->config['validator'];
  }

  # configure form
  # -----------------------------------------------

  public function set_defaults( $defaults = array() ) {
    $this->defaults = $defaults;

    return $this;
  }

  public function set_attributes( $attributes = array() ) {
    $this->attributes = array_merge( array('method' => 'POST'),
                                     $attributes );

    return $this;
  }

  public function success_message($msg) {
    $this->success_message = $msg;

    return $this;
  }

  /**
   * meta method that takes type and attempts to generate field from that
   */
  public function add_field( $name, $type, $options = array(), $constraints = array() ) {
    if( array_key_exists($name, $this->defaults) ) {
      $options = array_merge(array('default' => $this->defaults[$name]), $options);
    }

    if ( array_key_exists($type, $this->field_type_map) ) {
      $field_class = $this->field_type_map[$type];
    } else {
      $field_class = $this->field_type_map['default'];
    }

    array_push( $this->fields,
                new $field_class( $this->config,
                                  $name,
                                  $type,
                                  $options,
                                  $constraints ));

    return $this;
  }

  public function cleanup() {
    foreach( $this->fields as $field ) {
      $field->cleanup();
    }

    return $this;
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
