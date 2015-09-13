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
 * @package   wp-tutorial-maker
 * @author    Ran Bar-Zik <ran@bar-zik.com>
 */
class wp_tutorial_maker_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;



	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = wp_tutorial_maker::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();


		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'edited_term', array( $this, 'wp_tutorial_maker_option_update' ) );
        add_action( 'deleted_term_taxonomy', array( $this, 'wp_tutorial_maker_option_delete' ) );
		add_filter( 'edit_category_form', array( $this, 'tutorial_decider' ) );


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
	 * Register and enqueue admin-specific style sheet.
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

        if( isset($_GET['taxonomy']) && 'category' == $_GET['taxonomy'] ) {
            wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), wp_tutorial_maker::VERSION );
        }

    }


	/**
	 * Register and enqueue admin-specific JavaScript.
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
        if( isset($_GET['taxonomy']) && 'category' === $_GET['taxonomy'] ) {
            wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), wp_tutorial_maker::VERSION );
        }

	}

    /**
     *
     * action for updating the wp tutorial maker options from category edit page
     *
     * @param $term_id
     */
    public function wp_tutorial_maker_option_update($term_id) {

        if(!is_numeric($term_id)) {
            return '';
        }

        if (!isset( $_POST['tutorial_maker'] ) || ! wp_verify_nonce( $_POST['tutorial_maker'], 'submit_tutorial_category' )) {
            return '';
        }

        global $current_user;
        if('category' == $_POST['taxonomy'] && user_can( $current_user, 'manage_categories' ) ) {
            $wpmd = get_option($this->plugin_slug);;
            $wpmd[$term_id]['wptm'] = sanitize_text_field( $_POST['wp_tutorial_maker'] );
            //only 0, before or after - white label it
            switch($_POST['wp_tutorial_maker_nextprev']) {
                case 'before':
                    $wpmd[$term_id]['wp_tutorial_maker_nextprev'] = 'before';
                    break;
                case 'after':
                    $wpmd[$term_id]['wp_tutorial_maker_nextprev'] = 'after';
                    break;
                default:
                    $wpmd[$term_id]['wp_tutorial_maker_nextprev'] = 0;
                    break;

            }

            $wpmd[$term_id]['wp_tutorial_maker_nextprev'] = sanitize_text_field ( $_POST['wp_tutorial_maker_nextprev'] );
            $wpmd[$term_id]['wp_tutorial_maker_next_text'] = sanitize_text_field( $_POST['wp_tutorial_maker_next_text'] );
            $wpmd[$term_id]['wp_tutorial_maker_prev_text'] = sanitize_text_field( $_POST['wp_tutorial_maker_prev_text'] );
            //only 0 or 1
            if( 1 == $_POST['wp_tutorial_maker_show_category_index']) {
                $wpmd[$term_id]['wp_tutorial_maker_show_category_index'] = 1;
            } else {
                $wpmd[$term_id]['wp_tutorial_maker_show_category_index'] = 0;
            }
            $wpmd[$term_id]['wp_tutorial_maker_text_category_list'] = sanitize_text_field( $_POST['wp_tutorial_maker_text_category_list'] );
            $wpmd[$term_id]['wp_tutorial_maker_text_category_link_name'] = sanitize_text_field ( $_POST['wp_tutorial_maker_text_category_link_name'] );

            update_option($this->plugin_slug, $wpmd);

        }
	}

    /**
     *
     * action for removing wp tutorial maker options when deleting category
     *
     * @param $term_id
     */

    public function wp_tutorial_maker_option_delete($term_id) {
        global $current_user;

        if($_POST['taxonomy'] == 'category' && user_can( $current_user, 'manage_categories' )) {
            $wp_tutorial_maker_decider = get_option($this->plugin_slug);
            unset($wp_tutorial_maker_decider[$term_id]);
            update_option($this->plugin_slug, $wp_tutorial_maker_decider);
        }
    }

    /**
     *
     * Filter for adding the wp tutorial maker form in category edit page.
     *
     * @param $tag
     */

    public function tutorial_decider($tag) {

        $wp_tutorial_maker_decider = get_option($this->plugin_slug);
        $wp_nonce = wp_nonce_field('submit_tutorial_category', 'tutorial_maker');
		$form = "<table class='form-table'><tbody>
        $wp_nonce
		<tr class='form-field'>
		<th scope='row'><label for='wp_tutorial_maker'>".__('Activate WP Tutorial Maker for this category?',$this->plugin_slug)."</label></th>
		<td>
		<select name='wp_tutorial_maker' id='wp_tutorial_maker' class='postform'>
            <option value='0' ".selected( $wp_tutorial_maker_decider[$tag->term_id]['wptm'], 0, false)." >".__('Tutorial Category Disabled',$this->plugin_slug)."</option>
            <option value='1' ".selected( $wp_tutorial_maker_decider[$tag->term_id]['wptm'], 1, false)." >".__('Tutorial Category Enabled',$this->plugin_slug)."</option>
        </select>
        </td>
        </tr>";

        $form .= "
        <tr class='form-field wp_tutorial_maker_more_options'>
		<th scope='row'><label for='wp_tutorial_maker_nextprev'>".__('Show Previous\\Next Links in posts?',$this->plugin_slug)."</label></th>
                    <td>
                    <select name='wp_tutorial_maker_nextprev' id='wp_tutorial_maker_nextprev' class='postform'>
                        <option value='0' ".selected( $wp_tutorial_maker_decider[$tag->term_id]['wp_tutorial_maker_nextprev'], 0 , false).">".__('Do Not show previous or next links',$this->plugin_slug)."</option>
                        <option value='before' ".selected( $wp_tutorial_maker_decider[$tag->term_id]['wp_tutorial_maker_nextprev'], 'before', false ).">".__('Show previous or next links before the post',$this->plugin_slug)."</option>
                        <option value='after' ".selected( $wp_tutorial_maker_decider[$tag->term_id]['wp_tutorial_maker_nextprev'], 'after', false ).">".__('Show previous or next links after the post',$this->plugin_slug)."</option>
                    </select>
                    </td>
           </tr>";
        $form .= "
        <tr class='form-field wp_tutorial_maker_more_options'>
        <th scope='row'><label for='wp_tutorial_maker_next_text'>".__('Text For Next Article Link',$this->plugin_slug)."</label></th>
        <td>
            <input id='wp_tutorial_maker_next_text' name='wp_tutorial_maker_next_text' placeholder='".__('Next Tutorial Article',$this->plugin_slug)."' value='".sanitize_text_field( $wp_tutorial_maker_decider[$tag->term_id]['wp_tutorial_maker_next_text'] )."' />
        </td>
        </tr>
        <tr class='form-field wp_tutorial_maker_more_options'>
        <th scope='row'><label for='wp_tutorial_maker_prev_text'>".__('Text For Previous Article Link',$this->plugin_slug)."</label></th>
        <td>
             <input id='wp_tutorial_maker_prev_text' name='wp_tutorial_maker_prev_text' placeholder='".__('Previous Tutorial Article',$this->plugin_slug)."' value='".sanitize_text_field( $wp_tutorial_maker_decider[$tag->term_id]['wp_tutorial_maker_prev_text'] )."' />
        </td>
        </tr>";
        $form .= "
        <tr class='form-field wp_tutorial_maker_more_options'>
        <th scope='row'><label for='wp_tutorial_maker_show_category_index'>".__('Show links back to category?',$this->plugin_slug)."</label></th>
        <td>
        <select name='wp_tutorial_maker_show_category_index' id='wp_tutorial_maker_show_category_index' class='postform'>
                        <option value='0' ".selected( $wp_tutorial_maker_decider[$tag->term_id]['wp_tutorial_maker_show_category_index'], 0, false ).">".__('Do Not show all link to category in post',$this->plugin_slug)."</option>
                        <option value='1' ".selected( $wp_tutorial_maker_decider[$tag->term_id]['wp_tutorial_maker_show_category_index'], '1', false ).">".__('Show link to category in post',$this->plugin_slug)."</option>
                   </select>
        </td>
        </tr>";
        $form .= "
        <tr class='form-field wp_tutorial_maker_more_options'>
        <th scope='row'><label for='wp_tutorial_maker_text_category_link_name'>".__('Category Link Name',$this->plugin_slug)."</label></th>
        <td>
            <input id='wp_tutorial_maker_text_category_link_name' name='wp_tutorial_maker_text_category_link_name' placeholder='".__('Category Link Name',$this->plugin_slug)."' value='".sanitize_text_field( $wp_tutorial_maker_decider[$tag->term_id]['wp_tutorial_maker_text_category_link_name'] )."' />
        </td>
        </tr>";
        $form .= "
        <tr class='form-field wp_tutorial_maker_more_options'>
        <th scope='row'><label for='wp_tutorial_maker_text_category_list'>".__('Text To Present Before Link To Category',$this->plugin_slug)."</label></th>
        <td>
            <textarea id='wp_tutorial_maker_text_category_list' name='wp_tutorial_maker_text_category_list' placeholder='".__('Text To Present Before Link To Category',$this->plugin_slug)."'>".sanitize_text_field( $wp_tutorial_maker_decider[$tag->term_id]['wp_tutorial_maker_text_category_list'] )."</textarea>
        </td>
        </tr>";

        $form .="</tbody></table>";

        print $form;
	}



}
