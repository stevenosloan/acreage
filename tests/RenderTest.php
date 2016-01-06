<?php

namespace Acreage\Tests;

use Acreage\Render;

class RenderTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers Render::attributes
   */
  public function test_render_attributes() {
    $this->assertSame('foo="bar"',
                      Render::attributes(array('foo' => 'bar')));
  }


  /**
   * @covers Render::attributes
   */
  public function test_render_multiple_attributes() {
    $this->assertSame('foo="bar" wu="tang"',
                      Render::attributes(array('foo' => 'bar', 'wu' => 'tang')));
  }

  /**
   * @covers Render::tag
   */
  public function test_render_self_closing_tag() {
    $this->assertSame('<img src="example.com" />',
                      Render::tag('img', array('src' => 'example.com')));
  }

  /**
   * @covers Render::tag
   */
  public function test_render_open_tag_with_content() {
    $this->assertSame('<div class="foo" >bar</div>',
                      Render::tag('div', array('class'   => 'foo',
                                               'content' => 'bar')));
  }

  /**
   * @covers Render::tag
   */
  public function test_render_open_tag_with_no_content() {
    $this->assertSame('<span class="icon" ></span>',
                      Render::tag('span', array('class' => 'icon')));
  }

}
