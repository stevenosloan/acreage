<?php

namespace Acreage\Fields;

use \Acreage\Render;

class Radio extends Base {

  public function to_array() {
    return array(
      'name'      => $this->name,
      'type'      => 'radio',
      'value'     => $this->value,
      'label'     => isset($this->options['label']) ? $this->options['label'] : false,
      'valid'     => $this->valid,
      'validated' => $this->validated,
      'errors'    => $this->errors,
      'radios'    => array_map(
        function($value, $label, $idx) {
          return array( 'name'    => $this->name,
                        'id'      => $this->name.'_'.$idx,
                        'value'   => $value,
                        'checked' => ($this->value == $value),
                        'label'   => $label );
        },
        array_keys($this->options['radios']),
        array_values($this->options['radios']),
        array_keys(array_values($this->options['radios']))
      )
    );
  }

}

__halt_compiler();

@@ example_mustache

<div class="form-group">
  {{# radios }}
  <div class="field-row">
    <input type="radio" class="form-radio" value="{{ value }}" name="{{ name }}" id="{{ id }}" {{# checked }}checked="true"{{/ checked }} />
    <label class="form-inline-label" for="{{ id }}">{{ label }}</label>
  </div>
  {{/ radios }}
</div>
