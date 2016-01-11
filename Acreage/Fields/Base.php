<?php

namespace Acreage\Fields;

use \Acreage\Render;
use \Symfony\Component\HttpFoundation\Request;

class Base {

  public $default = null;

  public function __construct( $config, $name, $type, $options=array(), $constraints=array() ) {
    $this->config   = $config;
    $this->name  = $name;
    $this->type  = $type;
    $this->value = array_key_exists('default', $options) ? $options['default'] : $this->default;
    $this->options     = $options;
    $this->constraints = $constraints;

    $this->validated = false;
    $this->valid     = null;
  }

  public function cleanup() {
    // empty method to be overriden
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

}
