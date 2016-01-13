<?php

namespace Acreage\Render;

class Context {

  public static function create( $form ) {
    $creator = new self($form);
    return $creator->context();
  }

  public $form;

  public function __construct( $form ) {
    $this->form = $form;
  }

  public function context() {
    $context = array();

    $context['attributes'] = $this->form->attributes;
    $context['fields']     = $this->fields();
    $context['valid']      = $this->form->is_valid;
    $context['validated']  = $this->form->validated;

    return $context;
  }

  private function fields() {
    return array_map( function($field) {
      return $field->to_array();
    }, $this->form->fields );
  }

}
