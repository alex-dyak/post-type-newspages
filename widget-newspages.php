<?php
class NewsPages_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct( 'NewsPages_Widget' ,__( 'Last NewsPages', 'post-type-newspages' ),
            array(
                'description' => __( 'Show Last NewsPages', 'post-type-newspages' )
            ) );
    }

    /**
     *  Displays the output, the last newspages
     */
    public function widget( $args, $instance ) {

        $args_post = array(
            'post_type' => 'newspages',
            'order' => 'DESC',
            'showposts' => $instance['numnewspages'],
        );

        $query = new WP_Query( $args_post );

        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $args['before_widget'];
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                echo '<br /><a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            }
        }
        echo $args['after_widget'];
    }

    /**
     * Update the widget options
     *
     * @param array $new_instance The new instance options
     * @param array $old_instance The old instance options
     *
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['numnewspages'] = strip_tags( $new_instance['numnewspages'] );
        $instance['title']        = strip_tags( $new_instance['title'] );

        return $instance;
    }

    /**
     *  Output the newspages widget options form
     *
     * @param array $instance saved instance
     *
     */
    public function form( $instance ) {
        $numnewspages = isset( $instance['numnewspages'] ) ? $instance['numnewspages'] : 3;
        $title        = isset( $instance['title'] ) ? $instance['title'] : 'Last NewsPages';

        include( 'view-widget.phtml' );
    }
}

/**
 *  Register the widget
 */
function register_newspages_widget(){
    register_widget( 'NewsPages_Widget' );
}
add_action( 'widgets_init', 'register_newspages_widget' );