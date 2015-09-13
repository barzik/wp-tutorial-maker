<?php
/**
 * Plugin Name.
 *
 * @package   wp-tutorial-maker
 * @author    Ran Bar-Zik <ran@bar-zik.com>
 * @license   GPL-2.0+
 * @link      http://internet-israel.com
 * @copyright 2014 Ran Bar-Zik
 */

/**
 *
 * @package   wp-tutorial-maker
 * @author    Ran Bar-Zik <ran@bar-zik.com>
 */
class wp_tutorial_maker {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.2';

	/**
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected static $plugin_slug = 'wp-tutorial-maker';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

        add_filter( 'posts_orderby', array( $this, 'reorder_category' ) );
        add_filter( 'the_content', array( $this, 'add_previous_next_links' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return self::$plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );

					restore_current_blog();
				}
			}
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		restore_current_blog();

	}

    /**
     * Clearing out the data
     */

    private static function single_deactivate() {
        delete_option(self::$plugin_slug);
    }


	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = self::$plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( self::$plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}


	/**
     *
     * The filter for posts_orderby
     *
	 */
    public function reorder_category($orderby) {

        $wp_tutorial_maker_decider = get_option( self::$plugin_slug );;
        $curr_category_name = single_cat_title('', false);

        // Check if you are in the Category Page. Should not be called in case of main page/archives/tags
        if ( is_category() && $curr_category_name != '' ) {
            $term_id = get_cat_ID($curr_category_name);
            if ( $wp_tutorial_maker_decider[$term_id]['wptm'] == 1 ) {
                remove_filter('posts_orderby', 'reorder_category');
                return 'post_date ASC';
            }
        }

        return $orderby;
    }

    /**
     *
     * returning HTML next\previous links
     *
     * @param $content
     * @return string
     */

    public function add_previous_next_links($content) {

        if (is_single() ) {

            $tutorial_maker_options = $this->test_if_in_tutorial_category(get_the_ID());

            if($tutorial_maker_options  != false && $tutorial_maker_options['wptm'] != 0) {

                if( !empty( $tutorial_maker_options['wp_tutorial_maker_prev_text'] ) ) {
                    $prev_text = '<span>'.$tutorial_maker_options['wp_tutorial_maker_prev_text'].'</span>';
                } else {
                    $prev_text = '<span>'.__('&raquo;', self::$plugin_slug ).'</span>';
                }

                if( !empty( $tutorial_maker_options['wp_tutorial_maker_next_text'] ) ) {
                    $next_text = '<span>'.$tutorial_maker_options['wp_tutorial_maker_next_text'].'</span>';
                } else {
                    $next_text = '<span>'.__('&raquo;', self::$plugin_slug ).'</span>';
                }

                if(is_rtl() == true) {
                    $prev_text .= '%link ';
                    $next_text .= '%link ';
                } else {
                    $prev_text = $prev_text.'%link ';
                    $next_text = $next_text.'%link ';
                }


                $prev_next_links = trim("<div class='wptm_nextprev'>
                                        <div class='wptm_prev'>".
                                            get_previous_post_link( $prev_text,  '%title', true ).
                                        "</div>
                                        <div class='wptm_next'>".
                                            get_next_post_link( $next_text,  '%title', true ).
                                        "</div>
                                    </div>");

                if($tutorial_maker_options['wp_tutorial_maker_nextprev'] == 'before') {
                    $content = $prev_next_links.$content;
                }
                if($tutorial_maker_options['wp_tutorial_maker_nextprev'] == 'after') {
                    $content .= $prev_next_links;
                }

                if($tutorial_maker_options['wp_tutorial_maker_show_category_index']) {
                    $link_to_category = get_category_link( $tutorial_maker_options['category_id'] );
                    $html = trim("<div id='wptm_before_category_link_text'>".
                                "{$tutorial_maker_options['wp_tutorial_maker_text_category_list']}".
                            "</div>");
                    $html .= trim("<div class='wptm_link_to_category'><a href='$link_to_category'>".
                                "{$tutorial_maker_options['wp_tutorial_maker_text_category_link_name']}".
                            "</div></a>");

                    $content .= $html;

                }

            }

        }
        return $content;
    }

    /**
     *
     * checking if the category is a tutorial category
     *
     * @param int $id
     * @return bool
     */

    private function test_if_in_tutorial_category( $id = 0 ) {

        $categories = wp_get_post_categories( $id );
        $wp_tutorial_maker_decider = get_option( self::$plugin_slug );
        foreach( $categories as $category_id ) {
            if( !empty( $wp_tutorial_maker_decider[$category_id] ) ) {
                $wp_tutorial_maker_decider[$category_id]['category_id'] = $category_id; //adding category ID
                return $wp_tutorial_maker_decider[$category_id];
            }
        }
        return false;

    }
}
