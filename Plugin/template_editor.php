<?php

function template_editor() {?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Template Editor</h2>
	<p>Create/edit site templates.</p>
	<div id="switcher"></div>
	<div id="notification">
		&nbsp;<!-- ajax notification area -->
	</div>
</div>
<div id="editor">
	<form action="<?php admin_url('admin-ajax.php')?>" method="post">
		<?php wp_nonce_field(); ?>
		<div id="canvas"></div>
	</form>
</div>
<!-- properties panel -->
<div id="propPanel" class="ui-widget-content">
<div id="heirs"><!-- dom tree indicator --></div>
<label for="txtID">ID: </label> <input id="txtID"
class="ui-widget-content ui-corner-all" type="text" /> <label
for="txtClass">Class: </label> <input id="txtClass"
class="ui-widget-content ui-corner-all" type="text" /> <label
for="txtTemplate">Template: </label> <input id="txtTemplate"
class="ui-widget-content ui-corner-all" type="text" />
<button id="newTemplate" type="button"
class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
role="button" aria-disabled="false">
<span class="ui-icon ui-icon-document" title="New">New</span>
</button>
<button id="openTemplate" type="button"
class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
role="button" aria-disabled="false">
<span class="ui-icon ui-icon-folder-open" title="Open">Open</span>
</button>
<button id="saveTemplate" type="button"
class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
role="button" aria-disabled="false">
<span class="ui-icon ui-icon-disk" title="Save">Save</span>
</button>
</div>
<?php
}

add_action('admin_init', 'template_editor_init');
add_action('admin_menu', 'template_editor_menu');

function template_editor_init() {
wp_deregister_script('jquery');
wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', '', '1.7.1');
wp_register_script('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.js', array('jquery'), '1.8.16');
wp_register_script('theme_switcher_tool', 'http://jqueryui.com/themeroller/themeswitchertool/', array('jquery', 'jquery-ui'));
wp_register_script('contextmenu', 'https://raw.github.com/arnklint/jquery-contextMenu/master/jquery.contextMenu.js', array('jquery'));
wp_register_script('fastfrag', 'https://raw.github.com/gregory80/fastFrag/master/js/fastFrag.min.js', array('jquery'));
wp_register_script('template_editor', plugins_url('/template_editor.js', __FILE__), array('jquery', 'jquery-ui', 'theme_switcher_tool', 'contextmenu', 'fastfrag'), '201202260207');
}
function add_template_editor_scripts() {
wp_enqueue_script('template_editor');
wp_enqueue_script( 'jquery-form' );
}
function add_template_editor_styles() {
wp_enqueue_style('editor-styles', plugins_url('/nirvana.css', __FILE__));
wp_enqueue_style('base-theme', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css');
}
function template_editor_menu() {
$page = add_theme_page('Template Editor',		// Menu title
'Template Editor',		// Page title
'edit_theme_options',	// Permissions
'template_editor',		// slug
'template_editor');		// callback
add_action('admin_print_scripts-' . $page, 'add_template_editor_scripts');
add_action('admin_print_styles-' . $page, 'add_template_editor_styles');
}

