<?php

namespace Acreage;

class FormBuilder {

  public function __construct( $app ) {
    $this->app = $app;
  }

  public function create_form( $defaults = array() ) {
    return new Form( $this, $defaults );
  }

}
