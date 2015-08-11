<?php

namespace Acreage\Tests;

use Acreage\Render;

class RenderTest extends \PHPUnit_Framework_TestCase {

  public function testRenderAttributes() {
    $this->assertSame('foo="bar"',
                      Render::attributes(array('foo' => 'bar')));
  }

  public function testRenderMultipleAttributes() {
    $this->assertSame('foo="bar" wu="tang"',
                      Render::attributes(array('foo' => 'bar', 'wu' => 'tang')));
  }

  public function testRenderSelfClosingTag() {
    $this->assertSame('<img src="example.com" />',
                      Render::tag('img', array('src' => 'example.com')));
  }

  public function testRenderOpenTagWithContent() {
    $this->assertSame('<div class="foo" >bar</div>',
                      Render::tag('div', array('class'   => 'foo',
                                               'content' => 'bar')));
  }

  public function testRenderOpenTagWithNoContent() {
    $this->assertSame('<span class="icon" ></span>',
                      Render::tag('span', array('class' => 'icon')));
  }

}
