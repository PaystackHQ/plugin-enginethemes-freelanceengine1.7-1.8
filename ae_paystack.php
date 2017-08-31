<?php
/*
Plugin Name: Paystack Gateway for Enginethemes.com FreelanceEngine site 1.7+ & 1.8+ 
Plugin URI: http://paystack.com/
Description: Integrates the Paystack payment gateway to FreelanceEngine site 1.7+, 1.8+
Version: 1.1
Author: kendysond
Author URI: http://kendyson.com/
License: GPLv2
*/

add_filter('ae_admin_menu_pages','ae_paystack_add_settings', 10, 2 );
function ae_paystack_add_settings($pages){
	$sections = array();
	$options = AE_Options::get_instance();

	/**
	 * ae fields settings
	 */
	$sections = array(
		'args' => array(
			'title' => __("Paystack API", ET_DOMAIN) ,
			'id' => 'meta_field',
			'icon' => 'F',
			'class' => ''
		) ,

		'groups' => array(
			array(
				'args' => array(
					'title' => __("Paystack Api Settings", ET_DOMAIN) ,
					'id' => 'secret-key',
					'class' => '',
					'desc' => __('Get your api keys from your Paystack dashboard settings, under "Developer/Api" Tab.<br>
						<!-- <h4>Optional: To avoid situations where bad network makes it impossible to verify transactions, set your webhook URL <a href="https://dashboard.paystack.co/#/settings/developer">here</a> to the URL below<strong style="color: red">
						<pre><code>'. admin_url("admin-ajax.php") . "?action=ae_kkd_paystack_webhook".'</code></pre>
						</strong></h4> -->', ET_DOMAIN),
					'name' => 'paystack'
				) ,
				'fields' => array(
					array(
                        'id' => 'mode',
                        // 'type' => 'radio',
                        'label' => __("Mode", ET_DOMAIN),
                        'title' => __("Mode", ET_DOMAIN),
                        'name' => 'mode',
                        'class' => '',
                         'type' => 'select',
                            'data' => array(
                                'disable' => __("Disable", ET_DOMAIN) ,
                                'test' => __("Test", ET_DOMAIN) ,
                                'live' => __("Live", ET_DOMAIN) ,
                            ) ,
                    ) ,
                    array(
						'id' => 'tsk',
						'type' => 'text',
						'label' => __("Test Secret Key", ET_DOMAIN) ,
						'name' => 'tsk',
						'class' => ''
					) ,
					array(
						'id' => 'tpk',
						'type' => 'text',
						'label' => __('Test Public Key', ET_DOMAIN),
						'name'  => 'tpk',
						'class' => ''
					),
					array(
						'id' => 'lsk',
						'type' => 'text',
						'label' => __("Live Secret Key", ET_DOMAIN) ,
						'name' => 'lsk',
						'class' => ''
					) ,
					array(
						'id' => 'lpk',
						'type' => 'text',
						'label' => __('Live Public Key', ET_DOMAIN),
						'name'  => 'lpk',
						'class' => ''
					)
					
					
				)
			)
		)
	);

	$temp = new AE_section($sections['args'], $sections['groups'], $options);

	$paystack_setting = new AE_container(array(
		'class' => 'field-settings',
		'id' => 'settings',
	) , $temp, $options);

	$pages[] = array(
		'args' => array(
			'parent_slug' => 'et-overview',
			'page_title' => __('Paystack', ET_DOMAIN) ,
			'menu_title' => __('Paystack Api Key settings', ET_DOMAIN) ,
			'cap' => 'administrator',
			'slug' => 'ae-paystack',
			'icon' => '$',
			'desc' => __("Integrate the Paystack payment gateway to your site", ET_DOMAIN)
		) ,
		'container' => $paystack_setting
	);
	return $pages;
}


add_filter( 'ae_support_gateway', 'ae_paystack_add' );
function ae_paystack_add($gateways){
	$gateways['paystack'] = 'Paystack';
	return $gateways;
}

add_action('after_payment_list', 'ae_paystack_render_button');
function ae_paystack_render_button() {
	$paystack = ae_get_option('paystack');
	if($paystack['mode'] ==  'disable')
		return false;
?>
	<li>
		<span class="title-plan select-payment" data-type="paystack">
			<?php _e("Paystack", ET_DOMAIN); ?>
			
		</span>
		<br>
			<img src="<?php echo plugins_url( 'logos@2x.png' , __FILE__ ); ?>" alt="cardlogos" style="width: 200px !important;"/>
			
		<a href="#" class="btn btn-submit-price-plan select-payment" style="display:block;" data-type="paystack"><?php _e("Select", ET_DOMAIN); ?></a>
	</li>
<?php
}
add_filter('ae_setup_payment', 'ae_paystack_setup_payment', 10, 3);
function ae_paystack_setup_payment($response, $paymentType, $order) {
    global $current_user,$user_email;
    
    if ($paymentType == 'PAYSTACK') {
        $paystack = ae_get_option('paystack');
		$mode = $paystack['mode'];
 		if ($mode == 'test') {
			$key = $paystack['tsk'];
		}else{
			$key = $paystack['lsk'];
		}
        //get info order
  //       $order = new AE_Order($order_id);
		// $order_data = $order->get_order_data();
        $order_pay = $order->generate_data_to_pay();
        $orderId = $order_pay['product_id'];
        $amount = $order_pay['total'];
        $currency = $order_pay['currencyCodeType'];
        $pakage_info = array_pop($order_pay['products']);
        $pakage_name = $pakage_info['NAME'];
        $paystack_info = ae_get_option('paystack');
        $new_id = $order_pay['ID'];
		$txnref	= $new_id . '_' .time();

		et_write_session( 'order_id', $new_id );
		
        $return_url = et_get_page_link('process-payment', array(
                        'paymentType' => 'paystack',
                        // 'return' => "1",
                        'order-id' =>  $new_id
                    )) ;
        $koboamount = $amount*100;
		
		$paystack_url = 'https://api.paystack.co/transaction/initialize';
		$headers = array(
			'Content-Type'	=> 'application/json',
			'Authorization' => "Bearer ".$key
		);
		//Create Plan
		$body = array(
			'email'	=> $user_email,
			'amount' => $koboamount,
			'reference' => $txnref,
			'callback_url' => $return_url
			// 'metadata' => json_encode(array('custom_fields' => $meta )),

		);
		$args = array(
			'body'		=> json_encode( $body ),
			'headers'	=> $headers,
			'timeout'	=> 60
		);

		$request = wp_remote_post( $paystack_url, $args );
		if( ! is_wp_error( $request )) {
			$paystack_response = json_decode(wp_remote_retrieve_body($request));
			$url	= $paystack_response->data->authorization_url;
			$order->update_order();
			$response = array(
                'success' => true,
                'data' => array(
                    'url' => $url,
                    'ACK' => true,
                ) ,
                'paymentType' => 'PAYSTACK'
            );
			
		}else{
			$response = array(
                'success' => false,
                'data' => array(
                    'url' => site_url('post-place') ,
                    'ACK' => false
                )
            );
		}
        
      
    }
    return $response;
}
add_filter('ae_process_payment', 'ae_paystack_process_payment', 10 ,2 );
function ae_paystack_process_payment($payment_return, $data) {
	$paystack = ae_get_option('paystack');
	$mode = $paystack['mode'];
		if ($mode == 'test') {
		$key = $paystack['tsk'];
	}else{
		$key = $paystack['lsk'];
	}
    $paymenttype = $data['payment_type'];
    $order = $data['order'];
    $order_pay = $order->generate_data_to_pay();
    
    $main_order_id = $order_pay['ID'];
    $main_amount = $order_pay['total'];
    $main_kobo_amount = $main_amount*100;
   
		// die();
    if($paymenttype == 'paystack' && isset($_GET['reference'])){
      	
        $reference = $_GET['reference'];

       	$paystack_url = 'https://api.paystack.co/transaction/verify/' . $reference;

		$headers = array(
			'Authorization' => 'Bearer ' . $key
		);

		$args = array(
			'headers'	=> $headers,
			'timeout'	=> 60
		);

		$request = wp_remote_get( $paystack_url, $args );
		if ( ! is_wp_error( $request ) && 200 == wp_remote_retrieve_response_code( $request ) ) {

            	$paystack_response = json_decode( wp_remote_retrieve_body( $request ) );

				if ( 'success' == $paystack_response->data->status ) {

					
					$order_details 	= explode( '_', $paystack_response->data->reference );

					$order_id = (int) $order_details[0];

						$amount_paid	= $paystack_response->data->amount;

		        		if ( $main_kobo_amount !=  $amount_paid ) {
							$payment_return = array(
				                'ACK' => false,
				                'payment' => 'paystack',
				                'payment_status' => 'fail',
			                	'msg' => 'Wrong amount paid'

			                );
						} else {
							$payment_return = array(
				                'ACK' => true,
				                'payment' => 'paystack',
				                'payment_status' => 'Completed'
			                );
			                wp_update_post( array(
								'ID'          => $order_id,
								'post_status' => 'publish'
							) );
							update_post_meta( $order_id, 'et_paid', 1 );
						}

				} else {
					$payment_return = array(
		                'ACK' => false,
		                'payment' => 'paystack',
		                'payment_status' => 'fail',
		                'msg' => "Couldn't Verify Transaction"
	                );
				

				}

	        }
    }
    
    return $payment_return;
}

// add_action( 'wp_ajax_ae_kkd_paystack_webhook', 'ae_kkd_paystack_webhook');
// add_action( 'wp_ajax_nopriv_ae_kkd_paystack_webhook', 'ae_kkd_paystack_webhook');
			
// function ae_kkd_paystack_webhook() {
// 	global $wpdb;
// 	// if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) || !array_key_exists('HTTP_X_PAYSTACK_SIGNATURE', $_SERVER) ) {
// 	//     exit();
// 	// }

// 	$input = @file_get_contents("php://input");
// 	$event = json_decode($input);
// 	echo "<pre>";
// 	$paystack = ae_get_option('paystack');
// 	$mode = $paystack['mode'];
// 		if ($mode == 'test') {
// 		$key = $paystack['tsk'];
// 	}else{
// 		$key = $paystack['tsk'];
// 	}

// 	// update_post_meta(634, 'paystack_status','COMPLETE');

// 	ae_paystack_process_payment('','' ,634);
// 	die();
// 	// die();
// 	// die();
// 	// print_r($event);
// 	// // if(!$_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] || ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, paystack_recurrent_billing_get_secret_key()))){
// 	// //   exit();
// 	// // }
// 	switch($event->event){
// 	    case 'subscription.create':

// 	        break;
// 	    case 'subscription.disable':
// 	        break;
//        	case 'charge.success':
// 	       	$reference =  $event->data->reference;
			
	       
// 	       	$paystack_url = 'https://api.paystack.co/transaction/verify/' . $reference;

// 			$headers = array(
// 				'Authorization' => 'Bearer ' . $key
// 			);

// 			$args = array(
// 				'headers'	=> $headers,
// 				'timeout'	=> 60
// 			);

// 			$request = wp_remote_get( $paystack_url, $args );
// 			if ( ! is_wp_error( $request ) && 200 == wp_remote_retrieve_response_code( $request ) ) {

//             	$paystack_response = json_decode( wp_remote_retrieve_body( $request ) );
//             	echo "string";
	
// 				if ( 'success' == $paystack_response->data->status ) {

					
// 					$order_details 	= explode( '_', $paystack_response->data->reference );

// 					$order_id = (int) $order_details[0];

// 						$amount_paid	= $paystack_response->data->amount;

// 		        		// check if the amount paid is equal to the order amount.
// 						if ( $main_kobo_amount !=  $amount_paid ) {
// 							$payment_return = array(
// 				                'ACK' => false,
// 				                'payment' => 'paystack',
// 				                'payment_status' => 'fail',
// 			                	'msg' => 'Wrong amount paid'

// 			                );
// 						} else {
// 							$payment_return = array(
// 				                'ACK' => true,
// 				                'payment' => 'paystack',
// 				                'payment_status' => 'complete'
// 			                );
// 	        				update_post_meta($order_id, 'paystack_status','COMPLETE');
// 						}
// 					// }

// 				} else {
// 					$payment_return = array(
// 		                'ACK' => false,
// 		                'payment' => 'paystack',
// 		                'payment_status' => 'fail',
// 		                'msg' => "Couldn't Verify Transaction"
// 	                );
				

// 				}

// 	        }
//    			print_r($payment_return);
// 			break;
// 	    case 'invoice.create':
	    	
// 	    case 'invoice.update':
	    	
	    	
// 	        break;
// 	}
// 	// http_response_code(200);
// 	// exit();
// }	