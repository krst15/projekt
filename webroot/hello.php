<?php 
/**
 * This is a Anax pagecontroller.
 *
 */

// Get environment & autoloader.
include(__DIR__.'/config.php'); 



// Prepare the page content
$app->theme->setVariable('title', "Hello World Pagecontroller")
           ->setVariable('main', "
    <h1>Hello World Pagecontroller</h1>
    <p>This is a sample pagecontroller that shows how to use Anax with its base theme, <i>anax-base</i>.</p>
");



// Render the response using theme engine.
$app->theme->render();
