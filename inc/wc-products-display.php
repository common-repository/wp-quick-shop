<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    global $woocommerce, $wpqs_pro;

	//pree($_SESSION);


	$wpqs_custom_field = get_option('wpqs_custom_field', array());
	
    $is_custom_field = array_key_exists('active', $wpqs_custom_field);
	
    if(!$is_custom_field || !$wpqs_pro){

        wpqs_unset_custom_field_session();
    }

    if(!in_array('woocommerce', $wpqs_enabled)){
		echo __('WooCommerce is not ready yet.', 'wp-quick');
        return false;
    }
    $wpqse = wpqs_enabled();
    if(empty($wpqse)){
        echo __('Shopping cart is not ready yet.', 'wp-quick');
        return false;
    }
	
	if(!isset($woocommerce->cart)){ return false; }
	//pree($woocommerce);exit;
    $cart_url = wc_get_cart_url();
	
    $items = $woocommerce->cart->get_cart();
    $shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );

    $added_items = array();
    if(!empty($items)){
        foreach($items as $item => $values) {
            $added_items[$values['product_id']] = $values['quantity'];
        }
    }
	//pree($added_items);
    $args = array(
        'posts_per_page'   => -1,
        'offset'           => 0,
        'orderby'          => 'title',
        'order'            => 'ASC',
        'post_type'        => 'product',
        'post_status'      => 'publish',
        'suppress_filters' => true,

    );
	
	//pree($meta_keys);
	if(!empty($meta_keys)){
		$args['meta_query'] = array();
		
		foreach($meta_keys as $meta_data){
			
			$meta_data = explode('|', $meta_data);
			
			$meta_key = $meta_data[0];
			$meta_value = (count($meta_data)==2?$meta_data[1]:'');
			
			if($meta_value){
				$_meta_params = array(
											'key'   => $meta_key,
											'compare' => '=',
											'value' => $meta_value,
									);
			}else{
				$_meta_params = array(
											'key'   => $meta_key,
											'compare' => 'EXISTS',
									);
			}
									
			$args['meta_query'][] = $_meta_params;
			
			
						
						
		}
		
		
		$args['post_status'] = array('any');
	}
	

    if(function_exists('wpqs_posts')){ $args = wpqs_posts($args, $sc_args); }
    //pree($args);exit;
    $prdocuts_array = get_posts( $args );
	
    if(!empty($prdocuts_array)){
		//pree($fields_positions);
		
		$acf_fields_set = array();
		if(!empty($acf_fields)){ foreach($acf_fields as $acf_field){ $acf_field_arr = explode('|', $acf_field); if(count($acf_field_arr)==2){
			$acf_fields_set[current($acf_field_arr)] = end($acf_field_arr);
		}}}
		
		$columns_position = array(
		
									'item_image' => 1,
									'item_title' => 2,
									'acf_fields' => 3,
									'item_price' => 4,
									'item_qty' => 5,
									'item_stock' => 6,
									'custom_field' => 7,
									'buy_btn' => 8,
							);
		//pree($columns_position);
		if(is_array($fields_positions) && !empty($fields_positions)){
			foreach($fields_positions as $field_position => $field_key){ $field_position++;
				//pre($field_key);
				if(array_key_exists($field_key, $columns_position) || array_key_exists($field_key, $acf_fields_set)){
					$columns_position[$field_key] = $field_position;
				}
			}
		}
		//pre($columns_position);
							
		
		
		$re_arranged_columns = array();
    	
		//pre($columns_position);
		
		if(!empty($columns_position)){ for($col=1; $col<=count($columns_position); $col++){ 

        	$fields = wpqs_array_search_values($col, $columns_position, true);
			
			//pre($fields);
			
				foreach($fields as $field_key=>$position){
					
					$re_arranged_columns[] = $field_key;
					
				}
			} 
		} 
	
	//pre($sort_by);pre($order_by);		
	//pre($category_sort_by_arr);
?>
                
    <div class="wp-quick-instructions">
    <?php echo wp_kses_post(stripslashes(wpqs_settings('instructions', true))); ?>
    </div>
    <form action="<?php echo $cart_url; ?>" method="post" style="max-width: 100%">
    <?php wp_nonce_field( 'qs_bulk', 'qs_bulk_field' ); ?>
    <div class="qs-box">



        <div class="box-content">


          <div class="box-product qs-content" id="quickshop-<?php echo date('Y'); ?>">

            <div id="no-more-tables">

                <?php do_action('wpqs_before_qs_table') ?>

          <table class="qs-table table w-100" style="width:100%;">
          <thead class="bg-dark text-white">
            <tr>
            
            <?php 
				
				//pre($acf_fields_set);
				//pre($re_arranged_columns);
				
				foreach($re_arranged_columns as $re_arranged_column){
					
					//pre($re_arranged_column);
					
					switch($re_arranged_column){
						
						case 'item_image':

			?>
							<td class="qs-center text-center"><?php echo __('Image', 'wp-quick'); ?></td>
                    
            <?php 
						break;
						
						case 'item_title':						
			?>
							<td colspan="3" class="qs-title"><?php echo __('Item', 'wp-quick'); ?></td>
			<?php
						break;	
						
						case 'acf_fields':	        
			?>       
                  
                    <?php if(!empty($acf_fields)){ foreach($acf_fields as $acf_field){ $acf_field_arr = explode('|', $acf_field); if(count($acf_field_arr)==2){ 
							
							$acf_field_key = current($acf_field_arr);
							
							if(!array_key_exists($acf_field_key, $columns_position)){
					?>
							<td class="qs-<?php echo $acf_field_key; ?>"><?php echo end($acf_field_arr); ?></td>
                            
                    <?php } } } } ?>
			<?php
			  			 break;	    
						 
						 case 'item_price':
			?>		 
						 
							<td class="qs-price"><?php echo __('Price', 'wp-quick'); ?></td>
                    
 			<?php
			  			 break;	    
						 
						 case 'item_qty':						 
			?>
							<td class="qs-qty"><?php echo __('Qty', 'wp-quick'); ?></td>            						    
			<?php
			  			 break;	    
						 
						 case 'item_stock':
			?>								                
                    
							<td class="qs-stock"><?php echo __('Stock', 'wp-quick'); ?></td>
 			<?php
			  			 break;	    
						 
						 case 'custom_field':
			?>	                   
                    
                    <?php if($is_custom_field && $wpqs_pro) {?>
							<td><?php echo __($wpqs_custom_field['label'], 'wp-quick'); ?></td>
                    <?php } ?>
  			<?php
			  			 break;	    
						 
						 case 'buy_btn':
			?>	                    
                    
							<td class="qs-buy"></td>
  			<?php
			  			 break;	 
						 
						 default:
						 	
							if(array_key_exists($re_arranged_column, $acf_fields_set)){
			?>								
							<td class="qs-<?php echo $re_arranged_column; ?>"><?php echo $acf_fields_set[$re_arranged_column]; ?></td>
			
            <?php
							}
							
						 break;
						 
					}
					
				}
			?>					
            </tr>
          </thead>
              <tbody>



              <?php



        $terms_products = array();
		$terms_arr = array();
		//pre($prdocuts_array);
		
		//pree($added_items);
		
        foreach($prdocuts_array as $products){
			
			$minimum_order_quantity = 0;
        	$maximum_order_quantity = 0;
			
			if($wpqs_pro){
				$wpqs_users_list = get_post_meta($products->ID, 'wpqs-users-list', true);
				if(is_array($wpqs_users_list) && !empty($wpqs_users_list) && !in_array(get_current_user_id(), $wpqs_users_list)){
					continue;
				}
				
			}

//            $product = new WC_Product($products->ID);
            $product = new WC_Product_Variable($products->ID);

            $price = (float)$product->get_price();

            if($price<=0)
            continue;

            if(function_exists('wpbo_get_applied_rule_obj')){
                $rule = wpbo_get_applied_rule_obj($product);

                if(function_exists('wpbo_get_value_from_rule')){

                    $minimum_order_quantity = wpbo_get_value_from_rule('min', $product, $rule);
                    $minimum_order_quantity = ($minimum_order_quantity>0?$minimum_order_quantity:0);

                }

            }
			
			//pree($products->ID);
			
            if(array_key_exists($products->ID, $added_items)){
                $minimum_order_quantity = $added_items[$products->ID];
            }
			

            if(function_exists('wpbo_get_applied_rule_obj')){
                if(function_exists('wpbo_get_value_from_rule')){


                    $maximum_order_quantity = wpbo_get_value_from_rule('max', $product, $rule);
                    $maximum_order_quantity = ($maximum_order_quantity>0?$maximum_order_quantity:0);

                }
                //pre($minimum_order_quantity);
            }
            //pre($product);
            $stock = $product->get_stock_quantity();
            $stock = ($stock>0?$stock:__('Available', 'wp-quick'));
			
			
			$terms = get_the_terms ( $products->ID, 'product_cat' );
			
			
			//wpqs_pre($terms);
			
			if(!empty($terms)){
				foreach($terms as $term){
					$terms_arr[$term->term_id] = array('name'=>$term->name, 'slug'=>$term->slug);
					
					
					
					$terms_products[$term->term_id][$products->ID] = array(
						'product_obj'=>$product,
						'products_obj'=>$products,
						'price'=>$price,
						'minimum_order_quantity'=>$minimum_order_quantity,
						'maximum_order_quantity'=>$maximum_order_quantity
						
					);
				}
			}
			
		}
		//wpqs_pre($terms_products);
		//pre($terms_arr);
		
		$sort_attrib = '';
		if(!empty($terms_products)){
			$total_cats = array_keys($terms_products);
			foreach($terms_products as $category_id=>$terms_product_iter){
				
				if(count($total_cats)>1){
				
?>
				  <tr class="wpqs-category-title" data-category="<?php echo $category_slug = $terms_arr[$category_id]['slug']; ?>">
				  <td colspan="<?php echo count($acf_fields)+8; ?>"><?php echo $terms_arr[$category_id]['name']; ?></td>
				  </tr>
				  <?php
				  
				}
				foreach($terms_product_iter as $product_id=>$terms_product_arr){
				//wpqs_pree($terms_product_arr);
				$products = $terms_product_arr['products_obj'];
				$product = $terms_product_arr['product_obj'];
				//$product = new WC_Product_Variable($products->ID);
				
				$price = $terms_product_arr['price'];
				$minimum_order_quantity = $terms_product_arr['minimum_order_quantity'];
				$maximum_order_quantity = $terms_product_arr['maximum_order_quantity'];
				
				

    ?>
              <tr id="product-<?php echo $products->ID; ?>" class="<?php echo $product->has_child() ? 'qs_var_product': '';?>" data-product="<?php echo $products->ID; ?>" data-category="<?php echo $category_slug; ?>">



          <?php 
			
				foreach($re_arranged_columns as $re_arranged_column){
					
					switch($re_arranged_column){
						
						case 'item_image':

			?>
						<td data-title="Image" data-key="<?php echo $re_arranged_column; ?>" class="qs-center text-center">

                        <?php $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id($products->ID)); ?>
                        <?php if($featured_image) { //pre($featured_image);?>
                        <a href="<?php echo get_the_permalink($products->ID); ?>" title="<?php echo $products->post_title; ?>" target="_blank">
                        <img class="wp-quick-img-thumbnail" src="<?php echo $featured_image[0]; ?>" data-id="<?php echo $products->ID; ?>" alt="<?php echo $products->post_title; ?>" />
                        </a>
                        <?php } ?>

						</td>
                    
            <?php 
						break;
						
						case 'item_title':						
			?>
						<td data-title="Title" data-key="<?php echo $re_arranged_column; ?>" class="qs-title" colspan="3">
                      
						  <?php if(!$product->has_child()): ?>
                          <a href="<?php echo get_the_permalink($products->ID); ?>" title="<?php echo $products->post_title; ?>" class="qs-linked-title" target="_blank">
                          <?php endif; ?>
                          
                          <?php echo $products->post_title.($product->has_child() ? ' '.__('(Options)', 'wp-quick') : ''); ?>
                          
                          <?php if(!$product->has_child()): ?>
                          </a>
                          <?php endif; ?>
                          
                           <?php if(!$product->has_child()): ?>
                          
                          <span class="qs-plain-title"><?php echo $products->post_title.($product->has_child() ? ' '.__('(Options)', 'wp-quick') : ''); ?></span>
                          
                           <?php endif; ?>
                          
                        </td>
			<?php
						break;	
						
						case 'acf_fields':	        
			?>       
                  
                   <?php if(!empty($acf_fields)){ foreach($acf_fields as $acf_field){ $acf_field_arr = explode('|', $acf_field); if(count($acf_field_arr)==2){ 
				   
				   			$acf_key = current($acf_field_arr);
							
							if(!array_key_exists($acf_key, $columns_position)){
				   ?>
                    <td data-key="<?php echo $re_arranged_column; ?>" class="qs-<?php echo $acf_key; ?>"><?php echo get_field($acf_key, $products->ID); ?></td>
                    <?php } } } } ?>
			<?php
			  			 break;	    
						 
						 case 'item_price':
			?>		 
						 
							<td data-title="Price" data-key="<?php echo $re_arranged_column; ?>" class="qs-price"><?php echo get_woocommerce_currency_symbol().$price; ?></td>
                    
 			<?php
			  			 break;	    
						 
						 case 'item_qty':						 
			?>
							 <?php

                        $quantity_name = ($product->has_child() ? "var[{$products->ID}][quantity]" : "prod[{$products->ID}]");

                    ?>

							<td data-title="Qty" data-key="<?php echo $re_arranged_column; ?>" class="qs-qty"><input size="2" pid="<?php echo $products->ID; ?>" name="<?php echo $quantity_name; ?>" value="<?php echo $minimum_order_quantity?$minimum_order_quantity:''; ?>" type="text" class="qty-box" /></td>					    
			<?php
			  			 break;	    
						 
						 case 'item_stock':
			?>								                
                    
							<td data-title="Stock" data-key="<?php echo $re_arranged_column; ?>" class="qs-stock"><?php echo $stock; ?></td>
 			<?php
			  			 break;	    
						 
						 case 'custom_field':
			?>	                   
                    
							 <?php if($is_custom_field && $wpqs_pro){ ?>
        
                            <td data-title="<?php echo $wpqs_custom_field['label'] ?>" data-key="<?php echo $re_arranged_column; ?>">
        
                                
        
                                 <?php $custom_field = wpqs_custom_field_html($products->ID); echo $custom_field['field']; ?>
        
        
        
                            </td>
        
                           <?php }?>
  			<?php
			  			 break;	    
						 
						 case 'buy_btn':
			?>	                    
                    
                            <td data-title="Buy" data-key="<?php echo $re_arranged_column; ?>" style="text-align:center;" class="qs-buy">
                                <a rel="nofollow" data-quantity="<?php echo $minimum_order_quantity; ?>" data-quantity-min="<?php echo $minimum_order_quantity; ?>" data-quantity-max="<?php echo $maximum_order_quantity; ?>" href="<?php echo $shop_page_url; ?>/?add-to-cart=<?php echo $products->ID; ?>" data-product_id="<?php echo $products->ID; ?>" data-product_sku="<?php echo $product->get_sku(); ?>" class="<?php echo ($product->has_child() ? 'disabled' : '') ?> button btn btn-sm small btn-primary px-3 pt-0 pb-1 product_type_simple add_to_cart_button ajax_add_to_cart"><span><?php _e('Buy', 'wp-quick'); ?></span></a>
                            </td>
  			<?php
			  			 break;	 
						 
						 default:
						 
						 	if(array_key_exists($re_arranged_column, $acf_fields_set)){
						 
			?>
            				<td data-key="<?php echo $re_arranged_column; ?>" class="qs-<?php echo $re_arranged_column; ?>"><?php echo get_field($re_arranged_column, $products->ID); ?></td>
                            
			
            <?php		
			
						}			 
						 
						 break;
						 
					}
					
				}
			?>		
                    

                    
                    
                    
                    

                    

                   

                    
                 

                    

                  <?php

                      if($product->has_child()){

                          echo '<input type="hidden" name="var['.$products->ID.'][variation_id]" class="variation_id" value="0">';
                          echo '<input type="hidden" name="var['.$products->ID.'][product_id]" class="product_id" value="'.$products->ID.'">';


                      }

                  ?>

              </tr>

    <?php

            if($product->has_child()){



                $get_attributes = $product->get_attributes( 'edit' );


                $attributes = array();


                foreach ( $get_attributes as $attribute ) {


                  if ( $attribute->is_taxonomy() ) {

                        $terms = $attribute->get_terms();
                        if(!empty($terms)){
                            foreach ($terms as $term){

                                $attributes[$attribute->get_name()][] = $term->slug;
                            }
                        }


                    }else{

                        $attributes[$attribute->get_name()] = $attribute->get_options();
                    }

                }



                $attribute_keys  = array_keys( $attributes );
                $variations_json = wp_json_encode( $product->get_available_variations() );
                $variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );



                ?>


                    <tr id="var-product-<?php echo $products->ID; ?>" data-variation="<?php echo $variations_attr;  ?>" class="var_row_<?php echo $products->ID; ?> qs_var_product" data-product="<?php echo $products->ID; ?>" style="display: none">

                        <td colspan="<?php echo $is_custom_field ? 10 : 9; ?>">
                            <table class="variations" cellspacing="0">
                                <tbody>
                                <?php foreach ( $attributes as $attribute_name => $options ) :


                                    ?>
                                    <tr>
                                        <td class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label></td>
                                        <td class="value">
                                            <?php
                                            wc_dropdown_variation_attribute_options(
                                                array(
                                                    'options'   => $options,
                                                    'attribute' => $attribute_name,
                                                    'product'   => $product,
                                                    'class' => 'qs_variation_select',
                                                    'name' => "var[{$products->ID}][variations][attribute_".strtolower($attribute_name)."]",
                                                )
                                            );


//                                            echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>

                    </tr>


                <?php



            }
				}

			}
        }
    ?>
                </tbody>

          </table>
         </div>
        </div>

        <div class="qs-success"></div>

            <div class="buttons">
          <div style="text-align:right">
              <input type="submit" id="button-quickshop-cart-<?php echo date('Y'); ?>" class="button btn btn-sm small btn-primary add_to_cart_button" value="<?php echo __('Add to Cart', 'wp-quick'); ?>" />
              <a class="btn btn-primary text-white small bootstrap_add_to_cart" style="display: none"><?php echo __('Add to Cart', 'wp-quick'); ?></a>
          </div>
        </div>
          </div>
    </div>
<script type="text/javascript" language="javascript">
	jQuery(document).ready(function($){
		
		function sortTable(table, sort_by, order) {
			//console.log(sort_by+' - '+order);
			var asc   = order === 'asc',
				tbody = table.find('tbody');
		
			tbody.find('tr').sort(function(a, b) {
				if (asc) {
					return +$(a).data(sort_by) - +$(b).data(sort_by);
				} else {
					return +$(b).data(sort_by) - +$(a).data(sort_by);
				}
			}).appendTo(tbody);
		}


		var sort_by = '<?php echo $sort_by; ?>';
		var order_by = '<?php echo $order_by; ?>';
		var category_sort_by_arr = '<?php echo implode(",", $category_sort_by_arr); ?>';
		var category_sort_by = category_sort_by_arr.split(',');
		
		//console.log(category_sort_by);
		
		order_by = (order_by=='asc'?order_by:'desc');
		
		if(sort_by){
			var elem_wrapper = 'table.qs-table';
			var elem_str = elem_wrapper+' tbody tr td[data-key="'+sort_by+'"]';
			
			if($(elem_wrapper).length>0){
				
				if($(elem_str).length>0){
					
					//console.log($(elem_str));
					
					$.each($(elem_str), function(i,v){
						var html_str = parseInt($.trim($(this).html()));
						$(this).parent().attr('data-sort', html_str);
					});
					
					sortTable($(elem_wrapper), 'sort', order_by);
					
					$.each($('tr.wpqs-category-title'), function(i,v){
						$(this).insertAfter($(elem_wrapper+' tbody').find('tr').last());
					});
					
					$.each($('tr.wpqs-category-title'), function(i,v){
						
						var cat_title = $(this).data('category');
						
						var first_cat_product = 'tr[data-category="'+cat_title+'"]';
						
						if($(first_cat_product).length>0){
							$(this).insertBefore($(first_cat_product).eq(0));
						}
						
					});
					category_sort_by.reverse();
					//console.log(category_sort_by);
					$.each(category_sort_by, function(i,v){
						$(elem_wrapper+' tbody').prepend($('tr[data-category="'+v+'"]'));
					});
					
					sortTable($(elem_wrapper), 'sort', order_by);
				}
				
				
				
			}
		}
		
	});
</script>    
    </form>
    <?php
    }
    ?>


