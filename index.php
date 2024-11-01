<?php
/*
Plugin Name: Wcik Pennsylvania Shipping
Plugin URI: http://imran1.com/woo-checkout-pennsylvania-based-surcharge-fee-my-clients-custom-solution/
Description: WooCommerce Plugin that adds checkout fields and surcharge for shipping to pennsylvania state
Version: 1.0
Author: Imran Khan
Author URI: http://imran1.com
License: GPL2
*/

/**
 * Copyright (c) 2018 Imran1 (email: info@imran1.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	add_action( 'woocommerce_before_order_notes', 'wcik_checkout_field' );

function wcik_checkout_field( $checkout ) {

    echo '<div id="Pennsylvania_shipping">
	<p class="form-row form-row notes woocommerce-validated">
	<div id="Pennsylvania_field">
	<span style="color:#990000; font-size:16px; font-weight:bold">Attention Pennsylvania Residents:</span>
	<p>The state of Pennsylvania requires us to ship your order to your nearest liquor store instead of directly to you. Please enter the identification number of your nearest liquor store in the box below. <a href="http://www.lcbapps.lcb.state.pa.us/app/Retail/storeloc.asp">You can find the number of your local store at this link</a>';

       
    woocommerce_form_field( 'pennsylvania_shipping', array(
        'type'          => 'text',
        'class'         => array('pennsylvania_shipping-class form-row-wide'),
        'label'         => __('<b>PA Liquour Store Number</b>'),
        'placeholder'   => __('Enter Liquor Store Number'),
		'required'  => true,
        ), $checkout->get_value( 'pennsylvania_shipping' ));


    echo '<b>By placing your order you are signifying that you have read and agree to the Special Terms & Fees information for Pennsylvania residents only. <a href="https://www.thekeywestwinery.com/privacy-policy/" style="color:#0000EE; font-size:16px; font-weight:bold">You can read the Special Terms & Fees information here</a> </div>
	</p>
	<div>';
	

}


/**
 * Process the checkout
 */
add_action('woocommerce_checkout_process', 'wcik_checkout_field_process');
 
function wcik_checkout_field_process() {
    // Check if set, if its not set add an error.
    if ( ! $_POST['pennsylvania_shipping'] )
        wc_add_notice( __( '<strong>Liquor Store Number</strong> is required' ), 'error' );
}



/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'wcik_checkout_field_update_order_meta' );
 
function wcik_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['pennsylvania_shipping'] ) ) {
        update_post_meta( $order_id, 'PA Liquor Store Number', sanitize_text_field( $_POST['pennsylvania_shipping'] ) );
    }
}

/**
 * Display field value on the admin order edit page
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'wcik_checkout_field_display_admin_order_meta', 10, 1 );

function wcik_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('PA Liquor Store Number').':</strong> <br>' . get_post_meta( $order->id, 'PA Liquor Store Number', true ) . '</p>';
}

add_action('wp_footer', 'wcik_myscript');

function wcik_myscript()
{?>


<script type="text/javascript">
jQuery('#pennsylvania_shipping').val('none');

jQuery(document).on('change','#billing_state', function(e){
	 e.preventDefault();
	   if(jQuery('#billing_state').val() == 'PA' && jQuery('#billing_country').val() == 'US')
   {
	   jQuery('#pennsylvania_shipping').val('');
	   jQuery('#Pennsylvania_field').show();
	    jQuery( 'body' ).trigger( 'update_checkout' );
   
   }
   else if(jQuery('#shipping_state').val() != 'PA') 
   {
	   jQuery('#Pennsylvania_field').hide();
	   jQuery( 'body' ).trigger( 'update_checkout' );
	   
   }
 });
 
 
 jQuery(document).on('change','#shipping_state', function(e){
	 e.preventDefault();
	   if(jQuery('#shipping_state').val() == 'PA' && jQuery('#shipping_country').val() == 'US')
   {
	   jQuery('#pennsylvania_shipping').val('');
	   jQuery('#Pennsylvania_field').show();
   }
   else 
   {
	   jQuery('#Pennsylvania_field').hide();
	   
   }
 });
 
  jQuery(document).on('change','#ship-to-different-address-checkbox', function(e){
	 e.preventDefault();
    if(!this.checked) {
        	
		if(jQuery('#billing_state').val() == 'PA' && jQuery('#billing_country').val() == 'US')
 		  {
	   jQuery('#pennsylvania_shipping').val('');
	   jQuery('#Pennsylvania_field').show();
   		}
    }
});

 

	jQuery(document).on('change','#shipping_country', function(e){
	 e.preventDefault();
	   if(jQuery('#shipping_country').val() != 'US')
   {
	   jQuery('#Pennsylvania_field').hide();
	  }
   });
//ship-to-different-address-checkbox
  </script>


<?php
}


add_action( 'woocommerce_cart_calculate_fees','wcik_add_surcharge' );
function wcik_add_surcharge() {
  global $woocommerce;
 
	if ( is_admin() && ! defined( 'DOING_AJAX' ) )
		return;

 	$county 	= array('PA');
	$surcharge = 4.50;
	
	if ( in_array( $woocommerce->customer->get_shipping_state(), $county ) ) :
			$woocommerce->cart->add_fee( 'Surcharge', $surcharge, true, '' );
	endif;
 
}

}
?>