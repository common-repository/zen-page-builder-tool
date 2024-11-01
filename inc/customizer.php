<?php

/*
 * This file describes the sections, settings, and controls the plugin adds to the wordpress customizer
 */

defined( 'ABSPATH' ) or die( 'Error: Direct access to this code is not allowed.' );

function zenpbt_sanitize_setting ( $value ) {
	return sanitize_text_field ( $value );
}

add_action ( 'customize_register', 'zenpbt_customize_register' );
function zenpbt_customize_register ( $wp_customize ) {

	// COLORS
	$wp_customize->add_section ( 'zenpbt_color_scheme', array (
			'title'          => 'Zen Page Builder: Color Scheme',
			'description'    => 'Select the colors to use for any content created with the Zen Page Builder Tool. It is recommended to use the colors your theme uses here. <br><br>These colors will determine what color options you have when using the Zen Page Builder Tool. Therefore, if you want to use a certain color in your Zen Page content, make sure to select it here.',
			'priority'       => 35,
	) );

	$wp_customize->add_setting ( 'zenpbt_options[color_scheme]', array (
			'default'        => 'custom',
			'type'           => 'option',
			'capability'     => 'edit_theme_options',
			'sanitize_callback' => 'zenpbt_sanitize_setting',
	) );

	$wp_customize->add_control ( 'zenpbt_color_scheme', array (
			'label'       => 'Color Scheme',
			'description' => '',
			'section'     => 'zenpbt_color_scheme',
			'settings'    => 'zenpbt_options[color_scheme]',
			'type'        => 'radio',
			'choices'     => array (
					'custom' => 'Custom (Use The Colors Below)',
			),
	) );	
	
	
	// main color ( site title, h1, h2, h4. h6, widget headings, nav links, footer headings )
	$txtcolors[] = array (
			'slug'=>'color_1',
			'default' => '#B10DC9',
			'label' => 'Color 1'
	);
	
	// secondary color ( site description, sidebar headings, h3, h5, nav links on hover )
	$txtcolors[] = array (
			'slug'=>'color_2',
			'default' => '#2ECC40',
			'label' => 'Color 2'
	);
	
	// 3rd
	$txtcolors[] = array (
			'slug'=>'color_3',
			'default' => '#0074D9',
			'label' => 'Color 3'
	);
	
	// 4th
	$txtcolors[] = array (
			'slug'=>'color_4',
			'default' => '#FF4136',
			'label' => 'Color 4'
	);
	
	// 5th
	$txtcolors[] = array (
			'slug'=>'color_5',
			'default' => '#FFDC00',
			'label' => 'Color 5'
	);
	
	// add the settings and controls for each color
	foreach( $txtcolors as $txtcolor ) {
		$wp_customize->add_setting ( "zenpbt_colors[" . $txtcolor['slug'] . "]", array (
						'default' => $txtcolor['default'],
						'type' => 'option',
						'sanitize_callback' => 'zenpbt_sanitize_setting',
						'capability' => 'edit_theme_options'
				)
		);

		$wp_customize->add_control (
				new WP_Customize_Color_Control (
						$wp_customize, $txtcolor['slug'],
						array (
								'label' => $txtcolor['label'],
								'section' => 'zenpbt_color_scheme',
								'settings' => "zenpbt_colors[" . $txtcolor['slug'] . "]" )
						)
		);
	}
}

function zenpbt_customize_colors() {

	$options = get_option( 'zenpbt_options' );
	$colors = get_option( 'zenpbt_colors' );

	$black = '#000000'; 
	$white = '#ffffff';
	
	$scheme = $options['color_scheme'];
	?>
		<style>
		:root {
			--primary-color:   <?php echo $colors['color_1']; ?> !important;
			--secondary-color: <?php echo $colors['color_2']; ?> !important;
			--tertiary-color:  <?php echo $colors['color_3']; ?> !important;
				
			--accent-color-1: <?php echo $colors['color_4']; ?> !important;
			--accent-color-2: <?php echo $colors['color_5']; ?> !important;
				
			--black-color: <?php echo $black; ?>;
			--white-color: <?php echo $white; ?>;
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
		</style>
	<?php
}
add_action( 'wp_head', 'zenpbt_customize_colors' );

?>