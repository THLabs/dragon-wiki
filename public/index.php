<?php
/**
 * Composer Autoloader
 */
require '../vendor/autoload.php';

/**
 * Instantiate Dragon application
 */
$app = new \Dragon\Dragon();

/**
 * Run the application
 */
$app->run();
