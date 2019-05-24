<?php
/**
 * Created by PhpStorm.
 * User: qaz
 * Date: 5/18/2019
 * Time: 9:44 AM
 */

class ShortCodes{

    /**
     * ShortCodes constructor.
     */
    public function __construct()
    {
        if ( ! is_admin() ) {
            add_shortcode( 'wl-create-leagues-form', array( $this, 'wl_create_leagues_form_shortcode' ) );
            add_shortcode( 'wl-join-leagues-form', array( $this, 'wl_join_leagues_form_shortcode' ) );
            add_shortcode( 'wl-register-join-form', array( $this, 'wl_register_join_form_shortcode' ) );
            add_shortcode( 'wl-my-leagues-page', array( $this, 'wl_my_leagues_page_shortcode' ) );
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
     * Handle Shortcode [wl-my-leagues-page]
     * @param $shortcode_attrs
     * @return string
     */
    public function wl_my_leagues_page_shortcode($shortcode_attrs){
        ob_start();
        include WP_KWL_PLUGIN_DIR . "/templates/my-leagues-page.php";
        $output = ob_get_clean();
        return $output;
    }

    /**
     * Handle Shortcode [wl-register-join-form]
     * @param $shortcode_attrs
     * @return string
     */
    public function wl_register_join_form_shortcode($shortcode_attrs){
        ob_start();
        include WP_KWL_PLUGIN_DIR . "/templates/register-join-form.php";
        $output = ob_get_clean();
        return $output;
    }

}