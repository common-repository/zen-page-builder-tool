<?php 
/*
 Plugin Name: Zen Page Builder Tool
 Description: The Zen Page Builder Tool quickly allows you to create beautiful pages built from sections containing images, text, and more!
 Version:     1.0
 Author:      Corporate Zen
 Author URI:  http://www.corporatezen.com/
 License:     GPL2
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 
 Zen Page Builder Tool is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 any later version.
 
 Zen Page Builder Tool is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with Zen Page Builder Tool. If not, see https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or die( 'Error: Direct access to this code is not allowed.' );

require_once( 'inc/customizer.php' );

// de-activate hook
function zenpbt_deactivate_plugin() {
	// clear the permalinks to remove our post type's rules
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'zenpbt_deactivate_plugin' );


// activation hook
function zenpbt_active_plugin() {
	// trigger our function that registers the custom post type
	zenpbt_create_zenpage_posttype();
	
	// clear the permalinks after the post type has been registered
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'zenpbt_active_plugin' );

// Our custom post type function
function zenpbt_create_zenpage_posttype() {
	
	$labels = array(
			'name'                => 'Zen Pages',
			'singular_name'       => 'Zen Page',
			'menu_name'           => 'Zen Pages',
			'all_items'           => 'All Zen Pages',
			'view_item'           => 'View Zen Page',
			'add_new_item'        => 'Add New Zen Page',
			'add_new'             => 'Add New',
			'edit_item'           => 'Edit Zen Page',
			'update_item'         => 'Update Zen Page',
			'search_items'        => 'Search Zen Pages',
			'not_found'           => 'Not Found',
			'not_found_in_trash'  => 'Not found in Trash'
	);
	
	$args = array(
			'labels'             => $labels,
			'menu_icon'          => 'dashicons-welcome-write-blog',
			'description'        => 'Pages built with our custom made Zen Builder Tool',
			'public'             => true,
			'publicly_queryable' => true,
			'show_in_nav_menus'  => true,
			'show_in_nav_menus'  => true,
			'capability_type'    => 'page',
			'map_meta_cap'       => true,
			'menu_position'      => 20,
			'hierarchical'       => false,
			'rewrite'            => false,
			'query_var'          => false,
			'delete_with_user'   => false,
			'supports'           => array( 'title', 'editor', 'author', 'revisions' ),
			'show_in_rest'       => true,
			'rest_base'          => 'pages',
			'rest_controller_class' => 'WP_REST_Posts_Controller'
	);
	
	register_post_type( 'zen_page', $args );
}
add_action( 'init', 'zenpbt_create_zenpage_posttype' );

// enqueue our dynamic style
add_action( 'wp_enqueue_scripts', 'zenpbt_enqueue_styles' );
function zenpbt_enqueue_styles() {
	wp_enqueue_style( 'dynamic-style', plugin_dir_url(__FILE__) . 'css/dynamic_style.css' );
}

function zenpbt_load_custom_wp_admin_style() {
	wp_enqueue_script( 'jquery-ui-droppable' );
}
add_action( 'admin_enqueue_scripts', 'zenpbt_load_custom_wp_admin_style' );

// run set up on these hooks
add_action( 'load-post.php', 'zenpbt_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'zenpbt_post_meta_boxes_setup' );

// main meta set up function
function zenpbt_post_meta_boxes_setup() {
	add_action ( 'add_meta_boxes', 'zenpbt_add_post_meta_boxes' );
	add_action ( 'save_post', 'zenpbt_save_meta', 10, 2 );
}

// add meta box
function zenpbt_add_post_meta_boxes() {
	add_meta_box ( 'zen-meta-info', __( 'Zen Builder Tool', 'zen_textdomain' ), 'zenpbt_fill_meta_box', 'zen_page', 'normal', 'high' );
}

// put content into the meta box
function zenpbt_fill_meta_box( $post ) {
	
	wp_enqueue_style ( 'zen_bootstrap_css', plugin_dir_url(__FILE__) . 'css/tool-grid.css' );
	wp_enqueue_script ( 'custom-builder'  , plugin_dir_url(__FILE__) . 'js/custom-builder.js', array( 'jquery' ) );
	
	$options = get_option ( 'zenpbt_options' );
	$colors = get_option ( 'zenpbt_colors' );
	
	$scheme = $options['color_scheme'];
	
	// style for previewing while the user is in the backend
	$style = '';
	if ( isset($colors) && is_array($colors) ) {
		$style = '<style>
				:root {
					--primary-color:  ' . ( isset ( $colors['color_1'] ) ? $colors['color_1'] : '' ). ' !important;
					--secondary-color:' . ( isset ( $colors['color_2'] ) ? $colors['color_2'] : '' ). ' !important;
					--tertiary-color: ' . ( isset ( $colors['color_3'] ) ? $colors['color_3'] : '' ). ' !important;
					--accent-color-1: ' . ( isset ( $colors['color_4'] ) ? $colors['color_4'] : '' ). ' !important;
					--accent-color-2: ' . ( isset ( $colors['color_5'] ) ? $colors['color_5'] : '' ). ' !important;
 		
					--black-color: black;
					--white-color: white;
				}

				.text-left {
				  text-align: left;
				  padding-left: 5px;
				}
				.text-right {
				  text-align: right;
				  padding-right: 5px;
				}
				.text-center {
				  text-align: center;
				}
				</style>';
	}

	echo $style;
	?>
	
<div class="entire_tool">

	<div class="insruction_links">
		<a id="show_instructions">Show Instructions</a>
		<a id="hide_instructions" style="display: none;">Hide Instructions</a>
	</div>
	
	<div class="instruction_div" style="display: none;">
		<strong><u>Instructions:</u></strong>
		<p>1. Start by selecting a template below. A form will appear allowing you to enter your text, choose your colors, text alignment, and more.</p>
		<p>2. Once you fill out the form, hit the blue <b>"Add To Preview"</b> button, and a preview will let you see what your section will look like when you choose to save.</p>
		<p>3. Once you have created a section, you can delete or edit that section by hitting the <b>"X"</b> or <b>"Edit"</b> buttons in the preview. You can also add more sections, or use drag and drop to re-arrange sections.</p>
		<p>4. After you have created something you are happy with, be sure to hit <b>"Publish"</b> or <b>"Update"</b> to save your work.
		<br>
		<br>
		<strong><u>Notes:</u></strong> 
		<ul style="list-style: disc;margin-left: 15px;">
			<li>To help you use this tool, each section you create will have a unique ID, visable in the top left of the preview, above the delete and edit buttons. </li>
			<li>In the form, on the top in bold, you can see what you are "Currently Editing". 
			This will show "New Section" if the section will be added to the bottom, or it will show the ID of the section you are editing. 
			This way, if for some reason it becomes unclear which section you are editing, you can always refer to this area.</li>
			<li>You will notice various dropdowns for color selection. Which colors are displayed in these dropdowns are directly related to the colors you select in the options. Go to "Appearance" -> "Customize" -> "Zen Page Builder: Color Scheme" to select your colors.</li>
		</ul>
	</div>
	
	<div class="template_icon_div">
	<h2 class="inline_heading">Add New Section: </h2>
		<a id="sectionA" class="tool-link add-new-section-link"><img class="template_icon" id="template_A" src="<?php echo plugin_dir_url(__FILE__); ?>images/standard.png"></a>
		<a id="sectionB" class="tool-link add-new-section-link"><img class="template_icon" id="template_B" src="<?php echo plugin_dir_url(__FILE__); ?>images/column.png"></a>
		<a id="sectionC" class="tool-link add-new-section-link"><img class="template_icon" id="template_C" src="<?php echo plugin_dir_url(__FILE__); ?>images/small_image.png"></a>
	</div>
	
	<div class="tool" id="<?php echo $post->ID; ?>">
	
		<?php wp_nonce_field( basename( __FILE__ ), 'zentheme_nonce' ); ?>
		
		<?php 
		/*
		 * ============================================================================================================================================================
		 * Template A
		 * ============================================================================================================================================================
		 */
		?>
		<div style="display:none;" class="tool_section" id="section_form_A">
			<div class="tool_section_form">
			
			<div class="currently_editing">Currently Editing: <span id="sec_id_span_A">New Section</span></div>
				
				<input type="hidden" value="0" class="" id="section_id">
				
				<div class="section_area">
					
					Section Heading: 
					<input type="text" name="heading_text" class="heading_text widefat" placeholder="Enter Section Header" value="<?php echo esc_attr( get_post_meta( $post->ID, 'heading_text', true ) ); ?>"/>
					
					Heading Color: 
					<select name="heading_color" id="heading_color">
						<?php $v = get_post_meta( $post->ID, 'heading_color', true ); ?>
						<option <?php if ($v == false) echo 'selected'; ?> value="">--- Please Select ---</option>
						<option <?php if ($v == 6) echo 'selected'; ?> value="6" style="color: #ffffff; background-color: #2d2d2d">#2d2d2d</option>
						<option <?php if ($v == 7) echo 'selected'; ?> value="7" style="color: #2d2d2d; background-color: #ffffff">#ffffff</option>
						<?php
						$i = 0;							
						foreach ($colors as $color) {
							$i++;
							$selected = ($i == $v ? 'selected' : '');
							echo '<option ' . $selected . ' style="color: white; background-color: ' . $color . '" class="heading_color" value="' . $i . '">' . $color . '</option>';
						}
						?>
					</select>
				</div>
				
				<div class="section_area" id="section_area_background">
				
					<div class="background_type_select_area">
						Background:<br>
						<input type="radio" id="color-radio" name="background_type" value="color" <?php if (esc_attr( get_post_meta( $post->ID, 'bkgd_type', true ) != 'image')) echo "checked"; ?>>Color
	  					<input type="radio" id="img-radio" name="background_type" value="image" <?php if (esc_attr( get_post_meta( $post->ID, 'bkgd_type', true ) == 'image')) echo "checked"; ?>>Image
					</div>
					
					<div class="background_type_area">
						<span class="background_type_color" style="display:none;">
							Background Color: <br>
							<select name="bkgd_color" id="bkgd_color">
								<?php $v = get_post_meta( $post->ID, 'bkgd_color', true );  ?>
								<option <?php if ($v == false) echo 'selected'; ?> value="">--- Please Select ---</option>
								<option <?php if ($v == 6) echo 'selected'; ?> value="6" style="color: #ffffff; background-color: #2d2d2d">#2d2d2d</option>
								<option <?php if ($v == 7) echo 'selected'; ?> value="7" style="color: #2d2d2d; background-color: #ffffff">#ffffff</option>
								<?php 
								$i = 0;
								foreach ($colors as $color) {
									$i++;
									$selected = ($i == $v ? 'selected' : '');
									echo '<option ' . $selected . ' style="color: white; background-color: ' . $color . '" class="text_color" value="' . $i. '">' . $color . '</option>';
								}
								?>
							</select>
						</span>
						<span class="background_type_image" style="display:none;">Background Image: <br><input type="number" value="<?php echo esc_attr( get_post_meta( $post->ID, 'bkgd_image', true ) ); ?>" name="bkgd_image" class="regular-text process_custom_images" id="process_custom_images" max="" min="1" step="1" /> <button class="set_custom_images button">Add Image</button></span>
					</div>
					
				</div>
				
				<div class="section_area">
					Body Text
					<div id="body_text_section">
						
						<div id="tiny_mimic_background">
							Text Color: 
							<select name="text_color" id="text_color">
								<?php $v = get_post_meta( $post->ID, 'text_color', true ); ?>
								<option <?php if ($v == false) echo 'selected'; ?> value="">--- Please Select ---</option>
								<option <?php if ($v == 6) echo 'selected'; ?> value="6" style="color: #ffffff; background-color: #2d2d2d">#2d2d2d</option>
								<option <?php if ($v == 7) echo 'selected'; ?> value="7" style="color: #2d2d2d; background-color: #ffffff">#ffffff</option>
								<?php 
								$i = 0;							
								foreach ($colors as $color) {
									$i++;
									$selected = ($i == $v ? 'selected' : '');
									echo '<option ' . $selected . ' style="color: white; background-color: ' . $color . '" class="text_color" value="' . $i. '">' . $color . '</option>';
								}
								?>
							</select>
							
							<div id="body_text_radios">
								Align: 
								<input type="radio" name="text-align-type" id="template_A_left" value="left" <?php if (esc_attr( get_post_meta( $post->ID, 'text_align', true ) == 'left')) echo "checked"; ?>>Left
			  					<input type="radio" name="text-align-type" id="template_A_center" value="center" <?php if (esc_attr( get_post_meta( $post->ID, 'text_align', true ) != 'left') && esc_attr( get_post_meta( $post->ID, 'text_align', true ) != 'right')) echo "checked"; ?>>Center
			  					<input type="radio" name="text-align-type" id="template_A_right" value="right" <?php if (esc_attr( get_post_meta( $post->ID, 'text_align', true ) == 'right')) echo "checked"; ?>>Right<br>
							</div>
						</div>
							
						<textarea name="text_text" class="text_text" rows="6" cols="100"><?php echo esc_attr( get_post_meta( $post->ID, 'text_text', true ) ); ?></textarea>
										
						</div>
					</div>						
				
				<div class="section_buttons">				
					<input type="submit" value="Add to Preview" id="create_section_A" class="create_section button button-primary" style="float:right;"/>
				    <input type="button" value="Hide Form" class="remove_section button" style="margin-right: 10px;" /> 
				    <input type="button" value="Clear Form" class="start_over_section button" /> 
				    <div class="clear"></div>
				</div>
							
			</div>
		</div>
		
		
		<?php 
		/*
		 * ============================================================================================================================================================
		 * Template B
		 * ============================================================================================================================================================
		 */
		?>
		
		<div style="display:none;" class="tool_section" id="section_form_B">
			<div class="tool_section_form">
			
			<div class="currently_editing">Currently Editing: <span id="sec_id_span_B">New Section</span></div>
				
				<input type="hidden" value="0" class="" id="section_id">
				
				<div class="section_area">
					
					Section Heading: 
					<input type="text" name="heading_text" class="heading_text widefat" placeholder="Enter Section Header" value="<?php echo esc_attr( get_post_meta( $post->ID, 'heading_text', true ) ); ?>"/>
					
					Heading Color: 
					<select name="heading_color" id="heading_color">
						<?php $v = get_post_meta( $post->ID, 'heading_color', true ); ?>
						<option <?php if ($v == false) echo 'selected'; ?> value="">--- Please Select ---</option>
						<option <?php if ($v == 6) echo 'selected'; ?> value="6" style="color: #ffffff; background-color: #2d2d2d">#2d2d2d</option>
						<option <?php if ($v == 7) echo 'selected'; ?> value="7" style="color: #2d2d2d; background-color: #ffffff">#ffffff</option>
						<?php
						$i = 0;							
						foreach ($colors as $color) {
							$i++;
							$selected = ($i == $v ? 'selected' : '');
							echo '<option ' . $selected . ' style="color: white; background-color: ' . $color . '" class="heading_color" value="' . $i . '">' . $color . '</option>';
						}
						?>
					</select>
				</div>
				
				<div class="section_area" id="section_area_background">
					
					<div class="background_type_area">
						<span class="background_type_image_B">Background Image: <br><input type="number" value="<?php echo esc_attr( get_post_meta( $post->ID, 'bkgd_image', true ) ); ?>" name="bkgd_image" class="regular-text process_custom_images" id="process_custom_images" max="" min="1" step="1" /> <button class="set_custom_images button">Add Image</button></span>
						<br>
						<span class="background_type_color">
							Text Background Color: <br>
							<select name="bkgd_color" id="bkgd_color">
								<?php $v = get_post_meta( $post->ID, 'bkgd_color', true );  ?>
								<option <?php if ($v == false) echo 'selected'; ?> value="">--- Please Select ---</option>
								<option <?php if ($v == 6) echo 'selected'; ?> value="6" style="color: #ffffff; background-color: #2d2d2d">#2d2d2d</option>
								<option <?php if ($v == 7) echo 'selected'; ?> value="7" style="color: #2d2d2d; background-color: #ffffff">#ffffff</option>
								<?php 
								$i = 0;
								foreach ($colors as $color) {
									$i++;
									$selected = ($i == $v ? 'selected' : '');
									echo '<option ' . $selected . ' style="color: white; background-color: ' . $color . '" class="text_color" value="' . $i . '">' . $color . '</option>';
								}
								?>
							</select>
						</span>
					</div>
					
				</div>
				
				<div class="section_area">
					Body Text
					<div id="body_text_section">
						
						<div id="tiny_mimic_background">
							Text Color: 
							<select name="text_color" id="text_color">
								<?php $v = get_post_meta( $post->ID, 'text_color', true ); ?>
								<option <?php if ($v == false) echo 'selected'; ?> value="">--- Please Select ---</option>
								<option <?php if ($v == 6) echo 'selected'; ?> value="6" style="color: #ffffff; background-color: #2d2d2d">#2d2d2d</option>
								<option <?php if ($v == 7) echo 'selected'; ?> value="7" style="color: #2d2d2d; background-color: #ffffff">#ffffff</option>
								<?php 
								$i = 0;							
								foreach ($colors as $color) {
									$i++;
									$selected = ($i == $v ? 'selected' : '');
									echo '<option ' . $selected . ' style="color: white; background-color: ' . $color . '" class="text_color" value="' . $i. '">' . $color . '</option>';
								}
								?>
							</select>
							
							<div id="body_text_radios">
								Align: 
								<input type="radio" name="text-align-type" value="left" <?php if (esc_attr( get_post_meta( $post->ID, 'text_align', true ) == 'left')) echo "checked"; ?>>Left
			  					<input type="radio" name="text-align-type" value="center" <?php if (esc_attr( get_post_meta( $post->ID, 'text_align', true ) != 'left') && esc_attr( get_post_meta( $post->ID, 'text_align', true ) != 'right')) echo "checked"; ?>>Center
			  					<input type="radio" name="text-align-type" value="right" <?php if (esc_attr( get_post_meta( $post->ID, 'text_align', true ) == 'right')) echo "checked"; ?>>Right<br>
							</div>
						</div>
							
						<textarea name="text_text_B" class="text_text_B" rows="6" cols="100"><?php echo esc_attr( get_post_meta( $post->ID, 'text_text', true ) ); ?></textarea>
										
						</div>
					</div>						
				
				<div class="section_buttons">				
					<input type="submit" value="Add to Preview" id="create_section_B" class="create_section button button-primary" style="float:right;"/>
				    <input type="button" value="Hide Form" class="remove_section button" style="margin-right: 10px;" /> 
				    <input type="button" value="Clear Form" class="start_over_section button" /> 
				    <div class="clear"></div>
				</div>
							
			</div>
		</div>
				
		
	</div><!-- END .tool -->
	
		<div class="preview_div" id="the_preview_div">
			<div class="container-fluid">
				<div class="row">
				
				</div><div class="clear"></div></div></div>
	
</div><!-- END .entire_tool -->
<?php
}

function zenpbt_save_meta( $post_id, $post ) {
	
	// Verify the nonce before proceeding.
	if ( !isset( $_POST[' _nonce'] ) || !wp_verify_nonce( $_POST['zentheme_nonce'], basename( __FILE__ ) ) )
		return $post_id;
		
		// Get the post type object.
		$post_type = get_post_type_object( $post->post_type );
		
		// Check if the current user has permission to edit the post.
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;
			
			// Get the posted data and sanitize it for use as an HTML class.
			$new_head = ( isset( $_POST['heading_text'] ) ? $_POST['heading_text'] : '' );
			$new_head_color = ( isset( $_POST['heading_color'] ) ? $_POST['heading_color'] : '' );
			
			$new_text = ( isset( $_POST['text_text'] ) ? $_POST['text_text'] : '' );
			$new_text_color = ( isset( $_POST['text_color'] ) ? $_POST['text_color'] : '' );
			
			$bkgd_type  = ( isset( $_POST['background_type'] ) ? $_POST['background_type'] : '' );
			$bkgd_color = ( isset( $_POST['bkgd_color'] ) ? $_POST['bkgd_color'] : '' );
			$bkgd_image = ( isset( $_POST['bkgd_image'] ) ? $_POST['bkgd_image'] : '' );
			
			// Get the meta key.
			$meta_keys = array(
				"heading_text"  => sanitize_text_field ( $new_head ),
				"heading_color" => sanitize_text_field ( $new_head_color ),
				
				"text_text"  => sanitize_text_field ( $new_text ),
				"text_color" => sanitize_text_field ( $new_text_color ),
				
				"bkgd_type"  => sanitize_text_field ( $bkgd_type ),
				"bkgd_color" => sanitize_text_field ( $bkgd_color ),
				"bkgd_image" => sanitize_text_field ( $bkgd_image )
			);
			
			foreach ($meta_keys as $meta_key => $new_meta_value) {
				
				// Get the meta value of the custom field key.
				$meta_value = get_post_meta( $post_id, $meta_key, true );
				
				// If a new meta value was added and there was no previous value, add it.
				if ( $new_meta_value && '' == $meta_value )
					add_post_meta( $post_id, $meta_key, $new_meta_value, true );
					
					// If the new meta value does not match the old value, update it.
					elseif ( $new_meta_value && $new_meta_value != $meta_value )
					update_post_meta( $post_id, $meta_key, $new_meta_value );
					
					// If there is no new meta value but an old value exists, delete it.
					elseif ( '' == $new_meta_value && $meta_value )
					delete_post_meta( $post_id, $meta_key, $meta_value );
			}
}

// add custom post type to home page selection dropdown
function zenpbt_add_pages_to_dropdown( $pages, $r ) {
	if ( isset( $r['name'] ) && 'page_on_front' == $r['name'] ) {
		$args = array(
				'post_type' => 'zen_page'
		);
		$stacks = get_posts($args);
		$pages = array_merge($pages, $stacks);
	}
	
	return $pages;
}
add_filter( 'get_pages', 'zenpbt_add_pages_to_dropdown', 10, 2 );


function zenpbt_enable_front_page_zen_pages( $query ) {
	if ( !isset( $wp_customize ) ) {
		//if ( ( isset ( $query->query_vars['post_type'] ) && '' == $query->query_vars['post_type'] ) && 0 != $query->query_vars['page_id'] ) {
		if ( empty ( $query->query_vars['post_type'] ) && 0 != $query->query_vars['page_id'] ) {
			$query->query_vars['post_type'] = array( 'page', 'zen_page' );
		}
	}
}
add_action( 'pre_get_posts', 'zenpbt_enable_front_page_zen_pages' );

// get image ID
function zenpbt_get_attachment_id ( $url ) {
	$attachment_id = 0;
	$dir = wp_upload_dir();
	if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) {
		$file = basename( $url );
		$query_args = array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'fields'      => 'ids',
				'meta_query'  => array(
						array(
								'value'   => $file,
								'compare' => 'LIKE',
								'key'     => '_wp_attachment_metadata',
						),
				)
		);
		
		$query = new WP_Query( $query_args );
		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post_id ) {
				$meta = wp_get_attachment_metadata( $post_id );
				$original_file       = basename( $meta['file'] );
				$cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
				if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
					$attachment_id = $post_id;
					break;
				}
			}
		}
	}
	return $attachment_id;
}


/////////////////////////////// SIGN UP ////////////////////////////
add_action('wp_dashboard_setup', 'zenpbt_custom_dashboard_widgets');
function zenpbt_custom_dashboard_widgets() {
	global $wp_meta_boxes;
	wp_add_dashboard_widget('corporatezen_newsletter', 'CZ Newsletter', 'zenpbt_mailchimp_signup_widget');
}

function zenpbt_mailchimp_signup_widget() {
	$user    = wp_get_current_user();
	$email   = (string) $user->user_email;
	$fname   = (string) $user->user_firstname;
	$lname   = (string) $user->user_lastname;
	?>
	
<!-- Begin MailChimp Signup Form -->
<div id="mc_embed_signup">
	<form action="//corporatezen.us13.list-manage.com/subscribe/post?u=e9426a399ea81798a865c10a7&amp;id=9c1dcdaf0e" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
	    <div id="mc_embed_signup_scroll">
			<h2>Don't miss important updates!</h2>
			
			<p class="about-description">Don't worry, we hate spam too. We send a max of 2 emails a month, and we will never share your email for any reason. Sign up to ensure you don't miss any important updates or information about this plugin or theme. </p>
		
			<div class="mc-field-group">
				<!--<label for="mce-EMAIL">Email Address  <span class="asterisk">*</span></label>-->
				<input type="email" value="<?php echo $email; ?>" name="EMAIL" class="fat_wide required email" id="mce-EMAIL" style="width: 75%;">
				<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button button-primary">
			</div>
		
			<div class="mc-field-group">
				<input type="hidden" value="<?php echo $fname; ?>" name="FNAME" class="" id="mce-FNAME">
			</div>
			<div class="mc-field-group">
				<input type="hidden" value="<?php echo $lname; ?>" name="LNAME" class="" id="mce-LNAME">
			</div>
			
		
			<div id="mce-responses" class="clear">
				<div class="response" id="mce-error-response" style="display:none;color: red;font-weight: 500;margin-top: 20px; margin-bottom: 20px;"></div>
				<div class="response" id="mce-success-response" style="display:none;color: green;font-weight: 500;margin-top: 20px; margin-bottom: 20px;"></div>
			</div>    
			
			<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
		    <div style="position: absolute; left: -5000px;" aria-hidden="true">
		    	<input type="text" name="b_e9426a399ea81798a865c10a7_9c1dcdaf0e" tabindex="-1" value="">
		    </div>
	
	    </div>
	</form>
</div>

<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>
<script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
<!--End mc_embed_signup-->
	
	<?php
}

/////////////////////////////// AJAX ////////////////////////////
add_action( 'admin_enqueue_scripts', 'zenpbt_my_enqueue', 1 );
function zenpbt_my_enqueue($hook) {
	
	if( 'index.php' != $hook ) {
		// Only applies to dashboard panel
		return;
	}
	
	wp_register_script('ajax-script', plugins_url( '/js/custom-builder.js', __FILE__ ),  array('jquery') );
	
	wp_enqueue_script( 'ajax-script', plugins_url( '/js/custom-builder.js', __FILE__ ), array('jquery') );
	
	wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	
	// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
	//wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
}

// getImageID handler
add_action('wp_ajax_code_get_imageID', 'zenpbt_ajax_get_image');
function zenpbt_ajax_get_image() {
	
	if ( isset($_REQUEST["img_url"]) ) $img_url = $_REQUEST["img_url"]; else die("Error getting image URL");
	
	if ( empty ( $img_url ) ) {
		echo "Error getting image URL"; // failure
	} else {
		$imgID = zenpbt_get_attachment_id ( sanitize_text_field( $img_url ));
		echo $imgID;
	}
	
	wp_die();
}

// save/update post handler
add_action('wp_ajax_update_or_save_zen_page', 'zenpbt_save_zen_page');
function zenpbt_save_zen_page() {
	
	//print_r($_REQUEST);
	
	$pid = ( is_numeric($_REQUEST['pid']) ? $_REQUEST['pid'] : 0);
	//$bootstrap_content = str_replace($search, $replace, $subject);
	$insert = wp_insert_post( array (
			'ID'           => $pid,
			'post_title'   => sanitize_text_field ( $_REQUEST['post_title'] ),
			'post_content' => sanitize_text_field ( $_REQUEST['post_content'] ), //sanitize_post_field ('post_content', $_REQUEST['post_content'], $pid),
			'post_status'  => 'publish',
			'post_type'    => 'zen_page'
	));
	
	
	$update = wp_update_post( array (
			'ID'           => $pid,
			'post_title'   => sanitize_text_field ( $_REQUEST['post_title'] ),
			'post_content' => sanitize_post_field ('post_content', $_REQUEST['post_content'], $pid),
			'post_status'  => 'publish'
	));
	
	echo 'insert: ' . $insert . '<br><br>update: ' . $update;
	//echo sanitize_post_field ('post_content', $_REQUEST['post_content'], $pid);
	
	wp_die();
}


// build section handler
add_action('wp_ajax_build_zen_section', 'zenpbt_ajax_build_section');
function zenpbt_ajax_build_section() {
	
	//$is_update = ( !empty ( $_REQUEST['update_or_create'] ) ? true : false );
	$is_template_A  = ( !empty ( $_REQUEST['temp_A'] ) ? true : false );
	$is_template_B  = ( !empty ( $_REQUEST['temp_B'] ) ? true : false );
	
	if ( $is_template_A ) {
		
		$colors = get_option( 'czen_theme_colors' );
		$options = get_option( 'czen_theme_options' );
		
		$scheme = $options['color_scheme'];
		//$font = $options['font'];
		
		$sec_id = ( !empty($_REQUEST['sec_id']) && is_numeric($_REQUEST['sec_id']) ? $_REQUEST['sec_id'] : 0);
		
		/* Heading */
		$heading       = ( !empty($_REQUEST['head'] ) ? sanitize_text_field ( $_REQUEST['head'] ) : '');
		$heading_color = ( !empty($_REQUEST['color_h1'] ) && is_numeric($_REQUEST['color_h1']) ? $_REQUEST['color_h1'] : '');
		
		/* Text */
		$content = ( !empty($_REQUEST['content'] ) ? sanitize_text_field ( $_REQUEST['content'] ) : '');
		$p_color = ( !empty($_REQUEST['color_p'] ) && is_numeric($_REQUEST['color_p']) ? $_REQUEST['color_p'] : '');
		
		/* Background */
		$background_default = 'color';
		$background_type  = ( !empty ( $_REQUEST['background_type']) ? sanitize_text_field ( $_REQUEST['background_type'] ) : $background_default);
		$background_color = ( !empty ( $_REQUEST['color_bkg'] ) && is_numeric($_REQUEST['color_bkg']) ?  $_REQUEST['color_bkg'] : '');
		$bkg_imgID        = ( !empty ( $_REQUEST['bkg_imgID'] ) ? sanitize_text_field ( $_REQUEST['bkg_imgID'] ) : '');
		
		/* Alignment */
		$align_default = 'text-center';
		$align = ( !empty($_REQUEST['text_align'] ) ? 'text-' . sanitize_text_field ( $_REQUEST['text_align'] ) : $align_default);
		
		$bkg_html = '';
		if ( $background_type == 'image' ) {
			$img_url  = wp_get_attachment_url ( $bkg_imgID );
			$bkg_size = 'cover'; ///////////////// give this option to user
			$bkg_html = "background-image: url($img_url); background-size: $bkg_size;";
		} else if ( $background_type == 'color' ) {
			$bkg_html = '';
		}
		
		// post the color number, and theme name, and build the class name from the posted number
		$html = '<div id="' . $sec_id . '" class="zen_tool_main zen_tool_template_A col-lg-12 background_color_' . $background_color. '" style="' . $bkg_html . '">
				<div class="sec_id_div">ID: ' . $sec_id . '</div>
				<h1  class="dynamic_heading_1 font_color_' . $heading_color. ' zen_tool_h1 ' . $align . '">' . $heading . '</h1>
				<div class="dynamic_body font_color_' . $p_color. ' zen_tool_pdiv ' . $align . '"><p>' . $content . '</p></div>
				<input type="button" value="X" class="remove_this_section button"><input type="button" value="Edit" class="edit_this_section button"></div>';
		
		
		echo $html;
	} else if ( $is_template_B ) {
		
		$align_default = 'center';
		$align = ( !empty($_REQUEST['text_align'] ) ? sanitize_text_field ( $_REQUEST['text_align'] ) : $align_default);
		
		$colors = get_option( 'czen_theme_colors' );
		$options = get_option( 'czen_theme_options' );
		
		$scheme = $options['color_scheme'];
		//$font = $options['font'];
		
		$sec_id = ( !empty($_REQUEST['sec_id']) && is_numeric($_REQUEST['sec_id']) ? $_REQUEST['sec_id'] : 0);
		
		// Heading
		$heading       = ( !empty($_REQUEST['head'] ) ? sanitize_text_field ( $_REQUEST['head'] ) : '');
		$heading_color = ( !empty($_REQUEST['color_h1'] ) && is_numeric( $_REQUEST['color_h1'] ) ? $_REQUEST['color_h1'] : '');
		
		// Text
		$content = ( !empty($_REQUEST['content'] ) ? sanitize_text_field ( $_REQUEST['content'] ) : '');
		$p_color = ( !empty($_REQUEST['color_p'] ) && is_numeric( $_REQUEST['color_p'] ) ? $_REQUEST['color_p'] : '');
		
		// Background
		$background_color = ( !empty ( $_REQUEST['color_bkg'] ) && is_numeric($_REQUEST['color_bkg']) ? $_REQUEST['color_bkg'] : '');
		$bkg_imgID = ( !empty ( $_REQUEST['bkg_imgID'] ) ? sanitize_text_field ( $_REQUEST['bkg_imgID'] ) : '');
		$img_url  = wp_get_attachment_url ( $bkg_imgID );
		$bkg_size = 'cover'; ///////////////// give this option to user
		$bkg_html = "background-image: url($img_url); background-size: $bkg_size;";
		
		$html = '<div id="' . $sec_id . '" class="zen_tool_main zen_tool_template_B col-lg-12" style="' . $bkg_html . '">
				<div class="sec_id_div">ID: ' . $sec_id . '</div>
				<div class="tempB_text_div tempB_align_' . $align . ' background_color_' . $background_color. '">
					<h1  class="dynamic_heading_1 font_color_' . $heading_color. ' zen_tool_h1">' . $heading . '</h1>
					<div class="dynamic_body font_color_' . $p_color. ' zen_tool_pdiv"><p>' . $content . '</p></div>
					<div class="clear"></div>
				</div><div class="clear"></div><input type="button" value="X" class="remove_this_section button"><input type="button" value="Edit" class="edit_this_section button"></div>';
		
		echo $html;
	}
	
	wp_die();
}

?>