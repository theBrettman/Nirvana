<?php

/**
 * Return array of sidebars
 * */

function get_sidebars() {
	header( "Content-Type: application/json" );
	echo json_encode(get_option('dynamic_sidebars'));//if there is no option 'dynamic_sidebars', it conveniently returns false.
	exit;
}
add_action('wp_ajax_get-sidebars', 'get_sidebars');


/**
 * Return Sidebar form
 * */

function get_sidebar_form() {
	$dynamic_sidebars = get_option('dynamic_sidebars');
	$sidebar_id = isset($_POST['sidebar_id']);
	$dynamic_sidebar = ( $sidebar_id && isset($dynamic_sidebars[$sidebar_id]))
	? $dynamic_sidebars[$sidebar_id] : false;//if sidebar is already in database, fill out the form; else, return a blank form.
	$index = $_POST['index'];
	$dialog_id = ( $dynamic_sidebar ) ? "edit-" . $dynamic_sidebar['sidebar_name'] : "newSidebar";
	$dialog_title = ( $dynamic_sidebar ) ? "Edit Sidebar" : "New Sidebar";
	?>
<form id="<?php echo $dialog_id; ?>"
	title="<?php echo $dialog_title; ?>"
	action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="post">
	<?php wp_nonce_field('set'); ?>
	<input type="hidden" name="action" value="update-dynamic-sidebar"> <label><span>Name:
	</span><input type="text" name="sidebar_name" id="sidebar_name"
		class="ui-widget-content ui-corner-all"
		value="<?php if ($dynamic_sidebar) echo $dynamic_sidebar['sidebar_name']; ?>">
	</label> <label><span>ID: </span><input type="text" name="sidebar_id"
		id="sidebar_id" class="ui-widget-content ui-corner-all"
		value="<?php if ($dynamic_sidebar) echo $dynamic_sidebar['sidebar_id']; ?>">
	</label> <label><span>Description: </span><input type="text"
		name="description" id="description"
		class="ui-widget-content ui-corner-all"
		value="<?php if ($dynamic_sidebar) echo $dynamic_sidebar['description']; ?>">
	</label> <label><span>Before widget:&nbsp;</span><input type="text"
		name="before_widget" id="before_widget"
		class="ui-widget-content ui-corner-all"
		value="<?php if ($dynamic_sidebar) echo $dynamic_sidebar['before_widget']; ?>">
	</label> <label><span>After widget: </span><input type="text"
		name="after_widget" id="after_widget"
		class="ui-widget-content ui-corner-all"
		value="<?php if ($dynamic_sidebar) echo $dynamic_sidebar['after_widget']; ?>">
	</label> <label><span>Before title: </span><input type="text"
		name="before_title" id="before_title"
		class="ui-widget-content ui-corner-all"
		value="<?php if ($dynamic_sidebar) echo $dynamic_sidebar['before_title']; ?>">
	</label> <label><span>After title: </span><input type="text"
		name="after_title" id="after_title"
		class="ui-widget-content ui-corner-all"
		value="<?php if ($dynamic_sidebar) echo $dynamic_sidebar['after_title']; ?>">
	</label>
</form>
<?php exit; 
} add_action('wp_ajax_get-sidebar-form', 'get_sidebar_form');


/**
 * Update dynamic_sidebar option
 * */

function update_dynamic_sidebar() {
	$dynamic_sidebars = get_option('dynamic_sidebars', array());
	$index = $_POST['index'];//if it's a new index, it comes from array count on the client side.
	$dynamic_sidebars[$index] = array(//only creating/changing the values for this index of the array.
			'sidebar_name' => $_POST['sidebar_name'],
			'sidebar_id' => $_POST['sidebar_id'],
			'description' => $_POST['description'],
			'before_widget' => $_POST['before_widget'],
			'after_widget' => $_POST['after_widget'],
			'before_title' => $_POST['before_title'],
			'after_title' => $_POST['after_title']
	);
	if (update_option('dynamic_sidebars', $dynamic_sidebars) && check_admin_referer('set')) {
		$style = 'ui-state-highlight';
		$msg = $_POST['sidebar_name'] . " updated.";
	}
	else {
		$style = 'ui-state-error';
		$msg = $wpdb->print_error();
	}
	header( "Content-Type: application/json" );
	echo json_encode(array('id' => $index, 'msg' => $msg, 'style' => $style));//response
	exit;
} add_action('wp_ajax_update-dynamic-sidebar', 'update_dynamic_sidebar');


/**
 * Get delete form
 * */

function get_sidebar_delete_form() {
	wp_nonce_field('unset');
}
add_action('wp_ajax_get-sidebar-delete-form', 'get_sidebar_delete_form');


/**
 * Unset a dynamic_sidebar option
 * */

function delete_dynamic_sidebar() {
	$dynamic_sidebars = get_option('dynamic_sidebars', array());
	$index = $_POST['index'];
	$name = $index['sidebar_name'];
	unset($dynamic_sidebars[$index]);//remove this index from the array.
	if (update_option('dynamic_sidebars', $dynamic_sidebars) && check_admin_referer('unset')) {
		$style = 'ui-state-highlight';
		$msg = $name . " updated.";
	}
	else {
		$style = 'ui-state-error';
		$msg = $wpdb->print_error();
	}
	header( "Content-Type: application/json" );
	echo $response = json_encode(array('msg' => $msg, 'style' => $style));//response
	exit;
} add_action('wp_ajax_delete-dynamic-sidebar', 'delete_dynamic_sidebar');
