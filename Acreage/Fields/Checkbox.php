<?php

namespace Acreage\Fields;

use \Acreage\Render;

class Checkbox extends Base {

  /**
   * override the Base definition of default
   * with nothing checked we'll get an empty array
   */
  public $default = array();

  public function __construct( $config, $name, $type, $options=array(), $constraints=array() ) {
    call_user_func_array(array('parent', '__construct'), func_get_args());

    if( !is_array($this->default) ) {
      $this->default = array($this->default);
    }

    if( !is_array($this->value) ) {
      $this->value = array($this->value);
    }
  }


  public function to_array() {
    return array(
      'name'  => $this->name,
      'type'  => 'checkbox',
      'value' => $this->value,
      'label' => isset($this->options['label']) ? $this->options['label'] : false,
      'valid' => $this->valid,
      'validated' => $this->validated,
      'errors' => $this->errors,
      'boxes' => array_map(
        function($value, $label, $idx) {
          return array( 'name'    => $this->name.'[]',
                        'id'      => $this->name.'_'.$idx,
                        'value'   => $value,
                        'checked' => in_array($value, $this->value),
                        'label'   => $label );
        },
        array_keys($this->options['boxes']),
        array_values($this->options['boxes']),
        array_keys(array_values($this->options['boxes']))
      )
    );
  }

}
