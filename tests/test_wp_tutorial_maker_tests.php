<?php

/**
 * Tests to test that that testing framework is testing tests. Meta, huh?
 *
 * @package wordpress-plugins-tests
 */
class WP_test_tutorial_maker extends WP_UnitTestCase {

    public $tid = NULL;

	/**
	 * Ensure that the plugin has been installed and activated.
	 */
	function test_plugin_activated() {
		$this->assertTrue( is_plugin_active( 'wp-tutorial-maker/wp-tutorial-maker.php' ) );
	}

    /**
     * Ensure that the plugin has been installed and activated.
     */
    function test_plugin_public_css() {
        $plugin = wp_tutorial_maker::get_instance();
        $result = $plugin->enqueue_styles();
        $ver = $plugin::VERSION;
        global $wp_styles;
        $this->assertArrayHasKey("wp-tutorial-maker-plugin-styles",$wp_styles->registered);
        $this->assertEquals($wp_styles->registered['wp-tutorial-maker-plugin-styles']->ver, $ver);
    }

    /**
     * testing the slug name
     */

    function test_plugin_shortname() {
        $plugin = wp_tutorial_maker::get_instance();
        $result = $plugin->get_plugin_slug();
        $this->assertEquals('wp-tutorial-maker', $result);
    }

    function test_plugin_reorder() {
        $WP_test_tutorial_maker_admin = new WP_test_tutorial_maker_admin();


    }



}
