/*
 * This is the main script that will handle the zen builder tool
 */

function zenpbt_init_tinymce_A() {
	tinymce.init({
		selector : '.text_text',
		oninit : "setPlainText",
		//auto_focus: 'tool_content_text',
		//mode: "none",
		//mode : "specific_textareas",
		plugins: "wordpress wplink paste",
		theme : "modern",
		menubar : false,
		statusbar : false,
		toolbar1 : "bold italic underline strikethrough | bullist numlist | cut copy paste | link unlink | undo redo"
	});
}

function zenpbt_init_tinymce_B() {
	tinymce.init({
		selector : '.text_text_B',
		oninit : "setPlainText",
		//auto_focus: 'tool_content_text',
		//mode: "none",
		//mode : "specific_textareas",
		plugins: "wordpress wplink paste",
		theme : "modern",
		menubar : false,
		statusbar : false,
		toolbar1 : "bold italic underline strikethrough | bullist numlist | cut copy paste | link unlink | undo redo"
	});
}

function zenpbt_jump(h) {
	var top = document.getElementById(h).offsetTop;
	window.scrollTo(0, top);
}

function zenpbt_jumpToBottom() {
	jQuery("html, body").animate({ scrollTop: jQuery(document).height() }, "slow");
}

function zenpbt_getUniqueID() {
	var the_ids = [];
	
	jQuery('.zen_tool_main').each(function() {
		the_ids.push(parseInt(jQuery(this).attr('id')));
	});
	
	var i = 1;
	
	jQuery.each(the_ids, function(index, value) {
		if (jQuery.inArray(i, the_ids) === -1) {
			// not found, do nothing
		} else {
			// found
			i = i + 1;
		}
	});
	
	return i;
}

function zenpbt_update_or_publish_zenpage(e) {
	//e.preventDefault();
	
    var preview_html = jQuery.trim(jQuery('.preview_div').html());  
    
    // remove buttons, remove empty p tags
    var newdata1 = preview_html.replace(/<input type="button" value="X" class="remove_this_section button">/g, "");
    var newdata2 = newdata1.replace(/<input type="button" value="Edit" class="edit_this_section button">/g, "");
    var newdata3 = newdata2.replace(/<p><\/p>/g, "");
    
    var $jQuery_Object = jQuery(jQuery.parseHTML(newdata3));
    $jQuery_Object.remove('.sec_id_div');
    
    var new_content =  $jQuery_Object.prop('outerHTML');
    
    /*
    if (jQuery('.zen_tool_main').length == 0) {
    	var is_confirmed = confirm("You do not have any sections created. This will remove all content from this page. Continue?");	
    	if (is_confirmed == false) {
    		return false;
    	}
    	new_content = '';
    }
    */
    
    // update or create post
    
    jQuery.ajax({
    	url: ajaxurl,
    	data : {
	   		update_or_create: '1',
	   		post_content: new_content,
	   		post_title: jQuery('#title').val(),
	   		pid: jQuery('#post_ID').val(),
	   		action : "update_or_save_zen_page"
    	} , 
    	success : function(data) {
    		//alert( data );
    	},
    	error : function(data) {
    		//alert('error');
    	}
    });
    
    return true;
}

function zenpbt_clearTheForm() {
	jQuery('.tool_section_form').find('input, select').not(':input[type=button], :input[type=radio], :input[type=hidden], :input[type=submit], :input[type=reset]').val('');
}

function zenpbt_initDragAndDrop() {
	jQuery('#the_preview_div').sortable({
		axis: 'y',
		cursor: 'pointer',
		items: '.zen_tool_main'
	});
}


/*
 * Logic starts here after the document is ready
 */
jQuery(document).ready(function($) {

	// on load, if .zen_tool_main in #content, load html into .preview_div
	var currentcontent = $('#content').val();
	if (currentcontent.indexOf('zen_tool_main') != -1) {
		var $jQueryObject = $($.parseHTML(currentcontent));
		var pre = $jQueryObject.find('.row');
		$jQueryObject.find('.zen_tool_main').append('<input type="button" value="X" class="remove_this_section button"><input type="button" value="Edit" class="edit_this_section button">');
		$('.preview_div').html($jQueryObject.prop('outerHTML'));
	}
	
	// set image when they select add image
    $('.tool').on('click', '.set_custom_images', function(e) {
    	if ($('.set_custom_images').length > 0) {
    		if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
                e.preventDefault();
                var button = $(this);
                var id = button.prev();
                wp.media.editor.send.attachment = function(props, attachment) {
                    id.val(attachment.id);
                };
                wp.media.editor.open(button);
                return false;
            }
        }
    });
    
    // background type toggle
    var curr_background_type = $('input[name=background_type]:checked').val();
    if (curr_background_type == 'color') {
    	$('.background_type_color').show();
    } else if (curr_background_type == 'image') {
    	$('.background_type_image').show();
    }
    
    $('.tool').on('change', 'input[name=background_type]', function() {
    	if ($('input[name=background_type]:checked').val() == 'color') {
    		var this_div = $(this).parents('.tool_section_form');
    		$(this_div).find('.background_type_image').hide();
    	    $(this_div).find('.background_type_color').show();
    	} else if ($('input[name=background_type]:checked').val() == 'image') {
    		var this_div = $(this).parents('.tool_section_form');
    		$(this_div).find('.background_type_image').show();
    	    $(this_div).find('.background_type_color').hide();
    		
    	} else {
    		alert("Error - Could not determine background type");
    	}
    });
    
    // add new section A
    $('#sectionA').on('click', function(e) {
		e.preventDefault();
		$('#section_form_A').show();
		$('#section_form_B').hide();
		//$('.text_text').html('Enter body text...');
		zenpbt_init_tinymce_A();
		zenpbt_clearTheForm();
		
		if (tinymce.editors.length > 0) {
			//tinymce.get('text_text').setContent('Enter body text...');
		}
			
		var newid = zenpbt_getUniqueID();
		$('#section_id').val(newid);
		$('#sec_id_span_A').html("New Section");
		
		zenpbt_jump('section_form_A');
    });
    
    // add new section B
    $('#sectionB').on('click', function(e) {
		e.preventDefault();

		$('#section_form_A').hide();
		$('#section_form_B').show();
		//$('.text_text').html('Enter body text...');
		zenpbt_init_tinymce_B();
		zenpbt_clearTheForm();
		
		if (tinymce.editors.length > 0) {
			//tinymce.get('text_text').setContent('Enter body text...');
		}
			
		var newid = zenpbt_getUniqueID();
		$('#section_id').val(newid);
		$('#sec_id_span_A').html("New Section");
		
		zenpbt_jump('section_form_B');
    });
    
    $('#sectionC').on('click', function(e) {
		e.preventDefault();
		alert("This template has not been implemented yet.");
    });
    
    $('.start_over_section').on('click', function(e) {
    	e.preventDefault();
    	zenpbt_clearTheForm();
    	if (tinymce.editors.length > 0) {
    		tinymce.get('text_text').setContent('');
    		tinymce.get('text_text_B').setContent('');
    	}
    	$('#sec_id_span_A').html('New Section');
    	$('#section_id').val(zenpbt_getUniqueID());
    });
    
    // remove one section
    $('.preview_div').on('click', '.remove_this_section', function(e) {
    	$(this).parents('.zen_tool_main').remove();
    });
    
    /**************************************************************************
     * Edit This Section
     *************************************************************************/
    $('.preview_div').on('click', '.edit_this_section, .sec_id_div', function(e) {
    	
    	var classList = $(this).parents('.zen_tool_main').attr('class').split(/\s+/);
    	$.each(classList, function(index, item) {
    		if (item.indexOf("zen_tool_template_") >= 0) {
    			tempChar = item.substr(-1);
    		}
    	});

    	if (tempChar == 'A') {
    		$('#section_form_A').show();
    		$('#section_form_B').hide();
    	} else if (tempChar == 'B') {
    		$('#section_form_A').hide();
    		$('#section_form_B').show();
    	} else {
    		alert('Error - Could not get template ID.');
    		return;
    	}
    	
    	if (tempChar == 'A') {
    		zenpbt_init_tinymce_A();
    	} else if (tempChar == 'B') {
    		zenpbt_init_tinymce_B();
    	}

    	zenpbt_jump('section_form_' + tempChar);
    	
    	var formdiv = $('.tool_section_form');
    	var thisdiv = $(this).parents('.zen_tool_main');
    	
    	sid = thisdiv.attr('id');
    	$('#section_id').val(sid);
    	
    	if (tempChar == 'A') {
    		$('#sec_id_span_A').html(sid);
    	} else if (tempChar == 'B') {
    		$('#sec_id_span_B').html(sid);
    	}	
    	
    	// get h1 info
    	heading = thisdiv.find('.zen_tool_h1').html();
    	heading_color = thisdiv.find('.zen_tool_h1').attr("class");
    	var class_array_1 = heading_color.split(/\s+/);
    	$.each(class_array_1, function(index, value) {
    		if (value.indexOf("font_color_") >= 0) {
    			heading_color_value = value.substr(value.length - 1);
    		}
    	});
    	
    	// get p info
    	text = thisdiv.find('.zen_tool_pdiv').html();
    	text_color = thisdiv.find('.zen_tool_pdiv').attr("class");
    	var class_array_2 = text_color.split(/\s+/);
    	$.each(class_array_2, function(index, value) {
    		if (value.indexOf("font_color_") >= 0) {
    			text_color_value = value.substr(value.length - 1);
    		}
    	});
    	
    	// alignment info
    	if (tempChar == 'A') {
	    	text_align = thisdiv.find('.zen_tool_h1').css('text-align');
	    	$("#template_A_" + text_align).prop("checked", true);
	    	//$("input[name=text-align-type][value=" + text_align + "]").prop('checked', true);
    	} else if (tempChar == 'B') {
    		text_align = thisdiv.find('.tempB_text_div').css('float');
    		if (text_align != 'left' && text_align != 'right') {
    			$("input[name=text-align-type][value='center']").prop('checked', true);
    		} else {
    			$("input[name=text-align-type][value=" + text_align + "]").prop('checked', true);
    		}
    	}
    	
    	// get background info
    	if (tempChar == 'A') {
		    background_color = thisdiv.attr("class");
		    background_imageID = thisdiv.css('background-image');
		    var class_array_3 = background_color.split(/\s+/);
		    $.each(class_array_3, function(index, value) {
		    	if (value.indexOf("background_color_") >= 0) {
		    		background_color_value = value.substr(value.length - 1);
		    	}
		    });
    	} else if (tempChar == 'B') {
    		background_color = thisdiv.find('.tempB_text_div').attr("class");
    		background_imageID = thisdiv.css('background-image');
    		var class_array_3 = background_color.split(/\s+/);
		    $.each(class_array_3, function(index, value) {
		    	if (value.indexOf("background_color_") >= 0) {
		    		background_color_value = value.substr(value.length - 1);
		    	}
		    });
    	}
    	
    	
    	if (tempChar == 'A') {
	    	if (background_imageID == 'none') { 
	    		background_type = "color";
	    		$('.background_type_color').show();
	        	$('.background_type_image').hide();
	        	$("#color-radio").prop("checked", true);
	    		formdiv.find( "#bkgd_color" ).val(background_color_value);
	    	} else {
	    		background_type = "image";
	    		$('.background_type_color').hide();
	        	$('.background_type_image').show();
	        	$("#img-radio").prop("checked", true);
	        	
	        	var imgurl1 = background_imageID.replace('url("', '');
	        	var imgurl2 = background_imageID.replace('")', '');       
	        	
	        	$.ajax({
	        		url : ajaxurl,
	        		data : { 
	        			img_url : imgurl2,
	        			action : "code_get_imageID" 
	        		},
	        		success : function(data) {
	        			$('#process_custom_images').val(data);
	        		}
	        	});
	    	}  
	    
    	} else if (tempChar == 'B') {
    		
    		formdiv.find( "#bkgd_color" ).val(background_color_value);
    		var imgurl1 = background_imageID.replace('url("', '');
        	var imgurl2 = background_imageID.replace('")', '');       
        	
        	// post to edit a section B
        	$.ajax({
        		url : ajaxurl,
        		data : { 
        			img_url : imgurl2,
        			action : "code_get_imageID" 
        		},
        		success : function(data) {
        			
        			if ($.isNumeric(data)) {
            			$('#section_form_B').find('#process_custom_images').val(data);
            		} else {
            			// alert("Error - could not get image ID");
            		}
        		}
        	});
    	}
    	
    	// put data in form
    	formdiv.find( "input[name='heading_text']" ).val(heading);
    	formdiv.find( '#heading_color' ).val(heading_color_value);
    	
    	if (tempChar == 'A') {
    		setTimeout( function() {
    			tinymce.get('text_text').setContent(text);
    		}, 1000);
    		
    	} else if (tempChar == 'B') {
    		setTimeout( function() {
    			tinymce.get('text_text_B').setContent(text);
			}, 1000);
    	}
    	
    	formdiv.find( "#text_color" ).val(text_color_value);    	
    	
    	zenpbt_jump('section_form_' + tempChar);
    });

    // hide form
    $('.tool').on('click', '.remove_section', function(e) {
    	e.preventDefault();
    	$(this).parents('.tool_section').hide();
    });
    
    // PUBLISH OR UPDATE
    $('#publish').on('click', function(e) {
    	zenpbt_update_or_publish_zenpage(e);
    });
    
    // toggle instructions 
    $('#show_instructions').on('click', function(e) {
    	$('.instruction_div').show();
    	$('#hide_instructions').show();
    	$('#show_instructions').hide();
    });
    
    $('#hide_instructions').on('click', function(e) {
    	$('.instruction_div').hide();
    	$('#hide_instructions').hide();
    	$('#show_instructions').show();
    });
    
    /**************************************************************************
     * Add To Preview - Template A
     *************************************************************************/
    $('.tool').on('click', '#create_section_A', function(e) {
    	e.preventDefault();

    	var this_div = $(this).parents('.tool_section_form');
    	
    	// id
    	var num = $('#section_id').val();

    	// heading
    	heading = this_div.find( "input[name='heading_text']" ).val();
    	color_heading = this_div.find( "#heading_color option:selected" ).val();
    	 		
    	// text
    	text = tinymce.get('text_text').getContent();
    	color_txt = this_div.find( "#text_color option:selected" ).val();		
    		
    	// settings
    	text_align = this_div.find( "input[name='text-align-type']:checked" ).val();
    	
    	// background
    	bkg_type = this_div.find( "input[name='background_type']:checked" ).val();
    	color_background = this_div.find( "#bkgd_color option:selected" ).val();
    	bkg_image_id = this_div.find( "input[name='bkgd_image']" ).val();

    	/*
    	$.post('/wp-content/plugins/zen_page_builder_tool/inc/custom-builder.php', {
    		temp_A: 1,
    		sec_id: num,
    		
    		head: heading,
    		color_h1: color_heading,
    		
    		content: text,
    		color_p: color_txt,
    		text_align: text_align,
    		
    		background_type: bkg_type,    
    		color_bkg: color_background,   		  		    					
    		bkg_imgID: bkg_image_id
    			
    		} , function(data) {
    			var newdata = data.replace('<p><p>', '<p>');
    			newdata = newdata.replace('</p></p>', '</p>');
    			newdata = newdata.replace('<p></p>', '');
    			
    			// if num found, change that sec, else, append
    			if ($("#" + $('#section_id').val()).length > 0) {
    				$('#' + $('#section_id').val()).replaceWith($.trim(newdata));
    			} else {
    				$('.preview_div').find('.row').append(newdata);
    			}
    			
    			var newnum = zenpbt_getUniqueID();
    			$('#section_id').val(newnum);
    			$('#sec_id_span_A').html("New Section");
    		}
    	);
    	*/
    	
    	$.ajax({
    		url : ajaxurl,
    		data : { 
    			temp_A: 1,
        		sec_id: num,
        		
        		head: heading,
        		color_h1: color_heading,
        		
        		content: text,
        		color_p: color_txt,
        		text_align: text_align,
        		
        		background_type: bkg_type,    
        		color_bkg: color_background,   		  		    					
        		bkg_imgID: bkg_image_id,
        		
    			action : "build_zen_section" 
    		},
    		success : function(data) {
    			var newdata = data.replace('<p><p>', '<p>');
    			newdata = newdata.replace('</p></p>', '</p>');
    			newdata = newdata.replace('<p></p>', '');
    			
    			// if num found, change that sec, else, append
    			if ($("#" + $('#section_id').val()).length > 0) {
    				$('#' + $('#section_id').val()).replaceWith($.trim(newdata));
    			} else {
    				$('.preview_div').find('.row').append(newdata);
    			}
    			
    			var newnum = zenpbt_getUniqueID();
    			$('#section_id').val(newnum);
    			$('#sec_id_span_A').html("New Section");
    		}
    	});
    	
    	
    	zenpbt_clearTheForm();
    	if (tinymce.editors.length > 0) {
    		tinymce.get('text_text').setContent('');
    	}
    	//zenpbt_jump('the_preview_div');
    	//zenpbt_jumpToBottom();
    	$('.tool_section').hide();
    });	
    
    /**************************************************************************
     * Add To Preview - Template B
     *************************************************************************/
    $('.tool').on('click', '#create_section_B', function(e) {
    	e.preventDefault();
    	
    	var this_div = $(this).parents('.tool_section_form');
    	
    	// id
    	var num = $('#section_id').val();

    	// heading
    	heading = this_div.find( "input[name='heading_text']" ).val();
    	color_heading = this_div.find( "#heading_color option:selected" ).val();
    	 		
    	// text
    	text = tinymce.get('text_text_B').getContent();
    	
    	color_txt = this_div.find( "#text_color option:selected" ).val();
    	color_background = this_div.find( "#bkgd_color option:selected" ).val();
    		
    	// settings
    	text_align = this_div.find( "input[name='text-align-type']:checked" ).val();
    	
    	// background
    	//bkg_type = this_div.find( "input[name='background_type']:checked" ).val();
    	//color_background = this_div.find( "#bkgd_color option:selected" ).val();
    	bkg_image_id = this_div.find( "input[name='bkgd_image']" ).val();
    	
    	$.ajax({
    		url : ajaxurl,
    		data : {
	    		temp_B: 1,
	    		sec_id: num,
	    		
	    		head: heading,
	    		color_h1: color_heading,
	    		
	    		content: text,
	    		color_p: color_txt,
	    		text_align: text_align,
	    		   
	    		color_bkg: color_background,   		  		    					
	    		bkg_imgID: bkg_image_id,
	    		
	    		action : "build_zen_section"
    		} , 
    		success : function(data) {
    			var newdata = data.replace('<p><p>', '<p>');
    			newdata = newdata.replace('</p></p>', '</p>');
    			newdata = newdata.replace('<p></p>', '');
    			
    			// if num found, change that sec, else, append
    			if ( $("#" + $('#section_id').val()).length > 0 ) {
    				$('#' + $('#section_id').val()).replaceWith( $.trim(newdata) );
    			} else {
    				$('.preview_div').find('.row').append( newdata );
    			}
    			
    			var newnum = zenpbt_getUniqueID();
    			$('#section_id').val(newnum);
    			$('#sec_id_span_A').html("New Section");
    		}
    	});
    	
    	zenpbt_clearTheForm();
    	if (tinymce.editors.length > 0) {
    		tinymce.get('text_text_B').setContent('');
    	}
    	//zenpbt_jump('the_preview_div');
    	//zenpbt_jumpToBottom();
    	$('.tool_section').hide();
    });
	
	zenpbt_initDragAndDrop();
});