<?php

namespace Acreage\Fields;

use \Acreage\Render;
use \Symfony\Component\HttpFoundation\Request;

class Base {

  public $default = null;

  public function __construct( $app, $name, $type, $options=array(), $constraints=array() ) {
    $this->app   = $app;
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

  public function render() {
    return Render::tag('div', array(
      'class'   => 'form-group',
      'content' => implode('', array( $this->render_label(),
                                      $this->render_input(),
                                      $this->render_errors() )
      )
    ));
  }

  public function render_label() {
    $label = array(
      'content' => $this->label_content(),
      'class'   => 'form-label'
    );

    if( array_key_exists('label', $this->options) &&
        is_array( $this->options['label'] ) ) {
      $label = array_merge($label, $this->options['label']);
    }

    return Render::tag( 'label', $label );
  }

  public function label_content() {
    $label = ucwords( preg_replace("/(_)/", " ", $this->name) );
    if( array_key_exists('label', $this->options) ) {
      if( is_array( $this->options['label'] ) &&
          array_key_exists('content', $this->options['label']) ) {
        $label = $this->options['label']['content'];
      } else {
        $label = $this->options['label'];
      }
    }

    return $label;
  }

  public function render_errors() {
    if( !$this->validated || empty($this->errors) ) {
      return "";
    }

    $errors = array();
    foreach( $this->errors as $error ) {
      array_push( $errors,
                  Render::tag('li', array('content' => $error->getMessage()))
      );
    }

    return Render::tag('div', array(
      'class' => 'field-message',
      'content' => Render::tag('ul', array(
        'content' => implode('', $errors)
      ))
    ));
  }

}
