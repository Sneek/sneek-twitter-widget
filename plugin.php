<?php defined( 'ABSPATH' ) or exit;
/**
 * Plugin Name: Sneek Twitter Widget
 * Plugin URI: http://sneekdigital.co.uk/plugins/wp/twitter-widget
 * Description: A simple yet powerful Twitter widget
 * Version: 1.0.0
 * Author: Cristian Giordano
 * Author URI: http://sneekdigital.co.uk/
 * License: GPL2
 */

require_once __DIR__ . '/autoload.php';

new Sneek\Widgets\Manager;

new Sneek\Admin\Settings\Social;