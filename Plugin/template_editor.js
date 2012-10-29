$(window).load(function(){

	/*
	 * Properties panel styling
	 */

	$('#footer').html($('#propPanel'));
	$('button').on('mouseover', function() {
		$(this).addClass('ui-state-hover');
		$(this).addClass('ui-state-active');
	});
	$('button').on('mouseout', function() {
		$(this).removeClass('ui-state-hover');
		$(this).removeClass('ui-state-active');
	});


	/*
	 * set canvas height
	 */

	var height, y1, y2;
	y1 = $('#propPanel').offset().top;
	y2 = $('#canvas').offset().top;
	height = y1 - y2;
	$('#canvas').css('height', height);


	/*
	 * Manage sidebars
	 */

	$.post( ajaxurl, { action: 'get-sidebars'}, function(response) {//response may return false.
		$('#wpbody-content').append('<ul id="sidebars" title="Manage Sidebars">');//dialog container
		$.each(response, function(i) {//append sidebars
			$('#sidebars').append('<li><span class="mngSBI">' + i + '</span>'//index
					+ '<a class="mngSBN" href="javascript:void()">' + response.sidebar_name + '</a>'//name
					+' <span class="mngSBD"></span></li>');//delete
		});
		$('#sidebars').dialog({autoOpen: false});
	});
	$('#mngSB').on('click', function() {
		$('#sidebars').dialog('open');
	});


	/*
	 * Get Sidebar Form
	 */

	function getSidebarForm(sidebar) {
		$.post( ajaxurl, { action: 'get-sidebar-form', index: index }, function(response) {
			$('#wpbody-content').append(response);
		});
	}


	/*
	 * New sidebar
	 */

	function newSidebar() {
		getSidebarForm();
		$('#newSidebar').dialog({
			autoOpen: false,
			width: 343,
			buttons: {
				Insert: function() {
					sidebarSubmit(index);//sidebars.length
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function() {
				$('#newSidebar input[type=text]').dialog('destroy');
			}
		});
	}
	newSidebar();


	/*
	 * Update Sidebar
	 **/

	function updateSidebar(index, sidebar) {
		getSidebarForm(index);
		$(sidebar).dialog({
			autoOpen: false,
			width: 343,
			buttons: {
				Insert: function() {
					sidebarSubmit(index);
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function() {
				$(sidebar).dialog(destroy);
			}
		});
	}


	/**
	 * Delete Sidebar
	 * */

	function deleteSidebar(index) {
		$.post( ajaxurl, {action: 'get-sidebar-delete-form'}, function(response) {//get form
			$('#wpbody-content')
			.append('<form id="SBConfirm" action="' + ajaxurl + '" title="Delete Sidebar" method="post">')
			.children(':last')
			.append('<input type="hidden" name="action" value="delete-dynamic-sidebar">')//action
			.append('<input type="hidden" name="index" value="' + index + '">')//index
			.append(response)//nonce fields
			.append('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">'
					+ '</span>Delete this sidebar from the database?</p>')
			.dialog({
				buttons: {
					Yes: function() {
						sidebarSubmit($(this));//delete
					},
					No: function() {
						$(this).dialog('destroy');
					}
				}
			});
		}
	}


	/**
	 * Sidebar Submit
	 * */

	function sidebarSubmit(form) {
		$(form).ajaxSubmit({
			dataType: 'json',
			success: function(response) {
				$('#notification').attr('class', response.style).html(response.msg);
				//refresh contextMenu/manage dialog
			}
		});
	}


	/**
	 * add theme switcher
	 *  */

	$(document).ready(function(){
		$('#switcher').themeswitcher();
	});


	/**
	 * select element
	 * */

	$('#canvas').on('click', function(e) {
		var $target = $(e.target);//return target
		if ($target.hasClass('newElement')) {//remove new element background color
			$target.removeClass('newElement');
		}
		$('.selectedElement').removeClass('selectedElement');//un-highlight previously selected item
		$target.addClass('selectedElement');//highlight selected item
		$('#txtID').val($('.selectedElement').attr('id'));//add selected element's id to form
		$('#txtClass').val($('.selectedElement').attr('class'));//add selected element's classes to form
		if ($('#canvas').hasClass('selectedElement')) {// clear dom tree indicator when canvas is selected
			$('#heirs').html('&nbsp;');//add non-breaking space as a placeholder
		}
		else {
			var $heir = '<span class="ui-state-highlight ui-corner-all">' +e.target.localName;//tagname
			if ($('.selectedElement').attr('id')) {//add id if it exists
				$heir += '#' + $('.selectedElement').attr('id');
			}
			if ($('.selectedElement').attr('class')) {//add first class if it exists
				$heir += '.' + $('.selectedElement').attr('class');
			}
			$heir += '</span>';
			$('#heirs').html($heir);//replace dom tree with currently selected element
			$('.selectedElement').parentsUntil($('#canvas')).each(function(i, l) {//loop through heirarchy minus the canvas
				var $heirarchy = '<span class="ui-state-highlight ui-corner-all">' + l.localName;//tagname
				if ($(l).attr('id')) {//add id if it exists
					$heirarchy += '#' + $(l).attr('id');
				}
				if ($(l).hasClass()) {//add class if it exists
					$heirarchy += '.' + $(l).attr('class');
				}
				$heirarchy += '</span>&nbsp;&gt;&nbsp;';
				$('#heirs').prepend($heirarchy);//prepend each parent to the dom tree indicator
			});
		}
	});


	/*
	 * Context Menu
	 */

	$(document).each(function() {
	    $('#contextMenu').menu({
	        select: function(event, ui) {
	            switch (ui.item.children('a').text()) {
	            case 'new':
	                $('#updateSidebar').dialog('open');
	                updateTemplate();//TODO: pass select tab
	                break;
	            case 'manage':
	                $('#sidebars').dialog('open');
	                updateTemplate();
	                break;
	            case 'empty':
	                $('.selectedElement').empty();
	                updateTemplate();
	                break;
	            case 'remove':
	                if (!$('#canvas').hasClass('selectedElement')) {
	                    $('.selectedElement').remove();
	                }
	                updateTemplate();
	                break;
	            default:
	                $('.selectedElement').append('<div class="newElement" />');//TODO: Add id and class for widget area
	            }
	            $(this).hide();
	        },
	        input: $(this)
	    }).hide();
	}).on('contextmenu', function(event) {
	    var menu = $('#contextMenu');
	    if (menu.is(':visible')) {
	        menu.hide();
	        return false;
	    }
	    menu.menu().show().position({
	        my: 'left top',
	        of: event,
	        collision: 'flip'
	    }).focus().menu('blur');
	    $(document).one('click', function() {
	        menu.hide();
	    });
	    return false;
	});​
	

	/**
	 * Form Controls
	 * */

	$('#txtID').change(function() {//change or add selected element's id
		$('.selectedElement').attr('id', $(this).val());
	});
	$('#txtClass').change(function() {//change or add selected element's class (can add multiple classes seperated by a space)
		$('.selectedElement').attr('class', $(this).val());
	});
	$('#newTemplate').click(function(){
		$('#newTemplateForm').dialog('open');
	});
	$('#openTemplate').click(function(){
		//todo
	});
	$('#saveTemplate').click(function(){
		//FIXME: Move to updateTemplate();
		$('.selectedElement').removeClass('selectedElement');
	});


	/**
	 * New Template Form
	 * */

	$('#wpbody-content').append('<form id="newTemplateForm" action="' + ajaxurl + '" title="Template Name" method="post">');
	$('#newTemplateForm')
	.append('<p>Enter a name for the new template:</p>')
	.append('<input name="action" type="hidden" value="get-template-form">')
	.append('<input name="key" type="text">')
	.dialog({
		autoOpen: false,
		buttons: {
			Ok: function() {
				getTemplate('#newTemplateForm');
				$(this).dialog('close');
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			$('#newTemplate-form input[type=text]').val('');
		}
	});


	/**
	 * Update Template
	 * */

	function getTemplate(form) {
		$(form).ajaxSubmit({
			dataType: 'json',
			success: function(response) {
				$('#canvas').html(fastFrag.create(response.html));
				//append tab
			}
		});
	}

});