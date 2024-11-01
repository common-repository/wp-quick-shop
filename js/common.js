// JavaScript Document
jQuery(document).ready(function($){

	$('.qs-box').parents('pre.wp-block-preformatted:first').replaceWith($('.qs-box').parents('pre.wp-block-preformatted:first').html());

	$('.box-product.qs-content .qty-box').on('blur, change', function(){
		var obj = $(this);
		var val = obj.val();
		var row = obj.parents().closest('tr');
		var btn = row.find('.add_to_cart_button');
		var minq = parseInt(btn.attr('data-quantity-min')); 
		var maxq = parseInt(btn.attr('data-quantity-max')); 

		if((minq==0 || (minq>0 && val>=minq)) || val==0){
			btn.attr('data-quantity', val);
		}
		if(maxq>0 && val>maxq){
			btn.attr('data-quantity', maxq);
		}
		
		
		
	});		
	
	$('.box-product.qs-content .wpsc_buy_button').on('click', function(){
		var obj = $(this);
		obj.parent().find('.added_to_cart').remove();
		$.post(
			wpsc_ajax.ajaxurl, 
			{
				'action': 'wpsc_update_quantity',
				'key':-1,
				'product_id':   $(this).attr('data-product_id'),
				'wpsc_ajax_action': 'wpsc_update_quantity',
				'wpsc_ajax_action': 'add_to_cart',
				'wpsc_quantity_update': $(this).attr('data-quantity'),
				'wpsc_update_quantity': true
				
			}, 
			function(response){
				$(obj).parent().append('<a title="Successfuly added." class="added_to_cart"></a>');
				
			}
		);		
	});
	var wpqs_methods = {
	}
	$('.wpqs_cats, .wpec_cats').on('change', function(){
		//console.log($(this).val());
		if($(this).val()!=null){
			//console.log($(this).val());
			//console.log($(this).attr('class'));
			var ids = $(this).val();
			$('.'+$(this).attr('class')+'_code').html(ids.join());
		}

	});
	
	if($('.wpqs_cats, .wpec_cats').length>0){
		$('.wpqs_cats, .wpec_cats').trigger('change');
	}


	$('body').on('focusout','[name^="_wpqs_custom_field"]',  function(){

		console.log($(this));
		var custom_field_value = $(this).val();

		if(custom_field_value.length > 0) {
			var data = {
				action: "wpqs_save_custom_field_callback",
				_wpqs_custom_field: custom_field_value,
				product_id: $(this).data('id'),
			}


			jQuery.post(wpso.ajaxurl, data, function (response) {
				//console.log(response);
			});
		}
	});

	$('.wpqs_settings_div .nav-tab').click(function () {


			$(this).siblings().removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');
			$(this).index();
			$('.nav-tab-content, form:not(.wrap.ims-import-export-print-div .nav-tab-content)').hide();
			$('.nav-tab-content').eq($(this).index()).show();
			window.history.replaceState('', '', wpqs_obj.this_url + '&t=' + $(this).index());
			$('form input[name="wpqs_tn"]').val($(this).index());
			wpqs_obj.wp_ims_tab = $(this).index();

			// wos_trigger_selected_ie();

	});

	$('input[name^="wpqs_styling_settings"]').on('change', function() {

		if($(this).prop('checked') == true){


			$(this).parents('.card').find('.card-header .wpqs_checkbox_tick_wrapper').removeClass('d-none');


		}else{

			$(this).parents('.card').find('.card-header .wpqs_checkbox_tick_wrapper').addClass('d-none');

		}

	});

	$('input[name^="wpqs_styling_settings"]:checked').parents('.card').find('.card-header .wpqs_checkbox_tick_wrapper').removeClass('d-none');

	$(".wpqs-styling-wrapper textarea").keydown(function(e) {
		if(e.keyCode === 9) { // tab was pressed
			// get caret position/selection
			var start = this.selectionStart;
			var end = this.selectionEnd;

			var $this = $(this);
			var value = $this.val();

			// set textarea value to: text before caret + tab + text after caret
			$this.val(value.substring(0, start)
				+ "\t"
				+ value.substring(end));

			// put caret at right position again (add one for the tab)
			this.selectionStart = this.selectionEnd = start + 1;

			// prevent the focus lose
			e.preventDefault();
		}
	});

	var default_btn = $('.add_to_cart_button[id^="button-quickshop-cart"]');
	var bs_btn = $('.bootstrap_add_to_cart')

	if(wpqs_style_obj.style_types == undefined){
		wpqs_style_obj.style_types = [];
	}


	if(wpqs_style_obj.style_types.indexOf("bootstrap") != -1){

		bs_btn.show();
		default_btn.hide();


	}else{

		bs_btn.hide();
		default_btn.show();

	}

	bs_btn.on('click', function(){

		default_btn.click();

	});


	$('.wpqs_options_card [name^="wpqs_options"], .wpqs_options_card [name^="wpqs_linked_user_role"]').on('change', function(){

		var all_checkboxes = $('.wpqs_options_card [name^="wpqs_options"]');

		var wpqs_options = {};

		$.each(all_checkboxes, function () {

			if($(this).prop('checked')){

				wpqs_options[$(this).val()] = 1;

			}

		});



		var data = {

			action: 'wpqs_update_options',
			'wpqs_update_options_field': wpqs_obj.nonce,
			'wpqs_options': wpqs_options,
			'wpqs_linked_user_role': $('select[name^="wpqs_linked_user_role"]').val(),

		}

		$.post(ajaxurl, data, function(response, code){


			if(code == 'success'){


				$('.wpqs_options_card .alert').removeClass('fade');

				setTimeout(function(){

					$('.wpqs_options_card .alert').addClass('fade');


				}, 5000)

			}

		});

	});


	$('.qs_var_product').on('click', function(e){

		var link = $(this).find('td.qs-title a');

		if(link[0] != e.target){
			var product = $(this).data('product');
			$('.var_row_'+product).show();
		}

	});
	$('.qs_var_product').on('dblclick', function(e){

		var link = $(this).find('td.qs-title a');

		if(link[0] != e.target){
			var product = $(this).data('product');
			$('.var_row_'+product).hide();
		}

	});

	var selection_obj = {};

	$('.qs_variation_select').on('change', function(){

		var this_select = $(this);
		var selected_row = $(this).parents('tr.qs_var_product:first');
		var option_in_row = selected_row.find('.qs_variation_select');
		var variation_data = selected_row.data('variation');
		var product_id = selected_row.data('product');
		var parent_product_row = $('tr#product-'+product_id);
		var selected_option = 0;
		var add_to_cart =	parent_product_row.find('a.add_to_cart_button');
		var variation_input = parent_product_row.find('.variation_id');
		var variation_input_val = variation_input.val();
		// variation_input.val('');
		selection_obj = {};




		var all_selected = [];


		$.each(option_in_row, function () {

			var this_select = $(this);
			var selected_val = this_select.val();
			var name_prop = this_select.prop('name');
			name_prop = name_prop.replace('var['+product_id+'][variations][', '');
			name_prop = name_prop.replace(']', '');
			selection_obj[name_prop] = selected_val;

			if(selected_val == ''){

				all_selected.push(false);

			}else{

				selected_option++;

			}

		});

		var is_all_selected = selected_option == option_in_row.length;

		if(is_all_selected){

			add_to_cart.removeClass('disabled');

		}else{

			if(!add_to_cart.hasClass('disabled')){
				add_to_cart.addClass('disabled');
			}



		}

		if(selected_option == 0){
			variation_input.val(0);
		}

		if(is_all_selected){


		}

		$.each(variation_data, function(){

			var this_obj = this;
			var this_variation_id = this_obj.variation_id;
			var atr_obj = this_obj.attributes;
			var this_select_name = this_select.prop('name');
			this_select_name = this_select_name.replace('var['+product_id+'][variations][', '');
			this_select_name = this_select_name.replace(']', '');
			// this_select_name = this_select_name.toLowerCase();

			var this_select_value = this_select.val();
			var atr_obj_val = atr_obj[this_select_name];

			// console.log(this_select_name);
			// console.log(atr_obj_val);
			// console.log(this_obj.attributes);



			if((atr_obj_val=='' && this_select_value!='') || (atr_obj_val!='' && atr_obj_val==this_select_value)){


				variation_input.val(this_variation_id);

				return false;

			}


		});



		// console.log("\n");



	});

	$('.qs_var_product .add_to_cart_button').on('click', function(e){
		e.preventDefault();

		var parent_row = $(this).parents('.qs_var_product:first');
		var quantity_box = parent_row.find('.qty-box');
		var quantity = quantity_box.val();
		var variation_id = parent_row.find('.variation_id').val();
		var this_button = $(this);
		quantity_box.removeClass('error');
		this_button.addClass('loading');
		this_button.next('.added_to_cart').remove();


		if(quantity <= 0){

			quantity_box.addClass('error');
			this_button.removeClass('loading');
			return false;
		};

		var data = {

			"action": "wpqs_add_variation_to_cart",
			"product_id": parent_row.data('product'),
			"variation_id": variation_id,
			"quantity": quantity,
			"variation": selection_obj,
			"qs_ajax_nonce_field" : wpqs_obj.qs_ajax_nonce,

		}



		var success = '<a href="'+wpqs_obj.cart_url+'" class="added_to_cart wc-forward" title="View cart"></a>';


		$.post(wpqs_obj.ajax_url, data, function(response, code){

			this_button.removeClass('loading');

			if(code == 'success' && response == 'true'){

				this_button.after(success);

			}else{

			}

		});




		return false;
	})

	$('.qs_var_product .add_to_cart_button.disabled').on('click', function(e){
		e.preventDefault();
	})



});
