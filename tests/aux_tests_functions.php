<?php

/**
 * Aux functions
 *
 * @package wordpress-plugins-tests
 */
class WP_test_Tests_AuxFunctions {

    public $plugin;
    public $plugin_admin;

    function __construct() {
        $this->plugin = wp_tutorial_maker::get_instance();
        $this->plugin_admin = wp_tutorial_maker_Admin::get_instance();
    }

    public function set_up_post_data() {
        global $_POST;
        $_POST['taxonomy'] = 'category';
        $_POST['tutorial_maker'] = wp_create_nonce( 'submit_tutorial_category' );
        $_POST['wp_tutorial_maker'] = 1;
        $_POST['wp_tutorial_maker_nextprev'] = 'after';
        $_POST['wp_tutorial_maker_next_text'] = 'Some next text';
        $_POST['wp_tutorial_maker_prev_text'] = 'Some prev text';
        $_POST['wp_tutorial_maker_show_category_index'] = 1;
        $_POST['wp_tutorial_maker_text_category_list'] = 'Some Category List Header';
        $_POST['wp_tutorial_maker_text_category_link_name']  = 'Some Name to Category Link';
    }

    public function get_option_array_count() {
        $option_array = get_option($this->plugin->get_plugin_slug());
        if( FALSE === $option_array ) { //if is empty
            return 0;
        }
        else {
            return count( $option_array );
        }
    }


}