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
        }
    }

    public function custom_post_layouts( $post_layouts ) {

        if ( $this->current_post_layout ) {
            $post_layouts[] = $this->current_post_layout;
        }

        return $post_layouts;
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

function loop_layout( $args = array(), $settings = array(), $echo = false ) {
    $output = Loop_Layouts::instance()->loop( $args, $settings );

    if ( $echo )
        echo $output;
    else
        return $output;
}