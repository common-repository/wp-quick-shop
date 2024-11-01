<?php




    if(!function_exists('register_wpqs_bootstrap_style')){

        function register_wpqs_bootstrap_style() {
		
			wp_enqueue_style( 'wpqs-common-style', plugins_url('css/common.css', dirname(__FILE__)), array(), true);
			
			wp_enqueue_script('wpqs-popper-scripts', plugins_url('js/popper.min.js', dirname(__FILE__)), array('jquery'));
			wp_enqueue_script('wpqs-bootstrap-scripts', plugins_url('js/bootstrap.min.js', dirname(__FILE__)), array('jquery'));
			
			wp_enqueue_style( 'wpqs-bootstrap-style', plugins_url('css/bootstrap.min.css', dirname(__FILE__)), array(), true);
		
		}

    }

    if(!function_exists('register_wpqs_default_style')){

        function register_wpqs_default_style() {
			
			
			wp_enqueue_style( 'wpqs-common-style', plugins_url('css/common.css', dirname(__FILE__)), array(), true);
			wp_enqueue_style( 'wpqs-front-style', plugins_url('css/front-style.css', dirname(__FILE__)), array(), true);
			wp_enqueue_style( 'wpqs-mobile-style', plugins_url('css/mobile.css', dirname(__FILE__)), array(), true);

		}

    }

    if(!function_exists('register_wpqs_custom_style')){

        function register_wpqs_custom_style() {

			$wpqs_styling_settings = get_option('wpqs_styling_settings', array());
		
			if(!empty($wpqs_styling_settings)){
		
				echo '<style>'.$wpqs_styling_settings['custom_style_text'].'</style>';
		
			}
		
		
		}

    }


    if(!function_exists('wpqs_check_shortcode')){

        function wpqs_check_shortcode(){

            global  $post, $wpqs_pro;
			$data_array = array();
			$short_code = 'WP-QUICKSHOP';
			$page_content = '';
			if(!empty($post)){
				
				$page_content = $post->post_content;
				
				
				
			}

            if(!is_admin() && $page_content && has_shortcode($page_content, $short_code)){


                $wpqs_styling_settings = get_option('wpqs_styling_settings', array());

                if(!empty($wpqs_styling_settings)){

                    $styling_types = isset($wpqs_styling_settings['styling_type']) ? $wpqs_styling_settings['styling_type'] : array();

                    if(!empty($styling_types)) {

                        if (in_array('custom', $styling_types)) {

                            register_wpqs_custom_style();
                            $data_array['style_types'][] = 'custom';

                        }

                        if (in_array('default', $styling_types)) {

                            register_wpqs_default_style();
                            $data_array['style_types'][] = 'default';


                        }

                        if (in_array('bootstrap', $styling_types) && $wpqs_pro) {

                            register_wpqs_bootstrap_style();
                            $data_array['style_types'][] = 'bootstrap';

                        }else{

                            register_wpqs_default_style();
                            $data_array['style_type'] = 'none';

                        }

                    }else{

                        register_wpqs_default_style();
                        $data_array['style_type'] = 'none';


                    }


                }else{

                        register_wpqs_default_style();
                        $data_array['style_type'] = 'none';


                }

                

            }
			
			wp_localize_script('wpqs-scripts', 'wpqs_style_obj', $data_array);

        }
    }

    add_action('init', 'wpqs_unsanitized_settings_data');
    if(!function_exists('wpqs_unsanitized_settings_data')){
        function wpqs_unsanitized_settings_data(){

            if(!empty($_REQUEST)){
                if (
                    ! isset( $_REQUEST['wpqs_admin_settings'] )
                    || ! wp_verify_nonce( $_POST['wpqs_admin_settings'], 'wpqs_admin' )
                ) {



                } else {

                    global $wpqs_pro;

                    if(isset($_REQUEST['wpqs_styling_settings']) && $wpqs_pro){

                        $wpqs_styling_settings = sanitize_wpqs_data($_REQUEST['wpqs_styling_settings']);
                        update_option('wpqs_styling_settings', $wpqs_styling_settings);

                    }

                }
            }

        }
    }

    if(!function_exists('wpqs_unset_custom_field_session')){
        function wpqs_unset_custom_field_session(){

            if(isset($_SESSION['wpqs_custom_field_values'])){
                unset($_SESSION['wpqs_custom_field_values']);
            }
        }
    }

    add_action('wp_ajax_wpqs_update_options', 'wpqs_update_options');

    if(!function_exists('wpqs_update_options')){

        function wpqs_update_options(){


            if (
                ! isset( $_POST['wpqs_update_options_field'] )
                || ! wp_verify_nonce( $_POST['wpqs_update_options_field'], 'wpqs_update_options_action' )
            ) {

                print_r(__('Sorry! Your nonce did not verified.', 'wp-quick'));

            } else {


                $wpqs_options = isset($_POST['wpqs_options']) ? sanitize_wpqs_data($_POST['wpqs_options']) : array();
				$wpqs_linked_user_role = isset($_POST['wpqs_linked_user_role']) ? sanitize_wpqs_data($_POST['wpqs_linked_user_role']) : array();
			
                update_option('wpqs_options', $wpqs_options);
				update_option('wpqs_linked_user_role', $wpqs_linked_user_role);
				
					
            }


            wp_die();

        }
    }





