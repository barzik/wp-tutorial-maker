<?php

/**
 * Tests to test that that testing framework is testing tests. Meta, huh?
 *
 * @package wordpress-plugins-tests
 */
class WP_test_tutorial_maker_admin extends WP_UnitTestCase {

    public $tid = NULL;
    public $auxClass;

    function __construct() {
        $this->auxClass = new WP_test_Tests_AuxFunctions();
    }


    function test_plugin_option_insert_and_delete() {

        //setting up subscriber user with admin permission
        $user = new WP_User( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
        wp_set_current_user( $user->ID );
        $this->auxClass->set_up_post_data();
        set_current_screen( 'edit-page' );

        $plugin_admin = wp_tutorial_maker_Admin::get_instance();

        //define new term
        $tid = $this->generate_tid();

        //getting the number of current base decider
        $base_decider_count = $this->auxClass->get_option_array_count();

        //submitting data!
        $plugin_admin->wp_tutorial_maker_option_update($tid);

        //getting the current count
        $wp_tutorial_maker_decider_options_count  = $this->auxClass->get_option_array_count();

        //should achieve successfull submission and increase the base data by 1, since we have permission
        $this->assertEquals($wp_tutorial_maker_decider_options_count, $base_decider_count+1);

        //delete
        $plugin_admin->wp_tutorial_maker_option_delete($tid);

        //getting the current count
        $wp_tutorial_maker_decider_options_count  = $this->auxClass->get_option_array_count();

        //should achieve successfull submission and increase the base data by 1, since we have permission
        $this->assertEquals($wp_tutorial_maker_decider_options_count, $base_decider_count);

    }

    function test_unregistered_user_cannot_access_admin() {
        //setting up subscriber user with no permission
        $user = new WP_User( $this->factory->user->create( array( 'role' => 'subscriber' ) ) );
        wp_set_current_user( $user->ID );

        $plugin_admin = wp_tutorial_maker_Admin::get_instance();
        //define new term
        $tid = $this->generate_tid();
        //making sure that we have $_POST data
        $this->auxClass->set_up_post_data();

        //getting the number of current base decider
        $base_decider_count = $this->auxClass->get_option_array_count();

        //submitting data!
        $plugin_admin->wp_tutorial_maker_option_update($tid);

        //no change, since user does not have permission
        $wp_tutorial_maker_decider_options_count  = $this->auxClass->get_option_array_count();

        //deleting!
        $plugin_admin->wp_tutorial_maker_option_delete($tid);

        //no change, since user does not have permission
        $this->assertEquals($wp_tutorial_maker_decider_options_count, $base_decider_count);

    }

    function test_plugin_admin_css() {
        $plugin_admin = wp_tutorial_maker_Admin::get_instance();
        $plugin = wp_tutorial_maker::get_instance();

        $result = $plugin_admin->enqueue_admin_styles();
        $ver = $plugin::VERSION;
        global $wp_styles;
        $this->assertEmpty($wp_styles);

        //now adding GET parameter
        $_GET['taxonomy']  = 'category';
        $result = $plugin_admin->enqueue_admin_styles();
        $this->assertArrayHasKey("wp-tutorial-maker-admin-styles",$wp_styles->registered);
        $this->assertEquals($wp_styles->registered['wp-tutorial-maker-admin-styles']->ver, $ver);
    }

    function test_plugin_admin_js() {
        $plugin_admin = wp_tutorial_maker_Admin::get_instance();
        $plugin = wp_tutorial_maker::get_instance();
        $_GET['taxonomy']  = 'category';
        $result = $plugin_admin->enqueue_admin_scripts();
        $ver = $plugin::VERSION;
        global $wp_scripts;
        $result = $plugin_admin->enqueue_admin_styles();
        $this->assertArrayHasKey('wp-tutorial-maker-admin-script',$wp_scripts->registered);
        $this->assertEquals($wp_scripts->registered['wp-tutorial-maker-admin-script']->ver, $ver);
    }

    /**
     * @covers wp_tutorial_maker_Admin::tutorial_decider
     */
    function test_admin_interface_html() {
        //setting up subscriber user with admin permission
        $user = new WP_User( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
        wp_set_current_user( $user->ID );
        $this->auxClass->set_up_post_data();

        $plugin_admin = wp_tutorial_maker_Admin::get_instance();

        //define new term
        $tid = $this->generate_tid();

        //submitting data!
        $plugin_admin->wp_tutorial_maker_option_update($tid);

        //make sure that every option in the HTML interface is there!

        $plugin = wp_tutorial_maker::get_instance();
        $option_array = get_option($plugin->get_plugin_slug());
        $tag_option_array = $option_array[$tid];
        $this->assertNotEmpty($tag_option_array);
        $this->assertNotEmpty($tag_option_array['wptm']);
        $this->assertNotEmpty($tag_option_array['wp_tutorial_maker_nextprev']);
        $this->assertNotEmpty($tag_option_array['wp_tutorial_maker_next_text']);
        $this->assertNotEmpty($tag_option_array['wp_tutorial_maker_prev_text']);
        $this->assertNotEmpty($tag_option_array['wp_tutorial_maker_show_category_index']);
        $this->assertNotEmpty($tag_option_array['wp_tutorial_maker_text_category_link_name']);
        $this->assertNotEmpty($tag_option_array['wp_tutorial_maker_text_category_list']);
    }

    public function generate_tid() {
        // create Test Categories and Array Representations
        $testcat_array = array(
            'slug' => 'testmcc',
            'name' => 'Test MCC',
            'description' => 'Category Test'
        );
        $testcat = $this->factory->category->create_and_get( $testcat_array );
        return $testcat->term_id;
    }

}
