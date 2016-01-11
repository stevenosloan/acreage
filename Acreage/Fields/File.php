<?php

namespace Acreage\Fields;

use \Acreage\Render;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class File extends Base {

  public $stored_value = null;

  public function get_data(Request $request) {
    $this->value = $request->files->get($this->name);

    if( $request->request->has($this->name.'_stored') ) {
      $this->stored_value = $request->request->get($this->name.'_stored');
    }

    if( $this->value ) {
      if( $this->stored_value ) {
        unlink($this->tmp_file_location($this->stored_value));
        $this->stored_value = null;
      }
      $this->stored_value = $this->value->getClientOriginalName();
    } elseif( $this->stored_value ) {
      $this->value = new SymfonyFile( $this->tmp_file_location($this->stored_value) );
    }

    return $this->value;
  }

  public function render_input() {
    $tags = array();
    if( $this->stored_value ) {
      array_push( $tags, Render::tag('input', array(
        'type' => 'hidden',
        'name' => $this->name.'_stored',
        'value' => $this->stored_value
        )
      ));

      array_push( $tags, Render::tag('div', array(
        'class' => 'row',
        'content' => Render::tag('p', array(
          'content' => 'chosen file: '.$this->stored_value
        )
      ))));
    }

    array_push( $tags, Render::tag( 'input', $this->attributes() ) );

    return implode('', $tags);
  }

  public function cleanup() {
    if( $this->stored_value ) {
      unlink($this->tmp_file_location($this->stored_value));
    }
  }

  public function formatted_value() {
    if( $this->stored_value ) {
      $contents = file_get_contents($this->tmp_file_location($this->stored_value));

      return array(
        'type' => $this->value->getMimeType(),
        'name' => $this->stored_value,
        'content' => base64_encode($contents)
      );
    }

    return array();
  }

  protected function after_validation() {
    // only move files that were uploaded, not stored files
    if( method_exists($this->value, 'getClientOriginalName') ) {
      $this->value->move( $this->app['config']['path.tmp'], $this->value->getClientOriginalName() );
      $this->value = new SymfonyFile( $this->tmp_file_location($this->stored_value) );
    }
  }

  private function tmp_file_location($name) {
    return $this->app['config']['path.tmp'].'/'.$name;
  }

  private function attributes() {
    $classes = array('form-file');
    if( $this->valid === false ) {
      array_push( $classes, 'field-error' );
    }

    return array(
      'type' => 'file',
      'name' => $this->name,
      'class' => implode(' ', $classes)
    );
  }
}


/*



  public function add_file( $name, $options = array(), $constraints = array() ) {
    array_push( $this->fields,
                new $this->field_type_map['file']( $this->config, $name, 'file',
                                                   $options,
                                                   $constraints + array() ));

    return $this;
  }

*/
