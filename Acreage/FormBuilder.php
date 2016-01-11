<?php

namespace Acreage;

class FormBuilder {

  public function __construct( $config ) {
    $this->config = $config;
  }

  public function create_form( $defaults = array() ) {
    return new Form( $this, $defaults );
  }

}
