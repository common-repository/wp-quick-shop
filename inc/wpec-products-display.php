<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if(!in_array('wp-e-commerce', $wpqs_enabled)){
	return false;
}

$wpqse = wpqs_enabled();
if(empty($wpqse)){
	echo __('Shopping cart is not ready yet.', 'wp-quick');
	return false;
}

global $wpsc_cart;
$cart_url = '';//$woocommerce->cart->get_cart_url();
$items = $wpsc_cart->cart_items;
$shop_page_url = '';//get_permalink( wc_get_page_id( 'shop' ) );
//wpqs_pree($items);
$added_items = array();
if(!empty($items)){
	foreach($items as $item => $values) { 
		$added_items[$values->product_id] = $values->quantity;
	}
}
//wpqs_pree($added_items);

$args = array(
	'posts_per_page'   => -1,
	'offset'           => 0,
	'orderby'          => 'title',
	'order'            => 'ASC',
	'post_status'      => 'publish',
	'suppress_filters' => true
);

$args['post_type'] = 'wpsc-product';
//wpqs_pree($args);
if(function_exists('wpec_posts')){ $args = wpec_posts($args, $sc_args); }
//pree($args);exit;
$prdocuts_array = get_posts( $args );

//wpqs_pree($prdocuts_array);
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
				<td class="qs-center"><?php echo __('Image', 'wp-quick');?></td>		  
          		<td colspan="3" class="qs-title"><?php echo __('Item', 'wp-quick');?></td>
				<td class="qs-price"><?php echo __('Price', 'wp-quick');?></td>
                <td class="qs-qty"><?php echo __('Qty', 'wp-quick');?></td>
                <td class="qs-stock"><?php echo __('Stock', 'wp-quick');?></td>
                <td class="qs-buy"></td>        
		</tr>
      </thead>
      	    	    	    
	    
<?php	
	
	$minimum_order_quantity = 0;
	$maximum_order_quantity = 0;
	

	
	foreach($prdocuts_array as $products){
		
		//$meta = get_product_meta( $products->ID, 'product_metadata', true );
		$price = get_product_meta($products->ID, 'price', true);
		$stock = get_product_meta($products->ID, 'stock', true);
		$sku = get_product_meta($products->ID, 'sku', true);
		
		
		
		//wpqs_pree($meta);
		//exit;
		if($price<=0)
		continue;
		
			
		
		if(array_key_exists($products->ID, $added_items)){
			$minimum_order_quantity = $added_items[$products->ID];
		}		
		
		$stock = ($stock>0?$stock:__('Available'));
		
?>
<tbody>
          <tr id="product-<?php echo $products->ID; ?>">
		    		    <td data-title="<?php echo __('Image', 'wp-quick'); ?>" class="qs-center"><?php $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id($products->ID)); ?>
        <?php if($featured_image) { //pre($featured_image);?>
        <img src="<?php echo $featured_image[0]; ?>" data-id="<?php echo $products->ID; ?>" alt="<?php echo $products->post_title; ?>" />
        <?php } ?></td>
		    		    <td data-title="<?php echo __('Title', 'wp-quick');?>" class="qs-title" colspan="3">
			  			  <a href="<?php echo get_the_permalink($products->ID); ?>"><?php echo $products->post_title; ?></a>
		      			  		    </td>
		    		    		    <td class="qs-price" data-title="<?php echo __('Price', 'wp-quick');?>"><?php echo wpsc_get_currency_symbol().$price; ?></td>
			
		    <td data-title="<?php echo __('Qty', 'wp-quick');?>" class="qs-qty"><input size="2" pid="<?php echo $products->ID; ?>" name="prod[<?php echo $products->ID; ?>]" value="<?php echo $minimum_order_quantity; ?>" type="text" class="qty-box" /></td>
		    			<td data-title="<?php echo __('Stock', 'wp-quick');?>" class="qs-stock"><?php echo $stock; ?></td>
		    		    <td data-title="<?php echo __('Buy', 'wp-quick');?>" class="qs-buy">
                        

			<a id="product_<?php echo $products->ID; ?>_submit_button" rel="nofollow" data-quantity="<?php echo $minimum_order_quantity; ?>" data-quantity-min="<?php echo $minimum_order_quantity; ?>" data-quantity-max="<?php echo $maximum_order_quantity; ?>" data-product_id="<?php echo $products->ID; ?>" data-product_sku="<?php echo $sku; ?>" class="button add_to_cart_button wpsc_buy_button"><span><?php _e('Buy', 'wp-quick'); ?></span></a>
		  
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
	  <div style="text-align:right"><input type="submit" id="button-quickshop-cart-<?php echo date('Y'); ?>" class="button add_to_cart_button" value="<?php __('Add to Cart', 'wp-quick'); ?>" /></span></a></div>
	</div>
	  </div>
</div>
</form>
<?php		
}
?>