<?php

global $wpdb;

function nirvana_install() {
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	//do something...
}
register_activation_hook( __FILE__, 'nirvana_install' );