<?php

use Silex\Application;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Acreage\Form;

$app = new Silex\Application();
$app['debug'] = true;


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

$app->get('/forms/base', function(Application $app) {
  $form = Acreage\Form::create_from_data( array('validator' => 'validator'), FormConfig::fetch('base')  );

  return $app->json( Acreage\Render\Context::create($form) );
});

return $app;
