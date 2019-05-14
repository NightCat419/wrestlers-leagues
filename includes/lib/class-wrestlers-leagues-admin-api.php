<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Matches_List extends WP_List_Table
{

    /** Class constructor */
    public function __construct()
    {

        parent::__construct([
            'singular' => __('Match', 'sp'), //singular name of the listed records
            'plural' => __('Matches', 'sp'), //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ]);

    }


    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_matches($per_page = 5, $page_number = 1)
    {

        global $wpdb;

        $sql = "SELECT m.*, w.post_title AS winner_name, l.post_title AS loser_name FROM {$wpdb->prefix}kwl_matches AS m "
            . "JOIN {$wpdb->prefix}posts AS w ON m.winner_id=w.ID "
            . "JOIN {$wpdb->prefix}posts AS l ON m.loser_id=l.ID";

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;


        $result = $wpdb->get_results($sql, 'ARRAY_A');
        foreach ($result as $key => &$item) {
            $item['dateline'] = date('Y-m-d', $item['dateline']);
        }

        return $result;
    }


    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_match($id)
    {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}kwl_matches",
            ['ID' => $id],
            ['%d']
        );
    }


    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count()
    {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}kwl_matches";

        return $wpdb->get_var($sql);
    }


    /** Text displayed when no customer data is available */
    public function no_items()
    {
        _e('No matches available.', 'sp');
    }


    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'ID':
            case 'winner_id':
            case 'loser_id':
            case 'name':
            case 'winner_name':
            case 'winner_points':
            case 'loser_name':
            case 'loser_points':
            case 'dateline':
            case 'desc':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name($item)
    {

        $delete_nonce = wp_create_nonce('sp_delete_match');

        $title = '<strong>' . $item['name'] . '</strong>';

        $actions = [
            'delete' => sprintf('<a href="?page=%s&action=%s&match=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['ID']), $delete_nonce)
        ];

        return $title . $this->row_actions($actions);
    }


    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns()
    {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'name' => __('Name', 'sp'),
            'winner_name' => __('Winner Name', 'sp'),
            'winner_points' => __('Winner Points', 'sp'),
            'loser_name' => __('Loser Name', 'sp'),
            'loser_points' => __('Loser Points', 'sp'),
            'desc' => __('Description', 'sp'),
            'dateline' => __('Date', 'sp'),
        ];

        return $columns;
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'name' => array('name', true),
            'winner_name' => array('winner_name', true),
            'dateline' => array('dateline', true),
            'loser_name' => array('loser_name', true),
        );

        return $sortable_columns;
    }

    protected function column_winner_name($item)
    {
        $wrestler_url = get_post_permalink($item['winner_id']);
        $wrestler_url = esc_url($wrestler_url);
        // similarly add row actions for add usermeta.
        $row_value = '<a href="' . $wrestler_url . '" target="_blank"><strong>' . $item['winner_name'] . '</strong></a>';
        return $row_value;
    }

    protected function column_loser_name($item)
    {
        $wrestler_url = get_post_permalink($item['loser_id']);
        $wrestler_url = esc_url($wrestler_url);
        // similarly add row actions for add usermeta.
        $row_value = '<a href="' . $wrestler_url . '" target="_blank"><strong>' . $item['loser_name'] . '</strong></a>';
        return $row_value;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = [
            'bulk-delete' => 'Delete'
        ];

        return $actions;
    }


    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items()
    {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('matches_per_page', 5);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ]);

        $this->items = self::get_matches($per_page, $current_page);
    }

    public function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, 'sp_delete_match')) {
                die('Go get a life script kiddies');
            } else {
                self::delete_match(absint($_GET['match']));

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                wp_redirect(esc_url_raw(add_query_arg()));
                exit;
            }

        }

        // If the delete bulk action is triggered
        if ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete')
            || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')
        ) {

            $delete_ids = esc_sql($_POST['bulk-delete']);

            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_match($id);

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            wp_redirect(esc_url_raw(add_query_arg()));
            exit;
        }
        if (isset($_POST['action']) && $_POST['action'] == 'add') {
            global $wpdb;
            $wpdb->insert($wpdb->prefix . 'kwl_matches', array(
                'name' => trim($_POST['match_name']),
                'winner_id' => $_POST['winner_id'],
                'winner_points' => $_POST['winner_points'],
                'loser_id' => $_POST['loser_id'],
                'loser_points' => $_POST['loser_points'],
                'dateline' => strtotime($_POST['dateline']),
                'desc' => $_POST['desc']
            ));
        }
    }

}

class Bonuses_List extends WP_List_Table
{

    /** Class constructor */
    public function __construct()
    {

        parent::__construct([
            'singular' => __('Bonus', 'sp'), //singular name of the listed records
            'plural' => __('Bonuses', 'sp'), //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ]);

    }


    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_bonuses($per_page = 5, $page_number = 1)
    {

        global $wpdb;

        $sql = "SELECT m.*, w.post_title AS wrestler_name FROM {$wpdb->prefix}kwl_bonuses AS m "
            . "JOIN {$wpdb->prefix}posts AS w ON m.wrestler_id=w.ID ";

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;


        $result = $wpdb->get_results($sql, 'ARRAY_A');
        foreach ($result as $key => &$item) {
            $item['dateline'] = date('Y-m-d', $item['dateline']);
        }

        return $result;
    }


    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_bonus($id)
    {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}kwl_bonuses",
            ['ID' => $id],
            ['%d']
        );
    }


    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count()
    {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}kwl_bonuses";

        return $wpdb->get_var($sql);
    }


    /** Text displayed when no customer data is available */
    public function no_items()
    {
        _e('No matches available.', 'sp');
    }


    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'ID':
            case 'wrestler_id':
            case 'wrestler_name':
            case 'bonus_points':
            case 'provider_name':
            case 'desc':
            case 'dateline':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name($item)
    {

        $delete_nonce = wp_create_nonce('sp_delete_bonus');

        $title = '<strong>' . $item['wrestler_name'] . '</strong>';

        $actions = [
            'delete' => sprintf('<a href="?page=%s&action=%s&bonus=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['ID']), $delete_nonce)
        ];

        return $title . $this->row_actions($actions);
    }


    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns()
    {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'wrestler_name' => __('Wrestler Name', 'sp'),
            'bonus_points' => __('Bonus Points', 'sp'),
            'provider_name' => __('Provider Name', 'sp'),
            'desc' => __('Description', 'sp'),
            'dateline' => __('Date', 'sp'),
        ];

        return $columns;
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'wrestler_name' => array('wrestler_name', true),
            'bonus_points' => array('bonus_points', true),
            'dateline' => array('dateline', true),
            'provider_name' => array('provider_name', true),
        );
        return $sortable_columns;
    }

    protected function column_wrestler_name($item)
    {
        $wrestler_url = get_post_permalink($item['wrestler_id']);
        $wrestler_url = esc_url($wrestler_url);
        // similarly add row actions for add usermeta.
        $row_value = '<a href="' . $wrestler_url . '" target="_blank"><strong>' . $item['wrestler_name'] . '</strong></a>';
        return $row_value;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = [
            'bulk-delete' => 'Delete'
        ];

        return $actions;
    }


    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items()
    {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('bonuses_per_page', 5);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ]);

        $this->items = self::get_bonuses($per_page, $current_page);
    }

    public function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, 'sp_delete_bonus')) {
                die('Go get a life script kiddies');
            } else {
                self::delete_bonus(absint($_GET['bonus']));

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                wp_redirect(esc_url_raw(add_query_arg()));
                exit;
            }

        }

        // If the delete bulk action is triggered
        if ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete')
            || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')
        ) {

            $delete_ids = esc_sql($_POST['bulk-delete']);

            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_bonus($id);

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            wp_redirect(esc_url_raw(add_query_arg()));
            exit;
        }
        if (isset($_POST['action']) && $_POST['action'] == 'add') {
            global $wpdb;
            $wpdb->insert($wpdb->prefix . 'kwl_bonuses', array(
                'wrestler_id' => $_POST['wrestler_id'],
                'bonus_points' => $_POST['bonus_points'],
                'provider_name' => trim($_POST['provider_name'], ". \t\n\r\0\x0B"),
                'desc' => trim($_POST['desc'], ". \t\n\r\0\x0B"),
                'dateline' => strtotime($_POST['dateline'])
            ));
        }
    }

}

class Wrestlers_Leagues_Admin_API
{

    public $matches_instance;

    public $bonuses_instance;

    /**
     * Constructor function
     */
    public function __construct()
    {
        add_action('save_post', array($this, 'save_meta_boxes'), 10, 1);

        add_filter('set-screen-option', [__CLASS__, 'set_screen'], 10, 3);
        add_action('admin_menu', [$this, 'plugin_menu']);

//        add_action( 'admin_init', [$this, 'init_wrestlers_only_once'] );
    }

    function init_wrestlers_only_once()
    {
//        update_option( 'init_wrestlers', 'not completed' );
//        if ( get_option( 'init_wrestlers' ) != 'completed' ) {

        $posts = get_posts(['post_type' => 'post', 'posts_per_page' => -1, 'numberposts' => -1]);
        $count = 0;
        $wrestler_cats = ["205-live", "raw", "smackdown", "superstars"];
        foreach ($posts as $p) {
            $wrestler_category = Wrestlers_Leagues::$tax_term_wrestlers_raw;
            $categories = get_the_category($p->ID);
            foreach ($categories as $category) {
                if ($category->slug == "205-live") {
                    $wrestler_category = Wrestlers_Leagues::$tax_term_wrestlers_205live;
                    break;
                } elseif ($category->slug == "smackdown") {
                    $wrestler_category = Wrestlers_Leagues::$tax_term_wrestlers_smackdown;
                    break;
                } elseif ($category->slug == "superstars") {
                    $wrestler_category = Wrestlers_Leagues::$tax_term_wrestlers_superstar;
                    break;
                } elseif ($category->slug == 'nxt') {
                    $wrestler_category = Wrestlers_Leagues::$tax_term_wrestlers_nxt;
                    break;
                } elseif ($category->slug == 'raw') {
                    $wrestler_category = Wrestlers_Leagues::$tax_term_wrestlers_raw;
                    break;
                }

            }
            $wrestler = array(
                'post_title' => $p->post_title,
                'post_status' => 'publish',
                'post_type' => Wrestlers_Leagues::$post_type_wrestler,
                'tax_input' => [Wrestlers_Leagues::$taxonomy_wrestlers_brand => $wrestler_category],
                'tags_input' => strtolower($p->post_title[0]),
            );
            $post_id = wp_insert_post($wrestler);
            if ($post_id) {
                $count++;
            }
            $wrestler_category_id = term_exists($wrestler_category, Wrestlers_Leagues::$taxonomy_wrestlers_brand);

            wp_set_post_terms($post_id, [$wrestler_category_id['term_id']], Wrestlers_Leagues::$taxonomy_wrestlers_brand);
            wp_set_post_terms($post_id, strtolower($p->post_title[0]), Wrestlers_Leagues::$taxonomy_wrestlers_tags);

            $post_thumbnail_id = get_post_thumbnail_id($p->ID);
            set_post_thumbnail($post_id, $post_thumbnail_id);
        }

        if ($count >= 134) {
            update_option('init_wrestlers', 'completed');
        }
//        }
    }

    public static function set_screen($status, $option, $value)
    {
        return $value;
    }

    public function plugin_menu()
    {

        $hook = add_menu_page(
            'Wrestlers Matches Management',
            'Matches',
            'manage_options',
            'kwl_matches_manage',
            [$this, 'matches_manage_page']
        );

        add_action("load-$hook", [$this, 'screen_option']);
        $submenu_hook = add_submenu_page(
            'kwl_matches_manage',
            'Wrestlers Bonuses Management',
            'Bonuses',
            'manage_options',
            'kwl_bonuses_manage',
            [$this, 'bonuses_submenu_page']
        );
        add_action("load-$submenu_hook", [$this, 'screen_option_bonus']);

//        add_submenu_page(
//            'kwl_matches_manage',
//            'Wrestlers Management',
//            'Wrestlers Manage',
//            'manage_options',
//            'kwl_wrestlers_manage',
//            [$this, 'wrestlers_submenu_page']
//        );
    }

    public function wrestlers_submenu_page()
    {
        $this->init_wrestlers_only_once();
        header("Location: " . admin_url("/edit.php?post_type=kwl_wrestlers"));
        die();
    }

    /**
     * Plugin settings page
     */
    public function matches_manage_page()
    {
        ?>
        <div class="wrap">
            <h2>Wrestlers Matches</h2>

            <form method="post" class="add-match-form">
                <input type="hidden" name="action" value="add">
                <label>
                    Match name*:
                    <input type="text" name="match_name" required/>
                </label>
                <label>
                    Select Winner*:
                    <select name="winner_id" required>
                        <?php
                        $wrestlers = get_posts([
                            'post_type' => Wrestlers_Leagues::$post_type_wrestler,
                            'posts_per_page' => -1,
                            'numberposts' => -1
                        ]);
                        foreach ($wrestlers as $wrestler) {
                            echo "<option value='{$wrestler->ID}'>{$wrestler->post_title}</option>";
                        }
                        ?>
                    </select>
                </label>
                <label>
                    Winner Points*:
                    <input type="text" name="winner_points" required/>
                </label>
                <label>
                    Select Loser*:
                    <select name="loser_id" required>
                        <?php
                        foreach ($wrestlers as $wrestler) {
                            echo "<option value='{$wrestler->ID}'>{$wrestler->post_title}</option>";
                        }
                        ?>
                    </select>
                </label>
                <label>
                    Loser Points*:
                    <input type="text" name="loser_points" required/>
                </label>
                <label>
                    Description:
                    <input type="text" name="desc"/>
                </label>
                <label>
                    Date*:
                    <input type="date" name="dateline" required/>
                </label>
                <input type="submit" value="Add New" class="button button-primary"/>

            </form>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php
                                $this->matches_instance->prepare_items();
                                $this->matches_instance->display(); ?>
                            </form>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php
    }

    /**
     * Screen options
     */
    public function screen_option()
    {

        $option = 'per_page';
        $args = [
            'label' => 'Matches',
            'default' => 5,
            'option' => 'matches_per_page'
        ];

        add_screen_option($option, $args);

        $this->matches_instance = new Matches_List();
    }

    public function bonuses_submenu_page()
    {
        ?>
        <div class="wrap">
            <h2>Wrestlers Bonuses</h2>

            <form method="post" class="add-bonus-form">
                <input type="hidden" name="action" value="add">
                <label>
                    Select Wrestler*:
                    <select name="wrestler_id" required>
                        <?php
                        $wrestlers = get_posts([
                            'post_type' => Wrestlers_Leagues::$post_type_wrestler,
                            'posts_per_page' => -1,
                            'numberposts' => -1
                        ]);
                        foreach ($wrestlers as $wrestler) {
                            echo "<option value='{$wrestler->ID}'>{$wrestler->post_title}</option>";
                        }
                        ?>
                    </select>
                </label>
                <label>
                    Bonus Points*:
                    <input type="text" name="bonus_points" required/>
                </label>
                <label>
                    Provider Name*:
                    <input type="text" name="provider_name" required/>
                </label>
                <label>
                    Description*:
                    <input type="text" name="desc" required/>
                </label>
                <label>
                    Date*:
                    <input type="date" name="dateline" required/>
                </label>
                <input type="submit" value="Add New" class="button button-primary"/>

            </form>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php
                                $this->bonuses_instance->prepare_items();
                                $this->bonuses_instance->display(); ?>
                            </form>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php
    }

    public function screen_option_bonus()
    {
        $option = 'per_page';
        $args = [
            'label' => 'Bonuses',
            'default' => 5,
            'option' => 'bonuses_per_page'
        ];

        add_screen_option($option, $args);

        $this->bonuses_instance = new Bonuses_List();
    }

    /**
     * Generate HTML for displaying fields
     * @param  array $field Field data
     * @param  boolean $echo Whether to echo the field HTML or return it
     * @return void
     */
    public function display_field($data = array(), $post = false, $echo = true)
    {

        // Get field info
        if (isset($data['field'])) {
            $field = $data['field'];
        } else {
            $field = $data;
        }

        // Check for prefix on option name
        $option_name = '';
        if (isset($data['prefix'])) {
            $option_name = $data['prefix'];
        }

        // Get saved data
        $data = '';
        if ($post) {

            // Get saved field data
            $option_name .= $field['id'];
            $option = get_post_meta($post->ID, $field['id'], true);

            // Get data to display in field
            if (isset($option)) {
                $data = $option;
            }

        } else {

            // Get saved option
            $option_name .= $field['id'];
            $option = get_option($option_name);

            // Get data to display in field
            if (isset($option)) {
                $data = $option;
            }

        }

        // Show default data if no option saved and default is supplied
        if ($data === false && isset($field['default'])) {
            $data = $field['default'];
        } elseif ($data === false) {
            $data = '';
        }

        $html = '';

        switch ($field['type']) {

            case 'text':
            case 'url':
            case 'email':
                $html .= '<input id="' . esc_attr($field['id']) . '" type="text" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . esc_attr($data) . '" />' . "\n";
                break;

            case 'password':
            case 'number':
            case 'hidden':
                $min = '';
                if (isset($field['min'])) {
                    $min = ' min="' . esc_attr($field['min']) . '"';
                }

                $max = '';
                if (isset($field['max'])) {
                    $max = ' max="' . esc_attr($field['max']) . '"';
                }
                $html .= '<input id="' . esc_attr($field['id']) . '" type="' . esc_attr($field['type']) . '" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . esc_attr($data) . '"' . $min . '' . $max . '/>' . "\n";
                break;

            case 'text_secret':
                $html .= '<input id="' . esc_attr($field['id']) . '" type="text" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="" />' . "\n";
                break;

            case 'textarea':
                $html .= '<textarea id="' . esc_attr($field['id']) . '" rows="5" cols="50" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '">' . $data . '</textarea><br/>' . "\n";
                break;

            case 'checkbox':
                $checked = '';
                if ($data && 'on' == $data) {
                    $checked = 'checked="checked"';
                }
                $html .= '<input id="' . esc_attr($field['id']) . '" type="' . esc_attr($field['type']) . '" name="' . esc_attr($option_name) . '" ' . $checked . '/>' . "\n";
                break;

            case 'checkbox_multi':
                foreach ($field['options'] as $k => $v) {
                    $checked = false;
                    if (in_array($k, (array)$data)) {
                        $checked = true;
                    }
                    $html .= '<p><label for="' . esc_attr($field['id'] . '_' . $k) . '" class="checkbox_multi"><input type="checkbox" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '[]" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label></p> ';
                }
                break;

            case 'radio':
                foreach ($field['options'] as $k => $v) {
                    $checked = false;
                    if ($k == $data) {
                        $checked = true;
                    }
                    $html .= '<label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="radio" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label> ';
                }
                break;

            case 'select':
                $html .= '<select name="' . esc_attr($option_name) . '" id="' . esc_attr($field['id']) . '">';
                foreach ($field['options'] as $k => $v) {
                    $selected = false;
                    if ($k == $data) {
                        $selected = true;
                    }
                    $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
                }
                $html .= '</select> ';
                break;

            case 'select_multi':
                $html .= '<select name="' . esc_attr($option_name) . '[]" id="' . esc_attr($field['id']) . '" multiple="multiple">';
                foreach ($field['options'] as $k => $v) {
                    $selected = false;
                    if (in_array($k, (array)$data)) {
                        $selected = true;
                    }
                    $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
                }
                $html .= '</select> ';
                break;

            case 'image':
                $image_thumb = '';
                if ($data) {
                    $image_thumb = wp_get_attachment_thumb_url($data);
                }
                $html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
                $html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __('Upload an image', 'wrestlers-leagues') . '" data-uploader_button_text="' . __('Use image', 'wrestlers-leagues') . '" class="image_upload_button button" value="' . __('Upload new image', 'wrestlers-leagues') . '" />' . "\n";
                $html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="' . __('Remove image', 'wrestlers-leagues') . '" />' . "\n";
                $html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
                break;

            case 'color':
                ?>
                <div class="color-picker" style="position:relative;">
                    <input type="text" name="<?php esc_attr_e($option_name); ?>" class="color"
                           value="<?php esc_attr_e($data); ?>"/>
                    <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;"
                         class="colorpicker"></div>
                </div>
                <?php
                break;

            case 'editor':
                wp_editor($data, $option_name, array(
                    'textarea_name' => $option_name
                ));
                break;

        }

        switch ($field['type']) {

            case 'checkbox_multi':
            case 'radio':
            case 'select_multi':
                $html .= '<br/><span class="description">' . $field['description'] . '</span>';
                break;

            default:
                if (!$post) {
                    $html .= '<label for="' . esc_attr($field['id']) . '">' . "\n";
                }

                $html .= '<span class="description">' . $field['description'] . '</span>' . "\n";

                if (!$post) {
                    $html .= '</label>' . "\n";
                }
                break;
        }

        if (!$echo) {
            return $html;
        }

        echo $html;

    }

    /**
     * Validate form field
     * @param  string $data Submitted value
     * @param  string $type Type of field to validate
     * @return string       Validated value
     */
    public function validate_field($data = '', $type = 'text')
    {

        switch ($type) {
            case 'text':
                $data = esc_attr($data);
                break;
            case 'url':
                $data = esc_url($data);
                break;
            case 'email':
                $data = is_email($data);
                break;
        }

        return $data;
    }

    /**
     * Add meta box to the dashboard
     * @param string $id Unique ID for metabox
     * @param string $title Display title of metabox
     * @param array $post_types Post types to which this metabox applies
     * @param string $context Context in which to display this metabox ('advanced' or 'side')
     * @param string $priority Priority of this metabox ('default', 'low' or 'high')
     * @param array $callback_args Any axtra arguments that will be passed to the display function for this metabox
     * @return void
     */
    public function add_meta_box($id = '', $title = '', $post_types = array(), $context = 'advanced', $priority = 'default', $callback_args = null)
    {

        // Get post type(s)
        if (!is_array($post_types)) {
            $post_types = array($post_types);
        }

        // Generate each metabox
        foreach ($post_types as $post_type) {
            add_meta_box($id, $title, array($this, 'meta_box_content'), $post_type, $context, $priority, $callback_args);
        }
    }

    /**
     * Display metabox content
     * @param  object $post Post object
     * @param  array $args Arguments unique to this metabox
     * @return void
     */
    public function meta_box_content($post, $args)
    {

        $fields = apply_filters($post->post_type . '_custom_fields', array(), $post->post_type);

        if (!is_array($fields) || 0 == count($fields)) return;

        echo '<div class="custom-field-panel">' . "\n";

        foreach ($fields as $field) {

            if (!isset($field['metabox'])) continue;

            if (!is_array($field['metabox'])) {
                $field['metabox'] = array($field['metabox']);
            }

            if (in_array($args['id'], $field['metabox'])) {
                $this->display_meta_box_field($field, $post);
            }

        }

        echo '</div>' . "\n";

    }

    /**
     * Dispay field in metabox
     * @param  array $field Field data
     * @param  object $post Post object
     * @return void
     */
    public function display_meta_box_field($field = array(), $post)
    {

        if (!is_array($field) || 0 == count($field)) return;

        $field = '<p class="form-field"><label for="' . $field['id'] . '">' . $field['label'] . '</label>' . $this->display_field($field, $post, false) . '</p>' . "\n";

        echo $field;
    }

    /**
     * Save metabox fields
     * @param  integer $post_id Post ID
     * @return void
     */
    public function save_meta_boxes($post_id = 0)
    {

        if (!$post_id) return;

        $post_type = get_post_type($post_id);

        $fields = apply_filters($post_type . '_custom_fields', array(), $post_type);

        if (!is_array($fields) || 0 == count($fields)) return;

        foreach ($fields as $field) {
            if (isset($_REQUEST[$field['id']])) {
                update_post_meta($post_id, $field['id'], $this->validate_field($_REQUEST[$field['id']], $field['type']));
            } else {
                update_post_meta($post_id, $field['id'], '');
            }
        }
    }

}
