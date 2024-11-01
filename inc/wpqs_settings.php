<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


global $wpqs_premium_link, $wpqs_data, $wpqs_pro, $wpdb, $wpqs_url, $wp_roles;

$instructions = wpqs_settings('instructions');
$admin_url = get_admin_url();
$tab_index = (int)(isset($_GET['t']) ? esc_attr($_GET['t']) : 0);


$wpqs_custom_field = get_option('wpqs_custom_field', array());
$wpqs_styling_settings = get_option('wpqs_styling_settings', array());
$wpqs_options = get_option('wpqs_options', array());

//pree($wpqs_custom_field);exit;

$wpqs_custom_field_types = array(

    'textarea' => 'Textarea',
    'text' => 'Text',
    'number' => 'Number',
);

$premium_alert = $wpqs_pro?'':'<div class="alert alert-warning text-right" role="alert">'.__("PREMIUM FEATURE", "wp-quick").'</div>';

$wpqs_enabled = wpqs_enabled();

?>

<div class="wrap wpqs_settings_div">
        <div class="icon32" id="icon-options-general"><br></div><h2><?php echo $wpqs_data['Name'].' ('.$wpqs_data['Version'].($wpqs_pro?') Pro':')'); ?> - <?php echo __('Settings', 'wp-quick'); ?></h2>


<?php if(!$wpqs_pro): ?>
<a title="<?php _e('Click here to download pro version', 'wp-quick'); ?>" style="background-color: #25bcf0;    color: #fff !important;    padding: 2px 30px;    cursor: pointer;    text-decoration: none;    font-weight: bold;    right: 0;    position: absolute;    top: 0;    box-shadow: 1px 1px #ddd;" href="https://shop.androidbubbles.com/download/" target="_blank"><?php _e('Already a Pro Member?', 'wp-quick'); ?></a>
<?php endif; ?>

    <?php
   
    ?>

    <div class="nav-tab-wrapper">

        <a class="nav-tab nav-tab-active"><?php _e("General Settings", "wp-ims"); ?></a>
        <a class="nav-tab"><?php _e("Shortcodes", "wp-ims"); ?></a>
        <a class="nav-tab float-right"><?php _e("Custom Field", "wp-ims"); ?></a>
        <a class="nav-tab"><?php _e("Styling", "wp-ims"); ?></a>
        <a class="nav-tab" style="display:none"><?php _e("Compatibility", "wp-ims"); ?></a>


    </div>

    <div class="wpqs_icon">
    	<a href="<?php echo esc_url($wpqs_premium_link); ?>" target="_blank" class="qs-premium" title="<?php echo __('Go Premium', 'wp-quick'); ?>'">
        <img src="<?php echo $wpqs_url.'images/icon.gif' ?>">
        </a>
    </div>

    <!--General Settings form-->
    <form class="nav-tab-content wpqs-general-settings-form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

        <input type="hidden" name="wpqs_tn" value="<?php echo $tab_index ?>" />

        <?php $wpurl = get_bloginfo('wpurl'); ?>
        <?php wp_nonce_field( 'wpqs_admin', 'wpqs_admin_settings' ); ?>

        <?php if($instructions==''){

        $instructions = __('You can buy multiple products quickly and easily.', 'wp-quick').' '.__('For more detail about the products or services, click on the title link.', 'wp-quick').' '.__('To do quick shopping, simply add the required quantity (Qty) for ALL the product(s) and/or service(s) you wish to buy, then scroll down to the bottom of the relevant section and click Add to Cart.', 'wp-quick').' '.__('Or simply click buy button for individual purchase and follow the tick for view cart link.', 'wp-quick');
        }
        ?>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-7">

                    <span class="h6 mt-4 mb-2 d-inline-block"><?php _e('Instructions for buyers', 'wp-quick'); ?>:</span><br />

                    <textarea name="wpqs_settings[instructions]"><?php echo stripslashes($instructions); ?></textarea>



                    <p class="submit"><input type="submit" value="<?php _e('Save Changes', 'wp-quick');?>" class="button button-primary" id="submit" name="submit"></p>
                    
                    <?php if(!$wpqs_pro): ?>
                    <a class="premium-features" href="<?php echo $wpqs_premium_link; ?>" target="_blank"><img src="<?php echo plugins_url('images/screenshot-7.png', dirname(__FILE__)); ?>" /></a>
                    <?php endif; ?>


                </div>

                <div class="col-md-5 pt-2">

                    <div class="card bg-dark text-white w-100 mt-5 wpqs_options_card">

                        <div class="row">
                            <div class="col-12">
                                <div class="h5 text-center">
                                    <?php _e('Optional', 'wp-quick');?>
                                </div>
                            </div>
                        </div>
                        
                       

                        <div class="row">
                            <div class="col-12">
                             <?php //pree($wpqs_options); ?>
                                <ul class="mt-3">

                                    <!--Copy this li and paste it and change value and other things for auto working-->
                                    <li>
                                        <label for="wpqs_options_search">
                                            <input <?php checked(array_key_exists('search', $wpqs_options)); ?> type="checkbox" name="wpqs_options" value="search" id="wpqs_options_search"  />
                                            <?php echo __('Search box on products table', 'wp-docs'); ?> (<?php echo $wpqs_pro?__('Optional', 'wp-quick'):'<a href="'.esc_url($wpqs_premium_link).'" target="_blank" class="qs-premium" title="'.__('Go Premium', 'wp-quick').'">'.__('Premium', 'wp-quick').'</a>'; ?>) - <a href="https://www.youtube.com/embed/rRKYoalkyJc" class="qs-link" target="_blank"><?php echo __('Video Tutorial', 'wp-quick'); ?></a>
                                        </label>
                                    </li>
                                    
                                    <li>
                                        <label for="wpqs_options_linked_user_role">
                                            <input <?php checked(array_key_exists('linked_user_option', $wpqs_options)); ?> type="checkbox" name="wpqs_options" value="linked_user_option" id="wpqs_options_linked_user_role"  />
                                            <?php echo __('WooCommerce Products Linked/Assigned to User Accounts/Roles', 'wp-docs'); ?> (<?php echo $wpqs_pro?__('Optional', 'wp-quick'):'<a href="'.esc_url($wpqs_premium_link).'" target="_blank" class="qs-premium" title="'.__('Go Premium', 'wp-quick').'">'.__('Premium', 'wp-quick').'</a>'; ?>) <a style="display:none" href="https://www.youtube.com/embed/rRKYoalkyJc" class="qs-link" target="_blank"><?php echo __('Video Tutorial', 'wp-quick'); ?></a>
<br />
                                            <?php
											
if(!empty($wp_roles)){
	$wpqs_linked_user_role = get_option('wpqs_linked_user_role');
	$wpqs_linked_user_role = (is_array($wpqs_linked_user_role)?$wpqs_linked_user_role:array());
	?>
<select name="wpqs_linked_user_role[]" multiple="multiple" style="width:100%;
height:150px;">
<?php foreach ( $wp_roles->roles as $key=>$value ): ?>
<option value="<?php echo $key; ?>" <?php selected(in_array($key, $wpqs_linked_user_role)); ?>><?php echo $value['name']; ?></option>
<?php endforeach; ?>
<?php
}
?>
</select>

                                        </label>
                                    </li>                                    



                                </ul>

                                <div class="alert alert-secondary fade in alert-dismissible mx-auto" style="width: 100%">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true" style="font-size:20px">×</span>
                                    </button>    <strong><?php echo __('Success!', 'wp-docs'); ?></strong> <?php echo __('Options are updated successfully.', 'wp-docs'); ?>
                                </div>


                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>


    </form>

    <!--Shortcodes form-->
    <form class="nav-tab-content hide wpqs-sohortcode-form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="wpqs_tn" value="<?php echo $tab_index ?>" />
        <?php wp_nonce_field( 'wpqs_admin', 'wpqs_admin_settings' ); ?>



        <div class="wpqs_notes"></div><br />
        <span class="h6 mt-4 mb-2 d-inline-block"><?php _e('Implementation with shortcode', 'wp-quick'); ?>:</span>
        <pre>[WP-QUICKSHOP]<br /><br />[WP-QUICKSHOP type="woocommerce"]<br /><br /><?php if(in_array('acf', $wpqs_enabled)){ ?>[WP-QUICKSHOP type="woocommerce" acf="field-name|field-label" columns-position="item_title,item_image,item_qty,item_price,buy_btn,field-name" meta-key="key|value or simply key to check for existence" sort-by="field-name" order-by="ASC|DESC" category-sort-by="comma separated product categories slug in order"]<br /><br /><?php } ?>[WP-QUICKSHOP type="wp-e-commerce"]</pre>


        <?php if(function_exists('wpqs_wc_cats')){ wpqs_wc_cats(); }else{ ?>
            <div class="wpqs-pro-features">
                <span class="h6 mt-4 mb-2 d-inline-block"><?php echo __('Premium Shortcodes', 'wp-quick'); ?>:</span><br />
                <a href="<?php echo $wpqs_premium_link; ?>" target="_blank" title="<?php _e('Click here to check Premium Shortcode feature', 'wp-quick'); ?>">
                <img src="<?php echo plugins_url('images/screenshot-3.png', dirname(__FILE__)); ?>" />
                <img src="<?php echo plugins_url('images/screenshot-4.png', dirname(__FILE__)); ?>" />
                </a>
            </div>
        <?php } ?>


        <?php if($wpqs_pro){ ?>
        <p class="submit"><input type="submit" value="<?php _e('Save Changes', 'wp-quick');?>" class="button button-primary mt-3" id="submit" name="submit"></p>
        <?php } ?>
    </form>


    <!--Custom field form-->
    <form class="nav-tab-content hide wpqs-custom-field-form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="wpqs_tn" value="<?php echo $tab_index ?>" />

        <div class="row mt-3">
            <div class="col-md-12">
                <?php echo $premium_alert ?>
            </div>
        </div>

    <?php wp_nonce_field( 'wpqs_admin', 'wpqs_admin_settings' ); ?>


    <ul class="wpqs-list">


        <li class="wpqs-list-item">

            <label for="wpqs_custom_field_show">
                <input type="checkbox" name="wpqs_custom_field[active]" id="wpqs_custom_field_show" value="1" <?php echo (array_key_exists('active', $wpqs_custom_field))?'checked':'' ?>/>
                <?php _e('Show custom field', 'wp-quick'); ?>
            </label>

        </li>

        <li class="wpqs-list-item">
            <label for="wpqs_custom_field_type"><?php _e('Custom field type', 'wp-quick'); ?>:</label>
            <select id="wpqs_custom_field_type" name="wpqs_custom_field[type]">
                <?php if(!empty($wpqs_custom_field_types)){
                    foreach ($wpqs_custom_field_types as $field_type => $field_name){

                        $selected = (isset($wpqs_custom_field['type']) && $wpqs_custom_field['type'] == $field_type)?'selected':'';
                        echo '<option value="'.$field_type.'" '.$selected.'>'.$field_name.'</option>';
                    }
                } ?>
            </select>
        </li>
        <li class="wpqs-list-item">
            <label for="wpqs_custom_field_placeholder"><?php _e('Column Heading / Field Label', 'wp-quick'); ?>:</label>
            <input type="text" name="wpqs_custom_field[label]" id="wpqs_custom_field_placeholder" value="<?php echo (isset($wpqs_custom_field['label'])?$wpqs_custom_field['label']:''); ?>"/>
        </li>
        <li class="wpqs-list-item">
            <label for="wpqs_custom_field_placeholder"><?php _e('Field place holder', 'wp-quick'); ?>:</label>
            <input type="text" name="wpqs_custom_field[placeholder]" id="wpqs_custom_field_placeholder" value="<?php echo (isset($wpqs_custom_field['placeholder'])?$wpqs_custom_field['placeholder']:''); ?>"/>
        </li>
        <li class="wpqs-list-item">
            <label for="wpqs_custom_field_title"><?php _e('Text to appear on hover/mouseover (Tooltip)', 'wp-quick'); ?>:</label>
            <input type="text" name="wpqs_custom_field[title]" id="wpqs_custom_field_title" value="<?php echo (isset($wpqs_custom_field['title'])?$wpqs_custom_field['title']:''); ?>"/>
        </li>
        <li class="wpqs-list-item">
            <label for="wpqs_custom_field_type"><?php _e('Field max length', 'wp-quick'); ?>:</label>
            <input type="number" name="wpqs_custom_field[max_length]" id="wpqs_custom_field_max_length" value="<?php echo (isset($wpqs_custom_field['max_length'])?$wpqs_custom_field['max_length']:''); ?>"/>
        </li>

    </ul>
    
    <iframe style="float:right;" width="560" height="315" src="https://www.youtube.com/embed/1fBxE9y8yQY" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

    <p class="submit"><input type="submit" value="<?php _e('Save Changes', 'wp-quick');?>" class="button button-primary" id="submit" name="submit"></p>

</form>

    <!--Styling form-->
    <form class="nav-tab-content hide wpqs-styling-form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="wpqs_tn" value="<?php echo $tab_index ?>" />
        <?php wp_nonce_field( 'wpqs_admin', 'wpqs_admin_settings' ); ?>


        <div class="container-fluid mt-3 wpqs-styling-wrapper">

            <?php

                if(!isset($wpqs_styling_settings['styling_type'])){
                    $wpqs_styling_settings['styling_type'] = array();
                }

            ?>


            <div class="row">

                <div class="col-md-12">

                    <div class="accordion" id="accordionExample">

                        <div class="card">
                            <div class="card-header" id="headingOne">
                                <h2 class="mb-0">
                                    <a class="wpqs-card-link collapsed"  data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <?php _e('Bootstrap', 'wp-quick') ?>
                                    </a>
                                    <span class="wpqs_checkbox_tick_wrapper d-none"><img src="<?php echo $wpqs_url ?>/images/tick.png" class="wpqs_checkbox_tick"></span>

                                </h2>
                            </div>

                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                <div class="card-body">

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <?php echo $premium_alert ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">

                                                <label for="wpqs_bootstrap_style">
                                                    <input type="checkbox" name="wpqs_styling_settings[styling_type][]" id="wpqs_bootstrap_style" value="bootstrap" <?php echo in_array('bootstrap', $wpqs_styling_settings['styling_type'])?'checked':'' ?>/>
                                                    <?php _e('Apply bootstrap styling on "Quick Shop" table', 'wp-quick'); ?>
                                                </label>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="headingTwo">
                                <h2 class="mb-0">
                                    <a class="wpqs-card-link collapsed"  data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <?php _e('Default', 'wp-quick') ?>
                                    </a>
                                    <span class="wpqs_checkbox_tick_wrapper d-none"><img src="<?php echo $wpqs_url ?>/images/tick.png" class="wpqs_checkbox_tick"></span>

                                </h2>
                            </div>

                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">

                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">

                                                <label for="wpqs_default_style">
                                                    <input type="checkbox" name="wpqs_styling_settings[styling_type][]" id="wpqs_default_style" value="default" <?php echo in_array('default', $wpqs_styling_settings['styling_type'])?'checked':'' ?>/>
                                                    <?php _e('Apply default styling on "Quick Shop" table', 'wp-quick'); ?>
                                                </label>

                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="card">

                            <div class="card-header" id="headingThree">
                                <h2 class="mb-0">
                                    <a class="wpqs-card-link show"  data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <?php _e( 'Custom', 'wp-quick') ?>
                                    </a>
                                    <span class="wpqs_checkbox_tick_wrapper d-none"><img src="<?php echo $wpqs_url ?>/images/tick.png" class="wpqs_checkbox_tick"></span>
                                </h2>
                            </div>

                            <div id="collapseThree" class="collapse show" aria-labelledby="headingThree" data-parent="#accordionExample">

                                <div class="card-body">

                                    <div class="row">


                                        <div class="col-md-12">
                                            <div class="form-group">

                                                <label for="wpqs_custom_style">
                                                    <input type="checkbox" name="wpqs_styling_settings[styling_type][]" id="wpqs_custom_style" value="custom" <?php echo in_array('custom', $wpqs_styling_settings['styling_type'])?'checked':'' ?>/>
                                                    <?php _e('Apply custom styling on "Quick Shop" table', 'wp-quick'); ?>
                                                </label>

                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">

                                                <?php


                                                if(isset($wpqs_styling_settings['custom_style_text']) && !empty($wpqs_styling_settings['custom_style_text'])){

                                                    $custom_style_text = $wpqs_styling_settings['custom_style_text'];

                                                }else{

                                                    include_once ('styling-data.php');
                                                    $custom_style_text = $default_css_tags;

                                                }




                                                ?>
<!--                                                <pre><code class="css">--><?php //echo $custom_style_text ?><!--</code></pre>-->
                                                <label for="wpqs_custom_styling"><?php _e('Custom Styling', 'wp-quick'); ?>:</label>
                                                <textarea class="form-control" rows="20" id="wpqs_custom_styling" name="wpqs_styling_settings[custom_style_text]">
                                                    <?php echo stripslashes($custom_style_text); ?>
                                                </textarea>

                                            </div>
                                        </div>


                                    </div>

                                </div>

                            </div>

                    </div>

                    </div>

                </div>

            </div>

            <div class="row">
                <div class="col-md-12">

                    <p class="submit"><input type="submit" value="<?php _e('Save Changes', 'wp-quick');?>" class="button button-primary" id="submit" name="submit"></p>

                </div>
            </div>

        </div>

    </form>


    <!--Compatibilities form-->
    <form class="nav-tab-content hide wpqs-compatibilities-form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="wpqs_tn" value="<?php echo $tab_index ?>" />
        <?php wp_nonce_field( 'wpqs_admin', 'wpqs_admin_settings' ); ?>

        <ul class="compatibility_list mt-5">

            <li></li>

            <li class="wpqs_wc"><a href="<?php echo $admin_url.(!in_array('woocommerce', $wpqs_enabled)?'plugin-install.php?tab=search&s=woocommerce':'admin.php?page=wc-settings'); ?>" target="_blank" title="<?php _e('Click here to install/activate and/or manage ', 'wp-quick');?>WooCommerce"></a></li>

            <li class="wpqs_wpec"><a href="<?php echo $admin_url.(!in_array('wp-e-commerce', $wpqs_enabled)?'plugin-install.php?tab=search&s=wp-e-commerce':'options-general.php?page=wpsc-settings'); ?>" target="_blank" title="<?php _e('Click here to install/activate and/or manage ', 'wp-quick'); ?>WP eCommerce"></a></li>

            <li class="wpqs_wdp"><a href="<?php echo $admin_url.(!in_array('woocommerce-discounts-plus', $wpqs_enabled)?'plugin-install.php?tab=search&s=woocommerce-discounts-plus':'admin.php?page=wc-settings&tab=plus_discount'); ?>" target="_blank" title="<?php _e('Click here to install/activate and/or manage ', 'wp-quick'); ?> Woocommerce Discounts Plus"></a></li>

            <li class="wpqs_wphi"><a href="<?php echo $admin_url.(!in_array('wp-header-images', $wpqs_enabled)?'plugin-install.php?tab=search&s=wp-header-images':'admin.php?page=wp_hi'); ?>" target="_blank" title="<?php _e('Click here to install/activate and/or manage ', 'wp-quick'); ?> WP Header Images"></a></li>


        </ul>



    </form>




    <script type="text/javascript" language="javascript">

        jQuery(document).ready(function($) {

            <?php if (isset($_GET['page']) && $_GET['page']=='wp_quickshop' && isset($_GET['t'])) :

            $t = (int)(isset($_REQUEST['t']) ? esc_attr($_REQUEST['t']) : 0);
            $t = (int)(isset($_POST['wpqs_tn']) ? esc_attr($_POST['wpqs_tn']) : $t);
            $t = trim($t);
			
				if(is_numeric($t)){
            ?>
            setTimeout(function() {

                $('.nav-tab-wrapper .nav-tab:nth-child(<?php echo $t + 1; ?>)').click();

            }, 100);

            <?php 
				}
			
			endif; 
			
			?>



        });
    </script>

<style type="text/css">
#menu-settings li.current a {
    color: #ff9900 !important;
}
</style>
