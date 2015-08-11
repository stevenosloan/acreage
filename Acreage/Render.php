<?php

namespace Acreage;

class Render {

  public static $self_closing_tags = array(
    'input',
    'img'
  );

  public static function tag( $type, $attributes=array() ) {
    $output = array();

    $opening       = '<'.$type;
    $opening_close = in_array( $type, self::$self_closing_tags ) ? '/>' : '>';

    if( array_key_exists('content', $attributes) ) {
      $content = $attributes['content'];
      unset($attributes['content']);
    } else {
      $content = '';
    }

    $open_tag = implode(' ', array( $opening, self::attributes($attributes), $opening_close ));

    if( !in_array( $type, self::$self_closing_tags ) ) {
      array_push( $output, $open_tag, $content, '</'.$type.'>' );
    } else {
      array_push( $output, $open_tag );
    }


    return implode('', $output);
  }

  public static function attributes( $attributes ) {
    $output = array();

    foreach( $attributes as $attr => $val ) {
      array_push($output, $attr.'="'.$val.'"');
    }

    return implode(' ', $output);
  }

}
