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

    /**
     * @var UM_Customize
     */
    public $um_customize;

    /**
     * @var OceanWP_Customize
     */
    public $oceanwp_customize;

    /**
     * @var string
     */
    static public $post_type_wrestler = 'kwl_wrestlers';

    /**
     * @var string
     */
    static public $post_type_league = 'kwl_leagues';

    /**
     * @var string
     */
    static public $taxonomy_leagues_category = 'kwl_league_category';

    /**
     * @var string
     */
    static public $tax_term_leagues_public = 'kwl_public';

    /**
     * @var string
     */
    static public $tax_term_leagues_private = 'kwl_private';

    /**
     * @var string
     */
    static public $taxonomy_wrestlers_brand = 'kwl_wrestlers_brand';

    /**
     * @var string
     */
    static public $taxonomy_wrestlers_tags = 'kwl_wrestlers_tags';

    /**
     * @var string
     */
    static public $tax_term_wrestlers_smackdown = 'kwl_smackdown';

    /**
     * @var string
     */
    static public $tax_term_wrestlers_raw = 'kwl_raw';

    /**
     * @var string
     */
    static public $tax_term_wrestlers_205live = 'kwl_205live';

    /**
     * @var string
     */
    static public $tax_term_wrestlers_superstar = 'kwl_superstar';

    /**
     * @var string
     */
    static public $tax_term_wrestlers_nxt = 'kwl_nxt';


    // Meta box fields for Leagues custom post type
    /**
     * @var string : limit count of teams for league.
     */
    static public $limit_teams_league_meta = 'limit_teams_league-meta';

    /**
     * @var string : limit count of wrestlers of team for league.
     */
    static public $limit_wrestlers_team_meta = 'limit_wrestlers_team-meta';

    /**
     * @var string : list of teams for league.
     */
    static public $teams_of_league_meta = 'teams_of_league-meta';

    /**
     * @var string : commissioner for league
     */
    static public $league_commissioner_meta = 'league_commissioner-meta';

    // Meta box fields for Wrestlers custom post type

    static public $wrestler_brand_meta = 'wrestler_brand-meta';

    public static $wrestler_images_meta = 'wrestler_images-meta';

    public $brand_logos;

    public $brand_back_images;

    /**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'wrestlers-leagues';

        $this->brand_logos = array(
            Wrestlers_Leagues::$tax_term_wrestlers_205live => 'https://lds.sos4digitalmarketing.com/wp-content/uploads/2019/03/1-205-a.png',
            Wrestlers_Leagues::$tax_term_wrestlers_raw => 'https://lds.sos4digitalmarketing.com/wp-content/uploads/2019/03/Raw-transparent-background.png',
            Wrestlers_Leagues::$tax_term_wrestlers_smackdown => 'https://lds.sos4digitalmarketing.com/wp-content/uploads/2019/03/smackdown_600x300.png',
            Wrestlers_Leagues::$tax_term_wrestlers_superstar => 'https://lds.sos4digitalmarketing.com/wp-content/uploads/2019/04/Wrestling-image-04.05.19.png',
            Wrestlers_Leagues::$tax_term_wrestlers_nxt => 'https://lds.sos4digitalmarketing.com/wp-content/uploads/2019/03/NXT_logo.png',
        );
        $this->brand_back_images = array(
            Wrestlers_Leagues::$tax_term_wrestlers_205live => 'https://lds.sos4digitalmarketing.com/wp-content/uploads/2019/03/205-live-logo.png',
            Wrestlers_Leagues::$tax_term_wrestlers_raw => 'https://lds.sos4digitalmarketing.com/wp-content/uploads/2019/03/Raw-Logo.png',
            Wrestlers_Leagues::$tax_term_wrestlers_smackdown => 'https://lds.sos4digitalmarketing.com/wp-content/uploads/2019/03/Smackdown-logo.png',
            Wrestlers_Leagues::$tax_term_wrestlers_superstar => 'https://lds.sos4digitalmarketing.com/wp-content/uploads/2019/03/Raw-Logo.png',
            Wrestlers_Leagues::$tax_term_wrestlers_nxt => 'https://lds.sos4digitalmarketing.com/wp-content/uploads/2019/03/nxt-logo-1.png',
        );

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $this->script_suffix = '';

		$this->um_customize = new UM_Customize();
		$this->oceanwp_customize = new OceanWP_Customize();

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
     * @param  string $post_type Post type name
     * @param  string $plural Post type item plural name
     * @param  string $single Post type item single name
     * @param string $enter_title Helper title text for create page of admin area
     * @param  string $description Description of post type
     * @param array $options
     * @return object Post type class object
     */
	public function register_post_type ( $post_type = '', $plural = '', $single = '', $enter_title = 'Enter Title', $description = '', $options = array() ) {

		if ( ! $post_type || ! $plural || ! $single ) return;

		$post_type = new Wrestlers_Leagues_Post_Type( $post_type, $plural, $single, $description, $enter_title, $options );

		return $post_type;
	}


    /**
     * Wrapper function to register a new taxonomy
     * @param  string $taxonomy Taxonomy name
     * @param  string $plural Taxonomy single name
     * @param  string $single Taxonomy plural name
     * @param  array $post_types Post types to which this taxonomy applies
     * @param array $terms
     * @param array $taxonomy_args
     * @return object Taxonomy class object
     */
	public function register_taxonomy ( $taxonomy = '', $plural = '', $single = '', $post_types = array(), $terms = array(), $taxonomy_args = array() ) {

		if ( ! $taxonomy || ! $plural || ! $single ) return;

		$taxonomy = new Wrestlers_Leagues_Taxonomy( $taxonomy, $plural, $single, $post_types, $terms, $taxonomy_args );

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
        wp_register_style( 'bootstrap-4.0', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css', array(), '4.0.0' );
        wp_enqueue_style( 'bootstrap-4.0' );
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

        if ($post->post_type == Wrestlers_Leagues::$post_type_wrestler){
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

        $this->register_post_type('kwl_wrestlers', 'Wrestlers', 'Wrestler', 'Wrestler Name');

        $this->register_post_type('kwl_leagues', 'Leagues', 'League', 'League Name');


        add_filter('single_template', array($this, 'load_wl_template'));

        add_action( 'add_meta_boxes', array( $this, 'add_metabox'));
        add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );

    }

    /**
     *
     */
    private function _register_taxonomies(){
        $leagues_terms = array(
            array(
                'name'=>'Public',
                'slug'=>Wrestlers_Leagues::$tax_term_leagues_public,
                'description'=>'This league is visible to everyone.'
            ),
            array(
                'name'=>'Private',
                'slug'=>Wrestlers_Leagues::$tax_term_leagues_private,
                'description'=>'This league is visible to only team members.'
            )
        );
        $this->register_taxonomy(Wrestlers_Leagues::$taxonomy_leagues_category, 'Categories', 'Category', array(Wrestlers_Leagues::$post_type_league), $leagues_terms);

        $wrestlers_terms = [
            [
                'name'=>'205live',
                'slug'=>Wrestlers_Leagues::$tax_term_wrestlers_205live,
                'description'=>'205 Live.'
            ],
            [
                'name'=>'Raw',
                'slug'=>Wrestlers_Leagues::$tax_term_wrestlers_raw,
                'description'=>'Raw.'
            ],
            [
                'name'=>'Smackdown',
                'slug'=>Wrestlers_Leagues::$tax_term_wrestlers_smackdown,
                'description'=>'Smack down.'
            ],
            [
                'name'=>'Superstar',
                'slug'=>Wrestlers_Leagues::$tax_term_wrestlers_superstar,
                'description'=>'Super star.'
            ],
            [
                'name'=>'NXT',
                'slug'=>Wrestlers_Leagues::$tax_term_wrestlers_nxt,
                'description'=>'NXT.'
            ],
        ];
        $this->register_taxonomy(Wrestlers_Leagues::$taxonomy_wrestlers_brand, 'Brands', 'Brand', array(Wrestlers_Leagues::$post_type_wrestler), $wrestlers_terms);
        $wrestlers_tags_terms = [
            ['name'=>'A', 'slug'=>'a'],
            ['name'=>'B', 'slug'=>'b'],
            ['name'=>'C', 'slug'=>'c'],
            ['name'=>'D', 'slug'=>'d'],
            ['name'=>'E', 'slug'=>'e'],
            ['name'=>'F', 'slug'=>'f'],
            ['name'=>'G', 'slug'=>'g'],
            ['name'=>'H', 'slug'=>'h'],
            ['name'=>'I', 'slug'=>'i'],
            ['name'=>'J', 'slug'=>'j'],
            ['name'=>'K', 'slug'=>'k'],
            ['name'=>'L', 'slug'=>'l'],
            ['name'=>'M', 'slug'=>'m'],
            ['name'=>'N', 'slug'=>'n'],
            ['name'=>'O', 'slug'=>'o'],
            ['name'=>'P', 'slug'=>'p'],
            ['name'=>'Q', 'slug'=>'q'],
            ['name'=>'R', 'slug'=>'r'],
            ['name'=>'S', 'slug'=>'s'],
            ['name'=>'T', 'slug'=>'t'],
            ['name'=>'U', 'slug'=>'u'],
            ['name'=>'V', 'slug'=>'v'],
            ['name'=>'W', 'slug'=>'w'],
            ['name'=>'X', 'slug'=>'x'],
            ['name'=>'Y', 'slug'=>'y'],
            ['name'=>'Z', 'slug'=>'z'],
        ];
        $this->register_taxonomy(Wrestlers_Leagues::$taxonomy_wrestlers_tags, 'Tags', 'Tag',
            [Wrestlers_Leagues::$post_type_wrestler], $wrestlers_tags_terms, ['hierarchical' => false]);
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

    /**
     *
     */
    private function _register_post_handlers(){
        ! is_admin() and add_action('init', array($this, 'post_create_league'));
        ! is_admin() and add_action('init', array($this, 'post_join_league'));
    }

    /**
     *
     */
    public function post_create_league(){
        if ( !empty($_POST['action']) && $_POST['action'] === 'create_league' ) {

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
            $private_term_id = term_exists( Wrestlers_Leagues::$tax_term_leagues_private, Wrestlers_Leagues::$taxonomy_leagues_category);
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
            $count = $wpdb->insert("{$wpdb->prefix}kwl_league_user", $league_user);
            error_log("\nwrestler plugin: create league $count.");

            if(isset($_POST['friends']) && class_exists("UM_Friends_API")){
                $this->um_customize->invite_friends($_POST['friends'], $user_id, "Please join to league.");
            }
            header("Location: ".home_url("/user-profile"));
            die();
        }
    }

    /**
     *
     */
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
            $wpdb->insert("{$wpdb->prefix}kwl_league_user", $league_user);

            if(isset($_POST['friends']) && class_exists("UM_Friends_API")){
                $this->um_customize->invite_friends($_POST['friends'], $user_id, "Please join to league.");
            }

            header("Location: ".home_url("/user-profile"));
            die();
        }
    }

    /**
     *
     */
    public function add_metabox(){
        // register meta boxes for Leagues
        add_meta_box(Wrestlers_Leagues::$limit_teams_league_meta, "Limit of teams per league", array($this, "limit_teams_league"), Wrestlers_Leagues::$post_type_league, "normal", "low");
        add_meta_box(Wrestlers_Leagues::$limit_wrestlers_team_meta, "Limit of wrestlers per team", array($this, "limit_wrestlers_team"), Wrestlers_Leagues::$post_type_league, "normal", "low");
        add_meta_box(Wrestlers_Leagues::$teams_of_league_meta, "Teams of this league", array($this, "teams_of_league"), Wrestlers_Leagues::$post_type_league, "normal", "low");
        add_meta_box(Wrestlers_Leagues::$league_commissioner_meta, "Commissioner of this league", array($this, "commissioner_of_league"), Wrestlers_Leagues::$post_type_league, "normal", "low");

        // register meta boxes for Wrestlers
//        add_meta_box(Wrestlers_Leagues::$wrestler_brand_meta, "Brand of wrestler", array($this, "wrestler_brand"), Wrestlers_Leagues::$post_type_wrestler, "normal", "low");
        add_meta_box(Wrestlers_Leagues::$wrestler_images_meta, "Images of wrestler", array($this, "wrestler_images"), Wrestlers_Leagues::$post_type_wrestler, "normal", "low");
    }

    /**
     *
     */
    public function limit_teams_league(){
        global $post;
        $custom = get_post_custom($post->ID);
        $limit_teams_league_meta = $custom[Wrestlers_Leagues::$limit_teams_league_meta][0];
        ?>
        <input type="number" min="2" max="10" style="width:200px;" name="limit_teams_league_meta" value="<?php echo $limit_teams_league_meta; ?>" />
        <?php
    }

    /**
     *
     */
    public function limit_wrestlers_team(){
        global $post;
        $custom = get_post_custom($post->ID);
        $limit_wrestlers_team_meta = $custom[Wrestlers_Leagues::$limit_wrestlers_team_meta][0];
        ?>
        <input type="number" min="4" max="10" style="width:200px;" name="limit_teams_league_meta" value="<?php echo $limit_wrestlers_team_meta; ?>" />
        <?php
    }

    /**
     *
     */
    public function teams_of_league(){
        global $post;
        if($post->post_type != Wrestlers_Leagues::$post_type_league){
            return;
        }
        $custom = get_post_custom($post->ID);
//        $teams_of_league_meta = $custom[$this->teams_of_league_meta][0];
        global $wpdb;
        $query = "SELECT l.team_name, u.display_name FROM {$wpdb->prefix}kwl_league_user AS l "
            ."JOIN {$wpdb->prefix}users AS u ON l.user_id=u.ID "
            ."WHERE l.league_id={$post->ID}";
        $teams = $wpdb->get_results($query);
        ?>
        <label>Teams of this league:</label>
        <select multiple="" size="6" name="<?=Wrestlers_Leagues::$teams_of_league_meta?>">
            <?php
            foreach ($teams as $row):
            ?>
            <option value="1"><a href="/"><?=$row->team_name?> (<?=$row->display_name?>)</a></option>
            <?php
            endforeach;
            ?>
        </select>
        <?php
    }

    /**
     *
     */
    public function commissioner_of_league(){
        global $post;
        $custom = get_post_custom($post->ID);
        $league_commissioner_id = $custom[Wrestlers_Leagues::$league_commissioner_meta][0];
        $commissioner = get_user_by('ID', $league_commissioner_id);
        echo "<label>{$commissioner->display_name}</label>";
    }

    public function wrestler_brand(){

    }

    public function wrestler_images(){
        global $post_id;
        wp_nonce_field( Wrestlers_Leagues::$wrestler_images_meta, Wrestlers_Leagues::$wrestler_images_meta.'_nonce' );

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        echo $this->wrestler_image_uploader_field( Wrestlers_Leagues::$wrestler_images_meta, get_post_meta($post_id, Wrestlers_Leagues::$wrestler_images_meta, true) );

    }

    public function wrestler_image_uploader_field( $name, $value = '' ) {

        $image = 'Upload Image';
        $button = 'button';
        $image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
        $display = 'none'; // display state of the "Remove image" button

        ?>

        <p><i>Add Images for Wrestler</i></p>

        <label>
            <div class="gallery-screenshot clearfix" style="display: flex;">
                <?php
                {
                    $ids = explode(',', $value);
                    foreach ($ids as $attachment_id) {
                        $img = wp_get_attachment_image_src($attachment_id, 'thumbnail');
                        echo '<div class="screen-thumb"><img src="' . esc_url($img[0]) . '" /></div>';
                    }
                }
                ?>
            </div>

            <input id="edit-gallery" class="button upload_gallery_button" type="button"
                   value="Add/Edit Gallery"/>
            <input id="clear-gallery" class="button upload_gallery_button" type="button"
                   value="Clear"/>
            <input type="hidden" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($name); ?>" class="gallery_values" value="<?php echo esc_attr($value); ?>">
        </label>
        <?php
    }

    /**
     *
     */
    public function save_metabox(){
        global $post;
        if(is_object($post) && $post->post_type == Wrestlers_Leagues::$post_type_league){
            update_post_meta($post->ID, Wrestlers_Leagues::$limit_teams_league_meta, $_POST[Wrestlers_Leagues::$limit_teams_league_meta]);
            update_post_meta($post->ID, Wrestlers_Leagues::$limit_wrestlers_team_meta, $_POST[Wrestlers_Leagues::$limit_wrestlers_team_meta]);
        }
        elseif(is_object($post) && $post->post_type == Wrestlers_Leagues::$post_type_wrestler){
            if ( !isset( $_POST[Wrestlers_Leagues::$wrestler_images_meta.'_nonce'] ) ) {
                return $post->ID;
            }

            if ( !wp_verify_nonce( $_POST[Wrestlers_Leagues::$wrestler_images_meta.'_nonce'], Wrestlers_Leagues::$wrestler_images_meta) ) {
                return $post->ID;
            }

            if ( isset( $_POST[ Wrestlers_Leagues::$wrestler_images_meta] ) ) {
                update_post_meta( $post->ID, Wrestlers_Leagues::$wrestler_images_meta, esc_attr($_POST[Wrestlers_Leagues::$wrestler_images_meta]) );
            } else {
                update_post_meta( $post->ID, Wrestlers_Leagues::$wrestler_images_meta, '' );
            }
        }
        return null;
    }

}