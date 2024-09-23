<?php
$plugin_engine = dirname( __FILE__ ) . '/src';
require_once $plugin_engine . '/Pngx/Autoloader.php';
$autoloader = Pngx__Autoloader::instance();
$autoloader->register_prefix( 'Pngx__', $plugin_engine . '/Pngx' );
$autoloader->register_autoloader();