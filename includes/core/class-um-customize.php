<?php
/**
 * Created by PhpStorm.
 * User: qaz
 * Date: 5/11/2019
 * Time: 10:57 AM
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class UM_Customize{

    /**
     * UM_Customize constructor.
     */
    public function __construct()
    {
    }

    public function init_profile_tabs(){
        add_filter('um_profile_tabs', array($this, 'leagues_tab'), 1000 );
        add_action('um_profile_content_leagues_default', array($this, 'um_profile_content_leagues_default'));

    }

    /* add a custom tab to show user pages */
    function leagues_tab( $tabs ) {
        $tabs['leagues'] = array(
            'name' => 'My Leagues',
            'icon' => 'um-faicon-pencil',
            'custom' => true
        );
        return $tabs;
    }

    /* Tell the tab what to display */
    function um_profile_content_leagues_default( $args ) {
        global $ultimatemember;
        include_once WP_KWL_PLUGIN_DIR . '/templates/um-myleagues-tab-page.php';
    }

    public function invite_friends($friends, $sender, $message){
        foreach ($friends as $friendid){
            UM()->Friends_API()->api()->add($friendid, $sender);
            do_action('um_friends_after_user_friend_request', $friendid, $sender );
            $this->send_um_message($sender, $friendid, $message);
        }

    }

    public function send_um_message($from, $to, $message){
        if ( ! UM()->Messaging_API()->api()->can_message( $to ) ) {
            error_log("UM()->Messaging_API()->api()->can_message( $to ) : error");
        }

        $_POST['content'] = $message;
        // Create/Update conversation and add message
        $conversation_id = UM()->Messaging_API()->api()->create_conversation( $to, get_current_user_id() );
        if ( empty( $conversation_id ) ) {
            error_log("conversation_id is empty");
        }
        $response = UM()->Messaging_API()->api()->get_conversation_id( $to, get_current_user_id() );
        $output['conversation_id'] = $response['conversation_id'];
        $output['last_updated'] = $response['last_updated'];
        $output['messages'] = UM()->Messaging_API()->api()->get_conversation( $to, get_current_user_id(), $conversation_id );
        $output['limit_hit'] = UM()->Messaging_API()->api()->limit_reached() ? 1 : 0;
        $output['chat_history_download'] = UM()->Messaging_API()->gdpr()->get_download_url( $response['conversation_id'] );

//        wp_send_json_success( $output );
    }

}