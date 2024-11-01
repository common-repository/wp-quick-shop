<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	if(!function_exists('sanitize_wpqs_data')){
		function sanitize_wpqs_data( $input ) {
			if(is_array($input)){		
				$new_input = array();	
				foreach ( $input as $key => $val ) {
					$new_input[ $key ] = (is_array($val)?sanitize_wpqs_data($val):sanitize_text_field( $val ));
				}			
			}else{
				$new_input = sanitize_text_field($input);			
				if(stripos($new_input, '@') && is_email($new_input)){
					$new_input = sanitize_email($new_input);
				}
				if(stripos($new_input, 'http') || wp_http_validate_url($new_input)){
					$new_input = esc_url_raw($new_input);
				}			
			}	
			return $new_input;
		}	
	}		
	
	if(!function_exists('wpqs_array_search_values')){

		function wpqs_array_search_values( $m_needle, $a_haystack, $b_strict = false){
			return array_intersect_key( $a_haystack, array_flip( array_keys( $a_haystack, $m_needle, $b_strict)));
		}
		
	}

	if(!function_exists('wpqs_pre')){
		function wpqs_pre($data){
			if(isset($_GET['debug'])){
				wpqs_pree($data);
			}
		}	 
	} 
		
	if(!function_exists('wpqs_pree')){
	function wpqs_pree($data){
				echo '<pre>';
				print_r($data);
				echo '</pre>';	
		
		}	 
	}

	include ('functions-inner.php');

	add_action('wp_loaded', function(){
		add_shortcode('WP-QUICKSHOP', 'woo_shop_tablular');
	});
	


	function wpqs_settings($key, $front=false, $default=''){
		
		
		$settings = get_option('wpqs_settings', array());
		//pree($settings);
		$ret = $default;
		if(array_key_exists($key, $settings)){
			$ret = $settings[$key];
		}
		if($front){
			$ret = nl2br($ret);
		}
		return $ret;
	}
	function woo_shop_tablular($sc_args=array()){
		ob_start();
		
		$sc_args = is_array($sc_args)?$sc_args:array();
		//pree($sc_args);

		$sc_args['type'] = array_key_exists('type', $sc_args)?$sc_args['type']:'';
		$sc_args['cats'] = isset($sc_args['cats'])?$sc_args['cats']:'';
		
		$sc_args['acf'] = isset($sc_args['acf'])?$sc_args['acf']:'';
		
		$sc_args['meta_key'] = isset($sc_args['meta-key'])?$sc_args['meta-key']:'';
		
		$sc_args['sort-by'] = isset($sc_args['sort-by'])?$sc_args['sort-by']:'';
		
		$sc_args['order-by'] = isset($sc_args['order-by'])?$sc_args['order-by']:'';
		
		$sc_args['category-sort-by'] = isset($sc_args['category-sort-by'])?$sc_args['category-sort-by']:'';
		
		

		$sort_by = $sc_args['sort-by'];
		
		$order_by = strtolower($sc_args['order-by']);
		
		$category_sort_by = strtolower($sc_args['category-sort-by']);
		$category_sort_by_arr = ($category_sort_by?explode(',', $category_sort_by):'');
		
		$category_sort_by_arr = (is_array($category_sort_by_arr)?array_filter($category_sort_by_arr):array());
		
			
        $wpqs_enabled = wpqs_enabled();
		if(
			(
				(($sc_args['type']!='' && $sc_args['type']=='woocommerce') 
					&& 
					in_array('woocommerce', $wpqs_enabled))
					||
					$sc_args['type']==''
				)
				&&
				!is_admin()
					
			){
			
			$acf_fields = $meta_keys = $fields_positions = array();
			
			if($sc_args['acf']){
				$acf_fields = explode(',', $sc_args['acf']);	
			}
			
			//pree($sc_args);
			
			if($sc_args['meta_key']){
				$meta_keys = explode(',', $sc_args['meta_key']);	
			}
			
			if($sc_args['columns-position']){
				$fields_positions = explode(',', $sc_args['columns-position']);	
			}			
			//pree($meta_keys);	
			include('wc-products-display.php');
		}

		if(
			(
				(($sc_args['type']!='' && $sc_args['type']=='wp-e-commerce')
				&&
				in_array('wp-e-commerce', $wpqs_enabled))
				||
				$sc_args['type']=='')
			&&
				!is_admin()
			){
				
			include('wpec-products-display.php');
		}
		$out1 = ob_get_contents();		
		ob_end_clean();		
		return $out1;
	}
	
	
	add_action('wp_loaded', 'wpqs_init');
	
		
	function wpqs_woo_in_cart($product_id) {
		global $woocommerce;
	 
		foreach($woocommerce->cart->get_cart() as $key => $val ) {
			$_product = $val['data'];
	 		//pree($_product->get_id());exit;
			if($product_id == $_product->get_id() ) {
				return true;
			}
		}
	 
		return false;
	}	
	
	function wpqs_init(){
		if(!empty($_POST)){
			if ( 
				! isset( $_POST['qs_bulk_field'] ) 
				|| ! wp_verify_nonce( $_POST['qs_bulk_field'], 'qs_bulk' ) 
			) {
			
			   
			
			} else {
				
				if(!empty($_POST['prod'])){
					$prod = isset( $_POST['prod'] ) ? (array) $_POST['prod'] : array();
					$prod = array_map( 'esc_attr', $prod );
					$wpqs_enabled = wpqs_enabled();
					foreach($prod as $product_id=>$qty){
						if($qty>0){						
							
							if(in_array('woocommerce', $wpqs_enabled)){
								global $woocommerce;
								if(wpqs_woo_in_cart($product_id)){
									$cart = WC()->instance()->cart;
									$cart_id = $cart->generate_cart_id($product_id);
									//wpqs_pree($cart_id);exit;
 									$cart_item_id   = $cart->find_product_in_cart($cart_id);
  									$cart->set_quantity($cart_item_id, $qty);
								}else{
									$woocommerce->cart->add_to_cart($product_id, $qty);
								}
								
							}
							if(in_array('wp-e-commerce', $wpqs_enabled)){
								global $wpsc_cart;
								$parameters['quantity'] = (int) $qty;
								$wpsc_cart->set_item( $product_id, $parameters, true );								
								
							}
							
						}
					}
				}

				if(isset($_POST['var']) && !empty($_POST['var'])){



                    $wpqs_enabled = wpqs_enabled();
                    $wpqs_variable_array = isset($_POST['var']) ? sanitize_wpqs_data($_POST['var']) : array();





                    if(in_array('woocommerce', $wpqs_enabled)){

                        if(!empty($wpqs_variable_array)){

                            foreach ($wpqs_variable_array as $product_id => $single_variable){




                                $variation_id = isset($single_variable['variation_id']) ? $single_variable['variation_id'] : 0;
                                $quantity = isset($single_variable['quantity']) ? $single_variable['quantity'] : 0;
                                $variations = isset($single_variable['variations']) ? (array)$single_variable['variations'] : array();



                                if(!$product_id || $variation_id == 0 || $quantity == 0) continue;
                                WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations );

                            }

                        }

                    }

                }
			   // process form data
			   //pree($_POST);exit;
			}			
		}
	}
			
	function wpqs_plugin_links($links) { 
		global $wpqs_premium_link, $wpqs_pro;
		
		$settings_link = '<a href="options-general.php?page=wp_quickshop">'.__('Settings', 'wp-quick').'</a>';
		
		if($wpqs_pro){
			array_unshift($links, $settings_link); 
		}else{
			 
			$wpqs_premium_link = '<a href="'.esc_url($wpqs_premium_link).'" title="'.__('Go Premium', 'wp-quick').'" target=_blank>'.__('Go Premium', 'wp-quick').'</a>'; 
			array_unshift($links, $settings_link, $wpqs_premium_link); 
		
		}
		
		
		return $links; 
	}
	
	function wpqs_menu()
	{
		global $wpqs_pro;
		
		$title = 'WP Quick Shop'.($wpqs_pro?' Pro':'');
		
		add_options_page($title, $title, 'install_plugins', 'wp_quickshop', 'wp_quickshop');



	}

	function wp_quickshop(){ 



		if ( !current_user_can( 'install_plugins' ) )  {



			wp_die( __( 'You do not have sufficient permissions to access this page.', 'wp-quick' ) );



		}



		global $wpdb, $wpqs_group, $wpqs_pro;

		
		if(!empty($_REQUEST)){
			if ( 
				! isset( $_REQUEST['wpqs_admin_settings'] ) 
				|| ! wp_verify_nonce( $_POST['wpqs_admin_settings'], 'wpqs_admin' ) 
			) {
			
			   
			
			} else {
		
		
				if(isset($_REQUEST['wpqs_settings'])){

                    $wpqs_settings_previous = get_option( 'wpqs_settings', array());
                    $wpqs_settings = isset( $_POST['wpqs_settings'] ) ? (array) $_POST['wpqs_settings'] : array();
					//pree($wpqs_settings);exit;
					foreach($wpqs_settings as $k=>$v){

                        $wpqs_settings_previous[$k] = $wpqs_settings[$k];
                    }

                    update_option( 'wpqs_settings', sanitize_wpqs_data($wpqs_settings_previous) );

                }

				if(isset($_REQUEST['wpqs_styling_settings']) && !$wpqs_pro){

				    $wpqs_styling_settings = $_REQUEST['wpqs_styling_settings'];
                    update_option('wpqs_styling_settings', sanitize_wpqs_data($wpqs_styling_settings));

                }

				if(isset($_REQUEST['wpqs_custom_field'])){
				    $wpqs_custom_field = $_REQUEST['wpqs_custom_field'];
				    update_option('wpqs_custom_field', sanitize_wpqs_data($wpqs_custom_field));
                }
			}
		}
				
		include('wpqs_settings.php');	

		

	}	

	
	if(!function_exists('wpqs_start')){
	function wpqs_start(){	


		}	
	}

	if(!function_exists('wpqs_end')){
	function wpqs_end(){	


		}
	}	

	
	
	function register_wpqs_scripts() {

		wp_enqueue_script(
			'wpqs-scripts',
			plugins_url('js/common.js', dirname(__FILE__)),
			array('jquery'),
			date('Ymdhi')
		);

        wp_enqueue_script( 'underscore' );

        $wpqs_data = array(

            'this_url' => admin_url( 'options-general.php?page=wp_quickshop' ),
            'nonce' => wp_create_nonce('wpqs_update_options_action'),
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'cart_url' => wc_get_cart_url(),
            'qs_ajax_nonce' => wp_create_nonce('qs_ajax_nonce_action'),
         );

        wp_localize_script( "wpqs-scripts", "wpqs_obj", $wpqs_data );


	}
	
		
	function wpqs_admin_style() {

	    $wpqs_bootstrap_allowed_pages = array(
	        'wp_quickshop'
        );

	    if(isset($_GET['page']) && in_array($_GET['page'], $wpqs_bootstrap_allowed_pages)){


            wp_enqueue_script('wpqs-popper-scripts', plugins_url('js/popper.min.js', dirname(__FILE__)), array('jquery'));
            wp_enqueue_script('wpqs-bootstrap-scripts', plugins_url('js/bootstrap.min.js', dirname(__FILE__)), array('jquery'));

            wp_register_style('wpqs-bootstrap-style', plugins_url('css/bootstrap.min.css', dirname(__FILE__)));
            wp_enqueue_style( 'wpqs-bootstrap-style' );



        }

		wp_register_style('wpqs-admin', plugins_url('css/admin-style.css', dirname(__FILE__)), array(), date('Ymdh'));
		wp_enqueue_style( 'wpqs-admin' );
		
	}
			
			
	function wpqs_enabled(){
		
		$status = array();
		
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		
		if(in_array( 'woocommerce/woocommerce.php',  $active_plugins)){
			$status[] = 'woocommerce';
		}
		if(in_array( 'wp-e-commerce/wp-shopping-cart.php', $active_plugins )){
			$status[] = 'wp-e-commerce';
		}
		if(in_array( 'woocommerce-discounts-plus/index.php', $active_plugins )){
			$status[] = 'woocommerce-discounts-plus';
		}		
		
		if(
				in_array( 'advanced-custom-fields/acf.php', $active_plugins )
			||
				in_array('advanced-custom-fields-pro/acf.php', $active_plugins)
		){
			$status[] = 'acf';
		}
		
		

		return $status;
	}

    add_action('init', function(){

        if(!empty($_POST)){
//            pree($_POST);exit;
        }
    });


	add_action('wp_ajax_nopriv_wpqs_add_variation_to_cart', 'wpqs_add_variation_to_cart');
	add_action('wp_ajax_wpqs_add_variation_to_cart', 'wpqs_add_variation_to_cart');

    function wpqs_add_variation_to_cart() {
//

        if(!empty($_POST)){
            if (
                ! isset( $_POST['qs_ajax_nonce_field'] )
                || ! wp_verify_nonce( $_POST['qs_ajax_nonce_field'], 'qs_ajax_nonce_action' )
            ) {

                echo 'false';

            } else {


                $product_id      = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
                $quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
                $variation_id      = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
                $variations         = ! empty( $_POST['variation'] ) ? (array) $_POST['variation'] : array();

                if(!$product_id || $variation_id == 0 || $quantity == 0){ echo "false"; wp_die();}


                if (WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {

                    echo 'true';

                } else {

                    echo 'false';

                }

            }
        }



        die();
    }