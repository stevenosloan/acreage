<?php

namespace Acreage\Tests\Fields;

use Acreage\Fields\Input;
use \Symfony\Component\HttpFoundation\Request;

class InputTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers Acreage\Fields\Input::to_array
   */
  public function test_to_array() {
    $subject = new Input( array(), 'name', 'text', array('default' => 'default'));

    $this->assertSame( array( 'name'   => 'name',
                              'type'   => 'text',
                              'value'  => 'default',
                              'valid'  => null,
                              'errors' => array(),
                              'validated' => false ),
                       $subject->to_array() );
  }

}
