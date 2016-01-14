<?php

namespace Acreage\Fields;

use \Acreage\Render;
use \Symfony\Component\HttpFoundation\Request;

class Base {

  public $default   = null;

  public $errors    = array();
  public $validated = false;
  public $valid     = null;

  public function __construct( $config, $name, $type, $options=array(), $constraints=array() ) {
    $this->config      = $config;
    $this->name        = $name;
    $this->type        = $type;
    $this->value       = array_key_exists('default', $options) ? $options['default'] : $this->default;
    $this->options     = $options;
    $this->constraints = $this->resolve_constraints($constraints);
  }

  public function cleanup() {
    // empty method to be overriden
  }

  public function to_array() {
    return array( 'name'  => $this->name,
                  'type'  => $this->type,
                  'value' => $this->value,
                  'label' => isset($this->options['label']) ? $this->options['label'] : false,
                  'valid'     => $this->valid,
                  'errors'    => $this->errors,
                  'validated' => $this->validated );
  }

  public function get_data(Request $request) {
    if( $request->query->has($this->name) ) {
      $this->value = $request->query->get($this->name);
    }

    if( $request->request->has($this->name) ) {
      $this->value = $request->request->get($this->name);
    }

    return $this->value;
  }

  public function add_validation($errors) {
    $this->validated = true;

    if( 0 === count($errors) ) {
      $this->valid = true;
      $this->after_validation();
    } else {
      $this->valid  = false;
      $this->errors = $errors;
    }

    return $this;
  }

  protected function after_validation() {
    // empty method to be overriden
  }

  public function resolve_constraints( $constraints=array() ) {
    return array_map(
      function( $key, $val ) {
        if( is_object($val) ) {
          return $val;
        } else if( is_array($val) ) {
          $constraint = 'Symfony\Component\Validator\Constraints\\'.$key;
          return new $constraint($val);
        } else {
          $constraint = 'Symfony\Component\Validator\Constraints\\'.$val;
          return new $constraint();
        }
      },
      array_keys($constraints),
      array_values($constraints)
    );
  }

}
