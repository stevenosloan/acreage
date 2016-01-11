<?php

namespace Acreage\Fields;

use \Acreage\Render;

class Checkbox extends Base {

  public $default = array();

  public function render_input() {
    $idx    = 0;
    $inputs = array();
    foreach( $this->options['values'] as $value => $label ) {
      array_push( $inputs,
        Render::tag('div', array(
          'class'   => $this->row_classes(),
          'content' => implode('', array(
              Render::tag('input', $this->input_attributes($value, $idx)),
              Render::tag('label', array(
                'class' => 'form-label-inline',
                'content' => $label,
                'for'     => $this->name.'_'.$idx )))))));

      $idx++;
    }

    return implode('', $inputs);
  }

  private function row_classes () {
    $classes = array('row');
    if( $this->valid === false ) {
      array_push( $classes, 'field-error' );
    }

    return implode(' ', $classes);
  }

  private function input_attributes($value, $idx) {
    $attributes = array(
      'type' => 'checkbox',
      'class' => 'form-checkbox',
      'value' => $value,
      'name'  => $this->name.'[]',
      'id'    => $this->name.'_'.$idx
    );
    if ( in_array($value, $this->value) ) {
      $attributes = array_merge($attributes, array(
        'checked' => 'true'
      ));
    }

    return $attributes;
  }

}

/*



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

*/
