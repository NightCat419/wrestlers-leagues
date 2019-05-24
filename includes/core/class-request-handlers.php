<?php
/**
 * Created by PhpStorm.
 * User: qaz
 * Date: 5/15/2019
 * Time: 4:31 AM
 */

class Request_Handlers{

    /**
     * Request_Handlers constructor.
     */
    public function __construct()
    {
    }

    public function register_request_handlers(){
        ! is_admin() and add_action('init', array($this, 'post_create_league'));
        ! is_admin() and add_action('init', array($this, 'post_join_league'));
        ! is_admin() and add_action('init', array($this, 'post_proc_join_request'));
        ! is_admin() and add_action('init', array($this, 'post_register_join'));
        ! is_admin() and add_action('init', array($this, 'post_save_draft_settings'));

        add_action( 'wp_ajax_select_wrestler', [$this, 'ajax_select_wrestler'] );
        add_action( 'wp_ajax_wrestlers_in_league', [$this, 'ajax_wrestlers_in_league'] );
        add_action( 'wp_ajax_find_league_email', [$this, 'ajax_find_league_email'] );
        add_action( 'wp_ajax_nopriv_find_league_email', [$this, 'ajax_find_league_email'] );
    }

    public function ajax_find_league_email(){
        if(!empty($_POST['friend_email'])){
            global $wpdb;
            $query="SELECT l.league_id, p.post_title AS league_name FROM {$wpdb->prefix}kwl_league_user AS l "
                ."JOIN {$wpdb->prefix}users AS u ON l.commissioner_id=u.ID "
                ."JOIN {$wpdb->prefix}posts AS p ON l.league_id=p.ID "
                ."WHERE u.user_email='{$_POST['friend_email']}'";
            $result = $wpdb->get_results($query);
            error_log($query);
            wp_send_json($result);
        }
    }


    public function ajax_wrestlers_in_league(){
        if(!empty($_POST['league_id'])){
            global $wpdb;
            $in_wrestlers = $wpdb->get_results("SELECT wrestler_id FROM {$wpdb->prefix}kwl_league_wrestler "
                ."WHERE league_id={$_POST['league_id']}");
            wp_send_json($in_wrestlers);
        }
    }

    public function ajax_select_wrestler(){
        if ( !empty($_POST['action']) && $_POST['action'] === 'select_wrestler' ) {
            if(!empty($_POST['wrestler_id']) && !empty($_POST['league_id'])){
                $wrestler = [
                    'league_id' => $_POST['league_id'],
                    'wrestler_id' => $_POST['wrestler_id'],
                    'user_id' => get_current_user_id()
                ];
                global $wpdb;
                $result = $wpdb->insert($wpdb->prefix.'kwl_league_wrestler', $wrestler);

                wp_send_json($result);
            }
        }
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
            $res = wp_set_post_terms($post_id, $private_term_id['term_id'], Wrestlers_Leagues::$taxonomy_leagues_category);
            $opened_term_id = term_exists( Wrestlers_Leagues::$tax_term_leagues_opened, Wrestlers_Leagues::$taxonomy_leagues_status);
            $res = wp_set_post_terms($post_id, $opened_term_id['term_id'], Wrestlers_Leagues::$taxonomy_leagues_status);

            if($post_id){
                update_post_meta($post_id, Wrestlers_Leagues::$limit_teams_league_meta, $_POST["num_teams"]);
                update_post_meta($post_id, Wrestlers_Leagues::$limit_wrestlers_team_meta, $_POST["num_wrestlers"]);
                update_post_meta($post_id, Wrestlers_Leagues::$league_commissioner_meta, $user_id);
            }

            global $wpdb, $wl_instance;
            $league_user = array(
                'league_id' => $post_id,
                'user_id' => $user_id,
                'team_name' => $user->display_name,
                'date_join' => time(),
                'commissioner_id' => $user_id,
                'status' => 1,

            );
            $count = $wpdb->insert("{$wpdb->prefix}kwl_league_user", $league_user);
            error_log("\nwrestler plugin: create league $count.");

            $draft = [
                'league_id' => $post_id,
                'draft_time' => strtotime('+ 3 days', time()),
                'minutes_selection' => 5,
            ];
            $count = $wpdb->insert("{$wpdb->prefix}kwl_drafts", $draft);
            error_log("wrestler plugin: create league insert draft data $count\n".json_encode($draft));

            if(isset($_POST['friends']) && class_exists("UM_Friends_API")){
                $join_link = get_permalink( get_page_by_path('join-to-league-with-friends'))."?league_id={$post_id}";
                $message = "Please join to this league {$_POST['lg_name']}: {$join_link}.";
                foreach ($_POST['friends'] as $friendid){
                    if(is_numeric($friendid)){
                        UM()->Friends_API()->api()->add($friendid, $user_id);
                        do_action('um_friends_after_user_friend_request', $friendid, $user_id );
                        $wl_instance->um_customize->send_um_message($user_id, $friendid, $message);
                    }
                    else{
                        $options = wp_mail( $friendid, "Join League Request", $message, "Content-Type: text/html\r\n", null );
                        error_log(json_encode($options));
                    }
                }

            }
            header("Location: ".home_url("/my-leagues"));
            die();
        }
    }

    /**
     *
     */
    public function post_join_league(){
        if ( !empty($_POST['action']) && $_POST['action'] === 'join_league' ) {
            global $wpdb, $wl_instance;
            $league_id = $_POST['league'];
//            $user_id = get_current_user_id();
            $join_league_request = array(
                'league_id' => $league_id,
                'client_email' => $_POST['client_email'],
                'team_name' => $_POST['team_name'],
                'dateline' => time(),
                'status' => 0,

            );
            $result = $wpdb->insert("{$wpdb->prefix}kwl_join_league_requests", $join_league_request);

            $query_commissioner = "SELECT u.user_email, p.post_title AS league_name FROM {$wpdb->prefix}users AS u "
                ."JOIN {$wpdb->prefix}kwl_league_user AS l ON u.ID = l.commissioner_id "
                ."JOIN {$wpdb->prefix}posts AS p ON l.league_id=p.ID "
                ."WHERE l.league_id={$league_id} LIMIT 1";
            $result = $wpdb->get_results($query_commissioner);
            if($result && count($result) > 0){
                $commissioner_email = $result[0]->user_email;
                $league_name = $result[0]->league_name;
                $league_link = get_post_permalink($league_id);
                ob_start();
                include WP_KWL_PLUGIN_DIR . "/templates/join-league-request-email.php";
                $mail_content = ob_get_clean();
                $options = wp_mail( $commissioner_email, "Join League Request", $mail_content, "Content-Type: text/html\r\n", null );
                header("Location: ".home_url());
                die();
            }

        }
    }

    public function post_proc_join_request(){
        global $wpdb;
        if (!empty($_POST['action']) && $_POST['action'] === 'accept_join_request') {
            $request_id = $_POST['request_id'];
            $data = ['status' => 1];
            $where = ['ID' => $request_id];
            $result = $wpdb->update("{$wpdb->prefix}kwl_join_league_requests", $data, $where);

            $result = $wpdb->get_results("SELECT client_email FROM {$wpdb->prefix}kwl_join_league_requests "
                ."WHERE ID={$request_id}");
            $register_link = get_permalink( get_page_by_path( 'register-join' ) );
            $register_link .= "?request_id={$request_id}";
            ob_start();
            include WP_KWL_PLUGIN_DIR . "/templates/join-league-notify-email.php";
            $mail_content = ob_get_clean();
            $options = wp_mail( $result[0]->client_email, "Join League Notification", $mail_content, "Content-Type: text/html\r\n", null );
        }
        if (!empty($_POST['action']) && $_POST['action'] === 'reject_join_request') {
            $request_id = $_POST['request_id'];
            $data = ['status' => -1];
            $where = ['ID' => $request_id];
            $result = $wpdb->update("{$wpdb->prefix}kwl_join_league_requests", $data, $where);

            $result = $wpdb->get_results("SELECT client_email FROM {$wpdb->prefix}kwl_join_league_requests "
                ."WHERE ID={$request_id}");

            $register_link = get_permalink( get_page_by_path( 'register' ) );
            ob_start();
            include WP_KWL_PLUGIN_DIR . "/templates/reject-league-notify-email.php";
            $mail_content = ob_get_clean();
            $options = wp_mail( $result[0]->client_email, "Join League Notification", $mail_content, "Content-Type: text/html\r\n", null );
        }
    }
    public function post_register_join(){
        global $wpdb;
        if (!empty($_POST['action']) && $_POST['action'] === 'register-join') {
            $error_fields = [];
            $has_error = false;
            if(empty($_POST['user_email-2398'])){
                $has_error = true;
                $error_fields[] = "email";
            }
            if(empty($_POST['user_login-2398'])){
                $has_error = true;
                $error_fields[] = "user id";
            }
            if(empty($_POST['first_name-2398'])){
                $has_error = true;
                $error_fields[] = "first name";
            }
            if(empty($_POST['last_name-2398'])){
                $has_error = true;
                $error_fields[] = "last name";
            }
            if(empty($_POST['user_password-2398'])){
                $has_error = true;
                $error_fields[] = "password";
            }
            if(empty($_POST['confirm_user_password-2398'])){
                $has_error = true;
                if(!in_array("password", $error_fields)){
                    $error_fields[] = "password";
                }
            }
            if($has_error){
                $error_message = "Please fill ".implode(", ", $error_fields)." fields!";
                $_SESSION['error_message'] = $error_message;
            }
            else{
                if(username_exists($_POST['user_email-2398']) != null){
                    $_SESSION['error_message'] = "This email is already registered! Please login.";
                }
                else{
                    if($_POST['user_password-2398'] != $_POST['confirm_user_password-2398']){
                        $_SESSION['error_message'] = "Passwords is not match.";
                    }
                    else{
                        check_admin_referer('register-join');
                        $user_data = [
                            'user_login' => $_POST['user_login-2398'],
                            'user_pass' => $_POST['user_password-2398'],
                            'user_email' => $_POST['user_email-2398'],
                            'first_name' => $_POST['first_name-2398'],
                            'last_name' => $_POST['last_name-2398'],
                            'role' => 'sp_player'
                        ];
                        wp_insert_user($user_data);
                        $creds = array(
                            'user_login'    => $_POST['user_login-2398'],
                            'user_password' => $_POST['user_password-2398'],
                            'remember'      => true
                        );

                        $user = wp_signon( $creds, false );

                        $request_id = $_POST['request_id'];
                        $request_data = ['status' => 2];
                        $where = ['ID' => $request_id];
                        $wpdb->update($wpdb->prefix."kwl_join_league_requests", $request_data, $where);
                        $league = $wpdb->get_row("SELECT league_id, team_name FROM {$wpdb->prefix}kwl_join_league_requests "
                            ."WHERE ID={$request_id}");

                        $league_data = [
                            'league_id' => $league->league_id,
                            'user_id' => $user->ID,
                            'team_name' => $league->team_name,
                            'date_join' => time(),
                            'status' => 1,
                        ];
                        $wpdb->insert($wpdb->prefix."kwl_league_user", $league_data);

                        header("Location: ".home_url("/my-leagues"));
                        die();
                    }
                }
            }
        }
    }

    public function post_save_draft_settings(){
        if (!empty($_POST['action']) && $_POST['action'] === 'save_draft_settings') {
            global $wpdb;
            $post_id = $_POST['post_id'];
            $draft_data = [
                'draft_time' => strtotime($_POST['draft_time']),
                'minutes_selection' => $_POST['minutes_selection'],
            ];
            $where = ['league_id'=>$post_id];
            $updated = $wpdb->update("{$wpdb->prefix}kwl_drafts", $draft_data, $where);
            error_log("Draft data updated $updated");
            error_log("Draft data updated ".json_encode($where));
            error_log("Draft data updated ".json_encode($draft_data));

            update_post_meta($post_id, Wrestlers_Leagues::$limit_teams_league_meta, $_POST["limit_teams"]);
            update_post_meta($post_id, Wrestlers_Leagues::$limit_wrestlers_team_meta, $_POST["limit_wrestlers"]);

        }
    }
}