<?php

/*
Plugin Name: Custom Post Type Classes
Description: A standardized way to create custom post types in a DRY, OO way
Version: 1.0
Author: Coby Tamayo
Author URI: http://www.tamayoweb.net
License: GPL2
*/

define( CPC_PLUGIN_ACTIVE, true );
define( CPC_PLUGIN_DIR, plugin_dir_path( __FILE__ ) );

require_once CPC_PLUGIN_DIR . 'CustomPostType.php';
require_once CPC_PLUGIN_DIR . 'CustomPostMetaBox.php';
require_once CPC_PLUGIN_DIR . 'CustomPostField.php';

// Uncomment below to see an example custom post in action.
// Make sure to view it from the front end as well as the admin!

// require CPC_PLUGIN_DIR . 'example/robot_example.php';