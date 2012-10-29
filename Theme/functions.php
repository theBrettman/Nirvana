<?php

function sidebars_init() {

	global $wpdb;
	$table = $wpdb->prefix . 'dynamic_sidebars';

	$rows = $wpdb->get_results("SELECT * FROM $wpdb->$table");

	/* Register sidebars. */
	foreach ( $rows as $row ) {
		register_sidebar(
				array(
						'id' => $row->sidebar_id,
						'name' => $row->sidebar_name,
						'description' => __( $row->description ),
						'before_widget' => $row->before_widget,
						'after_widget' => $row->after_widget,
						'before_title' => $row->before_title,
						'after_title' => $row->after_title
				)
		);
	}

}
add_action( 'widgets_init', 'sidebars_init' );
