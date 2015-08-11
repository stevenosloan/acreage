<?php

namespace Acreage\Fields;

use \Acreage\Render;

class Input extends Base {

  public function render_input() {
    return Render::tag( 'input', $this->attributes() );
  }

  private function attributes() {
    $classes = array('form-input');
    if( $this->valid === false ) {
      array_push( $classes, 'field-error' );
    }

    return array(
      'type'  => $this->type,
      'name'  => $this->name,
      'value' => $this->value,
      'class' => implode(' ', $classes)
    );
  }

}
