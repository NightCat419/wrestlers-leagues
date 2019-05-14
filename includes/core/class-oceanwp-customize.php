<?php
/**
 * Created by PhpStorm.
 * User: qaz
 * Date: 5/13/2019
 * Time: 10:16 AM
 */

class OceanWP_Customize{

    /**
     * OceanWP_Customize constructor.
     */
    public function __construct()
    {
        add_filter( 'ocean_main_metaboxes_post_types', array($this, 'oceanwp_metabox'), 20 );
        add_filter( 'ocean_header_style', array($this, 'wrestler_header_style') );
        add_filter( 'ocean_header_template', array($this, 'wrestler_header_style') );
    }

    function oceanwp_metabox( $types ) {

        // Your custom post type
        $types[] = Wrestlers_Leagues::$post_type_wrestler;
        $types[] = Wrestlers_Leagues::$post_type_league;

        // Return
        return $types;

    }
    function wrestler_header_style( $style ) {

        // Return the transparent header style
        $style = 'custom';

        // Return
        return $style;

    }
    function wrestler_header_template( $template ) {

        // Return the transparent header style
        $template = 'custom_header_2';

        // Return
        return $template;

    }
}