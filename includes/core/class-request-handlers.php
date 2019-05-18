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

        add_action( 'wp_ajax_select_wrestler', [$this, 'ajax_select_wrestler'] );
        add_action( 'wp_ajax_wrestlers_in_league', [$this, 'ajax_wrestlers_in_league'] );
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

            if(isset($_POST['friends']) && class_exists("UM_Friends_API")){
                $wl_instance->um_customize->invite_friends($_POST['friends'], $user_id, "Please join to league.");
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
            $user_id = get_current_user_id();
            $league_user = array(
                'league_id' => $_POST['league'],
                'user_id' => $user_id,
                'team_name' => $_POST['team_name'],
                'date_join' => time(),
                'commissioner_id' => 0,
                'status' => 0,

            );
            $result = $wpdb->insert("{$wpdb->prefix}kwl_league_user", $league_user);

            if(isset($_POST['friends']) && class_exists("UM_Friends_API")){
                $wl_instance->um_customize->invite_friends($_POST['friends'], $user_id, "Please join to league.");
            }

            header("Location: ".home_url("/my-leagues"));
            die();
        }
    }

}