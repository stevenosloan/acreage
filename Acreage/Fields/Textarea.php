<?php

namespace Acreage\Fields;

use \Acreage\Render;

class Textarea extends Base {


  public function render_input() {
    return Render::tag( 'textarea', $this->attributes() );
  }

  public function formatted_value() {
    return explode("\n", $this->value);
  }

  private function attributes() {
    $classes = array('form-area');
    if( $this->valid === false ) {
      array_push( $classes, 'field-error' );
    }

    return array(
      'name'    => $this->name,
      'class'   => implode(' ', $classes),
      'content' => $this->value
    );
  }
}
