<?php
/*
Plugin Name: Courtyard Page Include Widget Plugin
Plugin URI: https://github.com/nickcmaynard/courtyard-page-include-widget-plugin
Description: This plugin adds a custom "page include" widget to augment the PT Courtyard theme.  It *requires* the PT Courtyard theme to be active.
Version: 1.0
Author: Nick Maynard
Author URI: https://github.com/nickcmaynard
License: GPL2
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

/**
 * Register widget.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
add_action( 'widgets_init', function() { register_widget( 'NCM_Courtyard_Page_Include_Widget' ); } );

wp_register_style('ncm-courtyard-page-include-widget', plugins_url( 'style.css', __FILE__ ));
wp_enqueue_style( 'ncm-courtyard-page-include-widget');

/**
 * Page Include Widget.
 */
class NCM_Courtyard_Page_Include_Widget extends WP_Widget {
    function __construct() {
        $widget_ops = array( 'classname' => 'ncm-courtyard-page-include', 'description' => esc_html__( 'Show a single page\'s content.', 'courtyard' ), 'customize_selective_refresh' => true, );
        $control_ops = array( 'width' => 200, 'height' =>250 );
        parent::__construct( false, $name = esc_html__( 'NCM: Page Include', 'courtyard' ), $widget_ops, $control_ops);
    }

    function form( $instance ) {
        $instance = wp_parse_args(
            (array) $instance, array(
                'title'             => '',
                'page_id'           => '',
            )
        );
        ?>

        <div class="pt-about">
            <div class="pt-admin-input-wrap">
                <div class="pt-admin-input-label">
                    <label
                    for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title', 'courtyard'); ?></label>
                </div><!-- .pt-admin-input-label -->

                <div class="pt-admin-input-holder">
                    <input type="text" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>"
                       value="<?php echo esc_attr($instance['title']); ?>"
                       placeholder="<?php esc_attr_e('Title', 'courtyard'); ?>">
                </div><!-- .pt-admin-input-holder -->

                <div class="clear"></div>
            </div><!-- .pt-admin-input-wrap -->

            <div class="pt-admin-input-wrap">
                <div class="pt-admin-input-label">
                    <label
                    for="<?php echo $this->get_field_id('page_id'); ?>"><?php esc_html_e('Page', 'courtyard'); ?></label>
                </div><!-- .pt-admin-input-label -->

                <div class="pt-admin-input-holder">
                    <?php wp_dropdown_pages( array(
                        'show_option_none'  => '',
                        'name'              => $this->get_field_name( 'page_id' ),
                        'selected'          => absint( $instance['page_id'] )
                    ) );
                    ?>
                </div><!-- .pt-admin-input-holder -->

                <div class="clear"></div>
            </div><!-- .pt-admin-input-wrap -->
        </div><!-- .pt-about -->
    <?php }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']             = sanitize_text_field( $new_instance['title'] );
        $instance['page_id']           = absint( $new_instance['page_id'] );
        return $instance;
    }

    function widget( $args, $instance ) {
        ob_start();
        extract($args);

        global $post;
        $pt_page_id         = isset( $instance['page_id'] ) ? $instance['page_id'] : '';

        $get_featured_pages = new WP_Query( array(
            'post_status'           => 'publish',
            'post_type'             =>  array( 'page' ),
            'page_id'               => $pt_page_id,
        ) );

        echo $args['before_widget']; ?>

        <div class="pt-widget-section">

            <?php if ( $get_featured_pages->have_posts() && !empty( $pt_page_id ) ) : ?>

                <?php while ($get_featured_pages->have_posts()) : $get_featured_pages->the_post(); ?>

                    <div class="pt-about-wrap">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="pt-about-cont-holder ncm-page-include-cont-holder">

                                        <header>
                                            <h3><?php the_title(); ?></h3>
                                        </header>

                                        <?php
                                        the_content();
                                        ?>
                                    </div>
                                </div><!-- .col-md-12 -->
                            </div><!-- .row -->
                        </div><!-- .container -->
                    </div><!-- .pt-about-wrap -->

                <?php endwhile;

                // Reset Post Data
                wp_reset_postdata(); ?>

            <?php endif; ?>

        </div><!-- .pt-widget-section -->

        <?php echo $args['after_widget'];
        ob_end_flush();
    }
}

?>
