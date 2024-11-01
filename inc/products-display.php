<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$wpqse = wpqs_enabled();
if(empty($wpqse)){
	echo __('Shopping cart is not ready yet.', 'wp-quick');
	return false;
}

global $woocommerce;
$cart_url = wc_get_cart_url();
$items = $woocommerce->cart->get_cart();
$shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );

$added_items = array();
if(!empty($items)){
	foreach($items as $item => $values) { 
		$added_items[$values['product_id']] = $values['quantity'];
	}
}
$args = array(
	'posts_per_page'   => -1,
	'offset'           => 0,
	'orderby'          => 'title',
	'order'            => 'ASC',
	'post_type'        => 'product',
	'post_status'      => 'publish',
	'suppress_filters' => true,
	/*'meta_query'	   => array(
							'relation' => 'OR',
							array(
								'key' => '_regular_price',
								'value' => 0,
								'compare' => '!='
								),
							array(
								'key'     => '_regular_price',
								'compare' => 'NOT EXISTS',
								'value'   => '0',   
							),
						)*/
);
$prdocuts_array = get_posts( $args );
if(!empty($prdocuts_array)){
	
?>
<div class="wp-quick-instructions">
<?php echo wp_kses_post(stripslashes(wpqs_settings('instructions', true))); ?>
</div>
<form action="<?php echo $cart_url; ?>" method="post">
<?php wp_nonce_field( 'qs_bulk', 'qs_bulk_field' ); ?>
<div class="qs-box">
  <div class="box-content">
    <div class="box-product qs-content" id="quickshop-<?php echo date('Y'); ?>">
         <div id="no-more-tables">
      <table class="qs-table" style="width:100%;">
	  <thead>
        <tr>
				<td class="qs-center"><?php echo __('Image');?></td>		  
          		<td colspan="3" class="qs-title"><?php echo __('Item'); ?></td>
				<td class="qs-price"><?php echo __('Price'); ?></td>
                <td class="qs-qty"><?php echo __('Qty'); ?></td>
                <td class="qs-stock"><?php echo __('Stock'); ?></td>
                <td class="qs-buy"></td>        
		</tr>
      </thead>
      	    	    	    
	    
<?php	
	
	$minimum_order_quantity = 0;
	$maximum_order_quantity = 0;
	

	
	foreach($prdocuts_array as $products){
		
		$product = new WC_Product($products->ID);
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
		$stock = ($stock>0?$stock:__('Available'));
		
?>
<tbody>
          <tr id="product-<?php echo $products->ID; ?>">
		    		    <td data-title="Image" class="qs-center"><?php $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id($products->ID)); ?>
        <?php if($featured_image) { //pre($featured_image);?>
       <a href="<?php echo get_the_permalink($products->ID); ?>" title="<?php echo $products->post_title; ?>" target="_blank"><img src="<?php echo $featured_image[0]; ?>" data-id="<?php echo $products->ID; ?>" alt="<?php echo $products->post_title; ?>" /></a>
        <?php } ?></td>
		    		    <td data-title="<?php _e('Title'); ?>" class="qs-title" colspan="3">
			  			  <a href="<?php echo get_the_permalink($products->ID); ?>" title="<?php echo $products->post_title; ?>" target="_blank"><?php echo $products->post_title; ?></a>
		      			  		    </td>
		    		    		    <td data-title="Price" class="qs-price"><?php echo get_woocommerce_currency_symbol().$price; ?></td>
			
		    <td data-title="Qty" class="qs-qty"><input size="2" pid="<?php echo $products->ID; ?>" name="prod[<?php echo $products->ID; ?>]" value="<?php echo $minimum_order_quantity; ?>" type="text" class="qty-box" /></td>
		    			<td data-title="Stock" class="qs-stock"><?php echo $stock; ?></td>
		    		    <td data-title="Buy"  class="qs-buy">
			<a rel="nofollow" data-quantity="<?php echo $minimum_order_quantity; ?>" data-quantity-min="<?php echo $minimum_order_quantity; ?>" data-quantity-max="<?php echo $maximum_order_quantity; ?>" href="<?php echo $shop_page_url; ?>/?add-to-cart=<?php echo $products->ID; ?>" data-product_id="<?php echo $products->ID; ?>" data-product_sku="<?php echo $product->get_sku(); ?>" class="button product_type_simple add_to_cart_button ajax_add_to_cart"><span><?php echo __('Buy'); ?></span></a>
		  
		    </td>
		    		              </tr>
        </tbody>

<?php		
	}
?>

            </table>
     </div>
    </div>

    <div class="qs-success"></div>

        <div class="buttons">
	  <div style="text-align:right"><input type="submit" id="button-quickshop-cart-<?php echo date('Y'); ?>" class="button add_to_cart_button" value="<?php _e('Add to Cart', 'wp-quick'); ?>" /></span></a></div>
	</div>
	  </div>
</div>
</form>
<?php		
}
?>