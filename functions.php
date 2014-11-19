<?php

namespace Genesis;

class Loop_Layouts {
    public static $instance;
    public $current_post_layout;

    public static function instance() {
        if ( is_null( self::$instance ) )
            return self::$instance = new self;
        else
            return self::$instance;
    }

    private function __construct() {
        if ( function_exists( 'add_filter' ) ) {
            add_filter( 'custom_post_layouts', array( $this, 'custom_post_layouts' ), 10000 );

            if ( function_exists( 'get_field' ) ) {
                include_once( dirname( __FILE__ ) . '/field-groups.php' );
                add_filter( 'custom_page_layouts', array( $this, 'custom_page_layouts_defaults' ), 5 );
                add_filter( 'custom_page_layouts', array( $this, 'custom_page_layouts' ), 10000 );
            }
        }

        if ( function_exists( 'acf_add_options_sub_page' ) ) {
            acf_add_options_sub_page( array(
                'title'      => 'Page Defaults',
                'parent'     => 'edit.php?post_type=page',
                'capability' => 'manage_options'
            ) );
        }
    }

    public function custom_post_layouts( $post_layouts ) {

        if ( $this->current_post_layout ) {
            $post_layouts[] = $this->current_post_layout;
        }

        return $post_layouts;
    }

    public function custom_page_layouts_defaults( $page_layouts ) {

        $id = 'option';

        if ( function_exists( 'get_field' ) && get_field( 'enable_page_settings', $id ) ) {
            $settings = array();

            if ( $body_bg_image_src = get_field( 'body_bg_image_src', $id ) )
                $settings['body_bg_image_src'] = $body_bg_image_src;

            if ( $show_site_title = get_field( 'show_site_title', $id ) )
                $settings['show_site_title'] = $show_site_title;

            if ( $show_site_description = get_field( 'show_site_description', $id ) )
                $settings['show_site_description'] = $show_site_description;

            if ( $show_header_widget_area = get_field( 'show_header_widget_area', $id ) )
                $settings['show_header_widget_area'] = $show_header_widget_area;

            if ( $show_primary_nav = get_field( 'show_primary_nav', $id ) )
                $settings['show_primary_nav'] = $show_primary_nav;

            if ( $show_secondary_nav = get_field( 'show_secondary_nav', $id ) )
                $settings['show_secondary_nav'] = $show_secondary_nav;

            if ( $show_page_title = get_field( 'show_page_title', $id ) )
                $settings['show_page_title'] = $show_page_title;

            if ( $show_content = get_field( 'show_content', $id ) )
                $settings['show_content'] = $show_content;

            if ( $inner_bg_image_src = get_field( 'inner_bg_image_src', $id ) )
                $settings['inner_bg_image_src'] = $inner_bg_image_src;

            if ( $footer_widgets = get_field( 'footer_widgets', $id ) )
                $settings['footer_widgets'] = $footer_widgets;

            if ( $show_footer = get_field( 'show_footer', $id ) )
                $settings['show_footer'] = $show_footer;

            if ( $footer_creds = get_field( 'footer_creds', $id ) )
                $settings['footer_creds'] = $footer_creds;

            $page_layouts[] = array(
                'settings' => $settings,
                'condition' => array(
                    array(
                        array(
                            'var'      => true,
                            'var_type' => 'variable',
                            'params'   => array(),
                            'operator' => '==',
                            'value'    => true
                        ),
                    ),
                ),
            );
        }
        return $page_layouts;
    }

    public function custom_page_layouts( $page_layouts ) {
        global $wp_the_query, $post;

        if ( ! $wp_the_query )
            return;

        $id = $wp_the_query->queried_object_id;

        if ( $post && function_exists( 'get_field' ) && get_field( 'enable_page_settings', $id ) ) {
            $settings = array();

            if ( $body_bg_image_src = get_field( 'body_bg_image_src', $id ) )
                $settings['body_bg_image_src'] = $body_bg_image_src;

            $settings['show_site_title'] = get_field( 'show_site_title', $id );

            $settings['show_site_description'] = get_field( 'show_site_description', $id );

            $settings['show_header_widget_area'] = get_field( 'show_header_widget_area', $id );

            $settings['show_primary_nav'] = get_field( 'show_primary_nav', $id );

            $settings['show_secondary_nav'] = get_field( 'show_secondary_nav', $id );

            $settings['show_page_title'] = get_field( 'show_page_title', $id );

            $settings['show_content'] = get_field( 'show_content', $id );

            if ( $inner_bg_image_src = get_field( 'inner_bg_image_src', $id ) )
                $settings['inner_bg_image_src'] = $inner_bg_image_src;

            if ( $footer_widgets = get_field( 'footer_widgets', $id ) )
                $settings['footer_widgets'] = $footer_widgets;

            $settings['show_footer'] = get_field( 'show_footer', $id );

            if ( $footer_creds = get_field( 'footer_creds', $id ) )
                $settings['footer_creds'] = $footer_creds;

            $page_layouts[] = array(
                'settings' => $settings,
                'condition' => array(
                    array(
                        array(
                            'var'      => 'ID',
                            'var_type' => 'post_property',
                            'params'   => array(),
                            'operator' => '==',
                            'value'    => $id
                        ),
                        array(
                            'var'      => 'is_main_query',
                            'var_type' => 'function',
                            'params'   => array(),
                            'operator' => '==',
                            'value'    => true,
                        ),
                    ),
                ),
            );
        }
        return $page_layouts;
    }

    public function loop( $args = array(), $settings = array() ) {
        if ( ! function_exists( 'genesis_custom_loop' ) )
            return '';

        $old_layout = $this->current_post_layout;

        $this->current_post_layout = array(
            'settings' => $settings,
            'condition' => array(
                array(
                    array(
                        'var' => '__return_true',
                    ),
                ),
            ),
        );

        ob_start();
        genesis_custom_loop( $args );
        $output = ob_get_clean();

        $this->current_post_layout = $old_layout;

        return $output;
    }
}

Loop_Layouts::instance();

function loop_layout( $args = array(), $settings = array(), $echo = false ) {
    $output = Loop_Layouts::instance()->loop( $args, $settings );

    if ( $echo )
        echo $output;
    else
        return $output;
}
