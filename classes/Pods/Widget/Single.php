<?php

/**
 * @package Pods\Widgets
 */
class Pods_Widget_Single extends WP_Widget {

	/**
	 * Register the widget
	 */
	public function Pods_Widget_Single() {

		$this->WP_Widget( 'pods_widget_single',
			'Pods - Single Item',
			array( 'classname' => 'pods_widget_single', 'description' => 'Display a Single Pod Item' ),
			array( 'width' => 200 )
		);

	}

	/**
	 * {@inheritdoc}
	 */
	public function widget( $args, $instance ) {

		extract( $args );

		// Get widget field values
		$title = apply_filters( 'widget_title', $instance[ 'title' ] );

		$args = array(
			'name'       => trim( pods_v( 'pod_type', $instance, '' ) ),
			'slug'       => trim( pods_v( 'slug', $instance, '' ) ),
			'template'   => trim( pods_v( 'template', $instance, '' ) ),
			'before'     => trim( pods_v( 'before', $instance, '' ) ),
			'after'      => trim( pods_v( 'after', $instance, '' ) ),
			'shortcodes' => (int) pods_v( 'shortcodes', $instance, 0 )
		);

		$content = trim( pods_v( 'template_custom', $instance, '' ) );

		if ( 0 < strlen( $args[ 'name' ] ) && 0 < strlen( $args[ 'slug' ] ) && ( 0 < strlen( $args[ 'template' ] ) || 0 < strlen( $content ) ) ) {
			require PODS_DIR . 'ui/front/widgets.php';
		}

	}

	/**
	 * {@inheritdoc}
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance[ 'title' ] = pods_v( 'title', $new_instance, '' );
		$instance[ 'pod_type' ] = pods_v( 'pod_type', $new_instance, '' );
		$instance[ 'slug' ] = pods_v( 'slug', $new_instance, '' );
		$instance[ 'template' ] = pods_v( 'template', $new_instance, '' );
		$instance[ 'template_custom' ] = pods_v( 'template_custom', $new_instance, '' );
		$instance[ 'before' ] = pods_v( 'before', $new_instance, '' );
		$instance[ 'after' ] = pods_v( 'after', $new_instance, '' );
		$instance[ 'shortcodes' ] = (int) pods_v( 'shortcodes', $new_instance, 0 );

		return $instance;

	}

	/**
	 * {@inheritdoc}
	 */
	public function form( $instance ) {

		$title = pods_v( 'title', $instance, '' );
		$slug = pods_v( 'slug', $instance, '' );
		$pod_type = pods_v( 'pod_type', $instance, '' );
		$template = pods_v( 'template', $instance, '' );
		$template_custom = pods_v( 'template_custom', $instance, '' );
		$before = pods_v( 'before', $instance, '' );
		$after = pods_v( 'after', $instance, '' );
		$shortcodes = (int) pods_v( 'shortcodes', $instance, 0 );

		require PODS_DIR . 'ui/admin/widgets/single.php';

	}

}