<?php

use Silex\Application;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

use Acreage\Form;

$app = new Silex\Application();
$app['debug'] = true;


$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register( new Mustache\Silex\Provider\MustacheServiceProvider,
                array( 'mustache.path' => __DIR__ . '/../views' ) );

class FormConfig {

  public static function fetch ($form_name) {
    $filename = dirname(__DIR__) . '/forms/' . $form_name . '.yaml';

    if( file_exists($filename) ) {
      return YAML::parse( file_get_contents($filename) );
    } else {
      $error = 'missing form: '.$filename;
      throw new Exception($error);
    }
  }

}


// GET /jobs

$app->get('/', function(Application $app) {
  return $app['mustache']->render( 'index.mustache', array( 'forms' => array(
    array( 'name' => 'Base Example',
           'url'  => '/forms/base' )
  )));
});

$app->match('/forms/base', function(Application $app, Request $request) {
  $form = Acreage\Form::create_from_data( array('validator' => $app['validator']), FormConfig::fetch('base')  );
  $form->handle_request($request);

  $render_context = Acreage\Render\Context::create($form);
  $render_context['content'] = implode('', array_map(
    function($field) use ($app) {
      if( in_array($field['type'], array_keys(Acreage\Form::$field_type_map)) ) {
        return $app['mustache']->render('form/'.$field['type'].'.mustache', $field );
      } else {
        return $app['mustache']->render('form/input.mustache', $field );
      }
    }, $render_context['fields']
  ));

  $render_context['invalid'] = ( $render_context['validated'] && !$render_context['valid'] );
  $render_context['success'] = ( $render_context['validated'] && $render_context['valid'] );

  return $app['mustache']->render('form.mustache', $render_context);
});

return $app;
