<?php

/**
 * Tests to test that that testing framework is testing tests. Meta, huh?
 *
 * @package wordpress-plugins-tests
 */
class WP_test_tutorial_maker extends WP_UnitTestCase {

    public $tid = NULL;
    public $auxClass;
    public $plugin_admin;
    public $plugin;

    function __construct() {
        $this->auxClass = new WP_test_Tests_AuxFunctions();
        $this->plugin_admin = wp_tutorial_maker_Admin::get_instance();
        $this->plugin = wp_tutorial_maker::get_instance();
    }


    /**
	 * Ensure that the plugin has been installed and activated.
	 */
	function test_plugin_activated() {
		$this->assertTrue( is_plugin_active( 'wp-tutorial-maker/wp-tutorial-maker.php' ) );
        $plugin = wp_tutorial_maker::get_instance();
        $this->assertInternalType('object', $plugin);
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

    function test_adding_tutorial_text_to_single() {

        //creating admin user and set ut
        $user = new WP_User( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
        wp_set_current_user( $user->ID );

        //create category
        $cat_id = $this->factory->category->create(array(
            'slug' => rand_str(),
            'name' => rand_str(),
            'description' => rand_str()
        ));

        //create 3 posts, the $post_ids[1] is the middle one, suppose to have next and prev in the pretext
        $post_ids[] = $this->factory->post->create( array( 'post_type' => 'post', 'post_status'=> 'publish', 'post_title' => 'POST1', 'post_date' => date( 'Y-m-d H:i:s', time() -300 ) ) );
        $post_ids[] = $this->factory->post->create( array( 'post_type' => 'post', 'post_status'=> 'publish', 'post_title' => 'POST2', 'post_date' => date( 'Y-m-d H:i:s', time() -200 ) ) );
        $post_ids[] = $this->factory->post->create( array( 'post_type' => 'post', 'post_status'=> 'publish', 'post_title' => 'POST3', 'post_date' => date( 'Y-m-d H:i:s', time() -100 ) ) );

        //adding them to the category
        foreach($post_ids as $post_id) {
            $res = wp_set_post_categories( $post_id, $cat_id );
        }

        //making the category a tutorial category
        $this->auxClass->set_up_post_data();
        $this->plugin_admin->wp_tutorial_maker_option_update($cat_id);

        //go to tutorial post
        $this->go_to( get_permalink( $post_ids[1] ) );

        $post_content_inside_tutorial = get_echo( 'the_content' );

        //making sure that the tutorial pretext that I want is there
        
        $this->assertRegExp('/<div class=\'wptm_prev\'><span>Some prev text<\/span><a href="http:\/\/example\.org\/\?p='.$post_ids[0].'" rel="prev">POST1<\/a> <\/div>/', $post_content_inside_tutorial);
        $this->assertRegExp('/<div class=\'wptm_next\'><span>Some next text<\/span><a href="http:\/\/example\.org\/\?p='.$post_ids[2].'" rel="next">POST3<\/a> <\/div>/', $post_content_inside_tutorial);
        $this->assertRegExp('/<div id=\'wptm_before_category_link_text\'>Some Category List Header<\/div>/', $post_content_inside_tutorial);
        $this->assertRegExp('/<div id=\'wptm_before_category_link_text\'>Some Category List Header<\/div>/', $post_content_inside_tutorial);
        $this->assertRegExp('/<div class=\'wptm_link_to_category\'><a href=\'http:\/\/example\.org\/\?cat='.$cat_id.'\'>Some Name to Category Link<\/div>/', $post_content_inside_tutorial);

        //making sure that the tutorial pretext is not being added by mistake to some other non Tutorial post
        $non_tutorial_post_id = $this->factory->post->create( array( 'post_type' => 'post', 'post_status'=> 'publish', 'post_title' => 'POST2', 'post_date' => date( 'Y-m-d H:i:s', time() -200 ) ) );

        $this->go_to( get_permalink( $non_tutorial_post_id ) );

        $post_content_inside_tutorial = get_echo( 'the_content' );

        $this->assertNotRegExp('/wptm/', $post_content_inside_tutorial);

    }


    function test_plugin_load_textdomain() {
        add_filter( 'locale', array( $this, '_set_locale_to_hebrew' ) );

        $this->plugin->load_plugin_textdomain();
        $this->assertTrue( is_textdomain_loaded( 'wp-tutorial-maker' ) );

    }

    public function _set_locale_to_hebrew() {
        return 'he_IL';
    }


    public function create_posts($cat_id) {

        $posts[] = $this->factory->post->create( array( 'post_type' => 'post', 'post_status'=> 'publish', 'post_title' => 'POST1', 'post_date' => date( 'Y-m-d H:i:s', time() -300 ) ) );
        $posts[] = $this->factory->post->create( array( 'post_type' => 'post', 'post_status'=> 'publish', 'post_title' => 'POST2', 'post_date' => date( 'Y-m-d H:i:s', time() -200 ) ) );
        $posts[] = $this->factory->post->create( array( 'post_type' => 'post', 'post_status'=> 'publish', 'post_title' => 'POST3', 'post_date' => date( 'Y-m-d H:i:s', time() -100 ) ) );

        return $posts;

    }

    public function generate_tid() {
        // create Test Categories and Array Representations
        $testcat_array = array(
            'slug' => rand_str(),
            'name' => rand_str(),
            'description' => rand_str()
        );
        $testcat = $this->factory->category->create_and_get( $testcat_array );
        return $testcat->term_id;
    }


}
