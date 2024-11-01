<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
	Plugin Name: WP Quick Shop
	Plugin URI: http://androidbubble.com/blog/wordpress/plugins/wp-quick-shop
	Description: WP Quick Shop is a great plugin to order multiple products together without searching and spending time on pagination.
	Version: 1.3.2
	Author: Fahad Mahmood 
	Author URI: https://www.androidbubbles.com
	Text Domain: wp-quick
	Domain Path: /languages
	License: GPL2
	
	This WordPress Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version. This free software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this software. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/ 

	
        
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        

	global $wpqs_premium_link, $wpqs_dir, $wpqs_pro, $wpqs_data, $wpqs_url, $wpqs_options;
	$disabled_letters = array();
	$wpqs_dir = plugin_dir_path( __FILE__ );
	$wpqs_url = plugin_dir_url( __FILE__ );
	$wpqs_pro = file_exists($wpqs_dir.'pro/wpqs_extended.php');
	$wpqs_premium_link = 'https://shop.androidbubbles.com/product/wp-quick-shop-pro';//https://shop.androidbubble.com/products/wordpress-plugin?variant=36439508549787';//
	$wpqs_data = get_plugin_data(__FILE__);
    $wpqs_options = get_option('wpqs_options', array());
	if($wpqs_pro){					
		include($wpqs_dir.'pro/wpqs_extended.php');
	}	
	include('inc/functions.php');
        
	register_activation_hook(__FILE__, 'wpqs_start');

	//KBD END WILL REMOVE .DAT FILES	

	register_deactivation_hook(__FILE__, 'wpqs_end' );



	add_action( 'admin_enqueue_scripts', 'register_wpqs_scripts' );
	add_action( 'wp_enqueue_scripts', 'register_wpqs_scripts' );

    add_action('admin_enqueue_scripts', 'wpqs_check_shortcode', 99);
	add_action('wp_enqueue_scripts', 'wpqs_check_shortcode', 99);	
		
	if(is_admin()){

		add_action( 'admin_menu', 'wpqs_menu' );	
		add_action( 'wp_ajax_wpqs_tax_types', 'wpqs_tax_types_callback' );
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", 'wpqs_plugin_links' );	
		
		add_action( 'admin_enqueue_scripts', 'wpqs_admin_style', 99 );
		
		
	}else{
		

		
		
	}

	

	