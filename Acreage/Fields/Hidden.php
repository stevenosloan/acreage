<?php

namespace Acreage\Fields;

use \Acreage\Render;
use \Symfony\Component\HttpFoundation\Request;

class Hidden extends Input {

  public function render() {
    return $this->render_input();
  }

}
