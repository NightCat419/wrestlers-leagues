<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Wrestlers_Leagues {

	/**
	 * The single instance of wrestlers-leagues.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	public $um_customize;

	static public $post_type_wrestler = 'kwl_wrestlers';

    static public $post_type_league = 'kwl_leagues';

    static public $taxonomy_leagues_category = 'kwl_league_category';

    static public $tax_term_leagues_public = 'kwl_public';

    static public $tax_term_leagues_private = 'kwl_private';

    static public $limit_teams_league_meta = 'limit_teams_league-meta';

    static public $limit_wrestlers_team_meta = 'limit_wrestlers_team-meta';

    static public $teams_of_league_meta = 'teams_of_league-meta';

    static public $league_commissioner_meta = 'league_commissioner-meta';

    /**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'wrestlers-leagues';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$this->um_customize = new UM_Customize();

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Load API for generic admin functions
		if ( is_admin() ) {
			$this->admin = new Wrestlers_Leagues_Admin_API();
		}

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()

	/**
	 * Wrapper function to register a new post type
	 * @param  string $post_type   Post type name
	 * @param  string $plural      Post type item plural name
	 * @param  string $single      Post type item single name
	 * @param  string $description Description of post type
	 * @return object              Post type class object
	 */
	public function register_post_type ( $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {

		if ( ! $post_type || ! $plural || ! $single ) return;

		$post_type = new Wrestlers_Leagues_Post_Type( $post_type, $plural, $single, $description, $options );

		return $post_type;
	}


    /**
	 * Wrapper function to register a new taxonomy
	 * @param  string $taxonomy   Taxonomy name
	 * @param  string $plural     Taxonomy single name
	 * @param  string $single     Taxonomy plural name
	 * @param  array  $post_types Post types to which this taxonomy applies
	 * @return object             Taxonomy class object
	 */
	public function register_taxonomy ( $taxonomy = '', $plural = '', $single = '', $post_types = array(), $taxonomy_args = array() ) {

		if ( ! $taxonomy || ! $plural || ! $single ) return;

		$taxonomy = new Wrestlers_Leagues_Taxonomy( $taxonomy, $plural, $single, $post_types, $taxonomy_args );

		return $taxonomy;
	}

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );
	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'wrestlers-leagues', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'wrestlers-leagues';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main wrestlers-leagues Instance
	 *
	 * Ensures only one instance of wrestlers-leagues is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see wrestlers-leagues()
	 * @return Main wrestlers-leagues instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();

        $this->_register_post_types();
        $this->_register_taxonomies();
        $this->_register_shortcodes();
        $this->_register_post_handlers();

        $this->um_customize->init_profile_tabs();

    } // End install ()


	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

    /**
     * @param $template
     * @return string
     */
    public function load_wl_template($template) {
        global $post;

        if ($post->post_type == "kwl_wrestlers" && $template !== locate_template(array("single-movie.php"))){
            /* This is a "movie" post
             * AND a 'single movie template' is not found on
             * theme or child theme directories, so load it
             * from our plugin directory
             */
            return WP_KWL_PLUGIN_DIR . "/templates/single-wrestler-template.php";
        }

        return $template;
    }

    /**
     * register all required post types and its templates.
     */
    private function _register_post_types(){

        $this->register_post_type('kwl_wrestlers', 'Wrestlers', 'Wrestler');

        $this->register_post_type('kwl_leagues', 'Leagues', 'League');

//        add_filter('single_template', 'load_wl_template');

        add_action( 'add_meta_boxes', array( $this, 'add_metabox'));
        add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );

    }

    private function _register_taxonomies(){
        $this->register_taxonomy(Wrestlers_Leagues::$taxonomy_leagues_category, 'Categories', 'Category', array(Wrestlers_Leagues::$post_type_league));

    }

    /**
     * register all required shortcodes
     */
    private function _register_shortcodes(){
        if ( ! is_admin() ) {
            add_shortcode( 'wl-create-leagues-form', array( $this, 'wl_create_leagues_form_shortcode' ) );
            add_shortcode( 'wl-join-leagues-form', array( $this, 'wl_join_leagues_form_shortcode' ) );
        }
    }

    /**
     * Handle Shortcode [wl-create-leagues-form].
     * @param $org_shortcode_atts
     * @return mixed
     */
    public function wl_create_leagues_form_shortcode($org_shortcode_atts){
        ob_start();
        include WP_KWL_PLUGIN_DIR . "/templates/create-leagues-form.php";
        $output = ob_get_clean();
        return $output;
    }

    /**
     * Handle Shortcode [wl-join-leagues-form].
     * @param $org_shortcode_atts
     * @return mixed
     */
    public function wl_join_leagues_form_shortcode($org_shortcode_atts){
        ob_start();
        include WP_KWL_PLUGIN_DIR . "/templates/join-leagues-form.php";
        $output = ob_get_clean();
        return $output;
    }

    private function _register_post_handlers(){
        ! is_admin() and add_action('init', array($this, 'post_create_league'));
        ! is_admin() and add_action('init', array($this, 'post_join_league'));
    }

    public function post_create_league(){
        if ( !empty($_POST['action']) && $_POST['action'] === 'create_league' ) {

            // insert terms if not exists
            $public_term_id = term_exists( Wrestlers_Leagues::$tax_term_leagues_public, Wrestlers_Leagues::$taxonomy_leagues_category);
            if(!$public_term_id){
                $public_term_id = wp_insert_term(
                    'Public', // the term
                    Wrestlers_Leagues::$taxonomy_leagues_category, // the taxonomy
                    array(
                        'description' => 'This league is visible to everyone.',
                        'slug' => Wrestlers_Leagues::$tax_term_leagues_public,
                    )
                );
            }
            $private_term_id = term_exists( Wrestlers_Leagues::$tax_term_leagues_private, Wrestlers_Leagues::$taxonomy_leagues_category);
            if(!$private_term_id){
                $private_term_id = wp_insert_term(
                    'Private', // the term
                    Wrestlers_Leagues::$taxonomy_leagues_category, // the taxonomy
                    array(
                        'description'=> 'This league is visible to only league members.',
                        'slug' => Wrestlers_Leagues::$tax_term_leagues_private,
                    )
                );
            }


            // Create post object
            $my_post = array(
                'post_title'    => wp_strip_all_tags( $_POST['lg_name'] ),
                'post_status'   => 'publish',
                'post_type' => Wrestlers_Leagues::$post_type_league,
                'tax_input' => array(
                    Wrestlers_Leagues::$taxonomy_leagues_category => Wrestlers_Leagues::$tax_term_leagues_public
                )
            );

            // Insert the post into the database
            $post_id = wp_insert_post( $my_post );
            $user_id = get_current_user_id();
            $user = wp_get_current_user();
            wp_set_post_terms($post_id, $private_term_id['term_id'], Wrestlers_Leagues::$taxonomy_leagues_category);

            if($post_id){
                update_post_meta($post_id, Wrestlers_Leagues::$limit_teams_league_meta, $_POST["num_teams"]);
                update_post_meta($post_id, Wrestlers_Leagues::$limit_wrestlers_team_meta, $_POST["num_wrestlers"]);
                update_post_meta($post_id, Wrestlers_Leagues::$league_commissioner_meta, $user_id);
            }

            global $wpdb;
            $league_user = array(
                'league_id' => $post_id,
                'user_id' => $user_id,
                'team_name' => $user->display_name,
                'date_join' => time(),
                'is_commissioner' => 1,
                'status' => 1,

            );
            $wpdb->insert('wp_kwl_league_user', $league_user);

            if(isset($_POST['friends']) && class_exists("UM_Friends_API")){
                $this->um_customize->invite_friends($_POST['friends'], $user_id, "Please join to league.");
//                foreach ($_POST['friends'] as $friendid){
//                    UM()->Friends_API()->api()->add($friendid, $user_id);
//                    do_action('um_friends_after_user_friend_request', $friendid, $user_id );
//                }
            }
            header("Location: ".home_url("/user-profile"));
            die();
//            wp_die();
        }
    }

    public function post_join_league(){
        if ( !empty($_POST['action']) && $_POST['action'] === 'join_league' ) {
            global $wpdb;
            $user_id = get_current_user_id();
            $league_user = array(
                'league_id' => $_POST['league'],
                'user_id' => $user_id,
                'team_name' => $_POST['team_name'],
                'date_join' => time(),
                'is_commissioner' => 0,
                'status' => 0,

            );
            $wpdb->insert('wp_kwl_league_user', $league_user);

            if(isset($_POST['friends']) && class_exists("UM_Friends_API")){
                $this->um_customize->invite_friends($_POST['friends'], $user_id, "Please join to league.");
            }

            header("Location: ".home_url("/user-profile"));
            die();
        }
    }

    public function add_metabox(){
        add_meta_box(Wrestlers_Leagues::$limit_teams_league_meta, "Limit of teams per league", array($this, "limit_teams_league"), Wrestlers_Leagues::$post_type_league, "normal", "low");
        add_meta_box(Wrestlers_Leagues::$limit_wrestlers_team_meta, "Limit of wrestlers per team", array($this, "limit_wrestlers_team"), Wrestlers_Leagues::$post_type_league, "normal", "low");
        add_meta_box(Wrestlers_Leagues::$teams_of_league_meta, "Teams of this league", array($this, "teams_of_league"), Wrestlers_Leagues::$post_type_league, "normal", "low");
        add_meta_box(Wrestlers_Leagues::$league_commissioner_meta, "Commissioner of this league", array($this, "commissioner_of_league"), Wrestlers_Leagues::$post_type_league, "normal", "low");

    }

    public function limit_teams_league(){
        global $post;
        $custom = get_post_custom($post->ID);
        $limit_teams_league_meta = $custom[Wrestlers_Leagues::$limit_teams_league_meta][0];
        ?>
        <input type="number" min="2" max="10" style="width:200px;" name="limit_teams_league_meta" value="<?php echo $limit_teams_league_meta; ?>" />
        <?php
    }

    public function limit_wrestlers_team(){
        global $post;
        $custom = get_post_custom($post->ID);
        $limit_wrestlers_team_meta = $custom[Wrestlers_Leagues::$limit_wrestlers_team_meta][0];
        ?>
        <input type="number" min="4" max="10" style="width:200px;" name="limit_teams_league_meta" value="<?php echo $limit_wrestlers_team_meta; ?>" />
        <?php
    }

    public function teams_of_league(){
        global $post;
        $custom = get_post_custom($post->ID);
//        $teams_of_league_meta = $custom[$this->teams_of_league_meta][0];
        ?>
        <label>Teams of this league:</label>
        <select multiple="" size="6" name="<?=Wrestlers_Leagues::$teams_of_league_meta?>">
            <option value="1"><a href="/">Team1</a></option>
            <option value="1"><a href="/">Team2</a></option>
            <option value="1"><a href="/">Team3</a></option>
        </select>
        <?php
    }

    public function commissioner_of_league(){
        global $post;
        $custom = get_post_custom($post->ID);
        $league_commissioner_id = $custom[Wrestlers_Leagues::$league_commissioner_meta][0];
        $commissioner = get_user_by('ID', $league_commissioner_id);
        echo "<label>{$commissioner->display_name}</label>";
    }

    public function save_metabox(){
        global $post;
        update_post_meta($post->ID, Wrestlers_Leagues::$limit_teams_league_meta, $_POST[Wrestlers_Leagues::$limit_teams_league_meta]);
        update_post_meta($post->ID, Wrestlers_Leagues::$limit_wrestlers_team_meta, $_POST[Wrestlers_Leagues::$limit_wrestlers_team_meta]);
    }
}