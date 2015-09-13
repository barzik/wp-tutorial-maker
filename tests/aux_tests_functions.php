<?php

class AuxTestsFunctions {

    public $plugin;
    public $plugin_admin;

    function __construct() {
        $this->plugin = wp_tutorial_maker::get_instance();
        $this->plugin_admin = wp_tutorial_maker_Admin::get_instance();
    }

    public function set_up_post_data() {
        global $_POST;
        $_POST['taxonomy'] = 'category';
        $_POST['wp_tutorial_maker'] = 1;
        $_POST['wp_tutorial_maker_nextprev'] = true;
        $_POST['wp_tutorial_maker_next_text'] = 'Some next text';
        $_POST['wp_tutorial_maker_prev_text'] = 'Some prev text';
        $_POST['wp_tutorial_maker_show_category_index'] = true;
        $_POST['wp_tutorial_maker_text_category_list'] = 'Some Category List Header';
        $_POST['wp_tutorial_maker_text_category_link_list']  = 'Some Name to Category Link';
    }

    public function generate_tid() {
        $taxonomy = 'wptests_tax';
        register_taxonomy( $taxonomy, 'post' );
        $term = rand_str();
        $t = wp_insert_term( $term, $taxonomy );
        return $t['term_id'];
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