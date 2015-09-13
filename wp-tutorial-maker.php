<?php
/**
 * The WordPress WP Tutorial maker
 *
 * @package   wp-tutorial-maker
 * @author    Ran Bar-Zik <ran@bar-zik.com>
 * @license   GPL-2.0+
 * @link      http://internet-israel.com
 * @copyright 2014 Ran Bar-Zik
 *
 * @wordpress-plugin
 * Plugin Name:       wp-tutorial-maker
 * Plugin URI:        http://internet-israel.com
 * Description:       Plugin to convert categories to Tutorial based categories.
 * Version:           1.5
 * Author:            Ran Bar-Zik <ran@bar-zik.com>
 * Author URI:        http://internet-israel.com
 * Text Domain:       wp-tutorial-maker
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/barzik/wp-tutorial-maker
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-wp-tutorial-maker.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'wp_tutorial_maker', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'wp_tutorial_maker', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'wp_tutorial_maker', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/


if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wp-tutorial-maker-admin.php' );
	add_action( 'plugins_loaded', array( 'wp_tutorial_maker_Admin', 'get_instance' ) );

}
