<?php

/*Return array of templates*/

function get_templates(){
	header( "Content-Type: application/json" );
	echo json_encode(get_option('templates'));//if there is no option 'templates', it conveniently returns false.
	exit;
}
add_action('wp_ajax_get-templates', 'get_templates');

/*Return template form*/

function get_template_form(){
	$templates = get_option('templates', array());
	$key = $_POST['key'];
	$template = $templates[$key];
	$html = ($template['value']) ? $template['value'] : '';
	$response = array('key' => $key, 'html' => $html);
	header( "Content-Type: application/json" );
	echo json_encode($response);
	exit;
}
add_action('wp_ajax_get-template-form', 'get_template_form');


/*Update templates option*/

function update_template() {
	$templates = get_option('templates', array());
	$key = $_POST['key'];//if it's a new template, it is named on the client side.
	$newNonce = wp_create_nonce();
	$templates[$key] = $_POST['value'];//only creating/changing the values for this key in the array.
	if (update_option('templates', $templates) && check_admin_referer()) {
		$style = 'ui-state-highlight';
		$msg = $key . " updated.";
	}
	else {
		$style = 'ui-state-error';
		$msg = $wpdb->print_error();
	}
	header( "Content-Type: application/json" );
	echo json_encode(array('id' => $key, 'msg' => $msg, 'nonce' => $newNonce, 'style' => $style));//response
	exit;
} add_action('wp_ajax_update-template', 'update_template');


/* Unset a template option */

function delete_template() {
	$templates = get_option('templates', array());
	$key = $_POST['key'];
	$newNonce = wp_create_nonce();
	unset($templates[$key]);//remove this template from the array.
	if (update_option('templates', $templates) && check_admin_referer()) {
		$style = 'ui-state-highlight';
		$msg = $key . " updated.";
	}
	else {
		$style = 'ui-state-error';
		$msg = $wpdb->print_error();
	}
	header( "Content-Type: application/json" );
	echo $response = json_encode(array('msg' => $msg, 'nonce' => $newNonce, 'style' => $style));//response
	exit;
} add_action('wp_ajax_delete-template', 'delete_template');
