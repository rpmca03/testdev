<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL  & ~E_NOTICE);
/**
* Plugin Name: Checkout
* Plugin URI: http://ccprn.com/
* Description: This Plugin will do checkout process.
* Version: 1.0 
* Author: Parag Ranjan
* Author URI: www.paragranjan.com
* License: This plugin is not for commercial use
*/
define('CHECKDIR', plugin_dir_path(__FILE__));

global $wpdb;

	function ch_menu_items(){

		add_menu_page('Checkout', 'Checkout', 'activate_plugins', 'checkout', 'checkout_view');
		add_submenu_page( 'checkout', 'User Area', 'User', 'manage_options', 'user_area', 'checkout_user');
		add_submenu_page( 'checkout', 'Product Area', 'Product', 'manage_options', 'product_area', 'checkout_product');
		add_submenu_page( 'checkout', 'Event Area', 'Event', 'manage_options', 'event_area', 'checkout_event');
		add_submenu_page( 'checkout', '', '', 'manage_options', 'online_payment', 'onlinepayment');
	}

	add_action('admin_menu', 'ch_menu_items');


function checkout_view(){
global $wpdb;
	include('checkout_view.php');	
}

function checkout_user(){
global $wpdb;
     //$complete = $_POST['completeTotal'];
	 $user_login = $_POST['user_login'];
	 $user = $_POST['user'];
	 $urole = $_POST['urole'];
	 $price = $_POST['price'];
	 $action = $_GET['action'];
	 $quantity = $_POST['quantity'];
	 if($action == 'add'){
	 $itemArray = array($user => array('userid' => $user, 'usern' => $user_login, 'role' => $urole, 'price' => $price, 'quantity' => $quantity));
	 			if(!empty($_SESSION["cart_item"])) {
					if(in_array($user,array_keys($_SESSION["cart_item"]))) {
						foreach($_SESSION["cart_item"] as $k => $v) {
								if($user == $k) {
									if(empty($_SESSION["cart_item"][$k]["quantity"])) {
										$_SESSION["cart_item"][$k]["quantity"] = 0;
									}
									$_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
								}
						}
					} else {
						$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
					}
			} else {
				$_SESSION["cart_item"] = $itemArray;
			}
		}
//For Remove Cart Item
if($action == 'remove'){
//print_r($_SESSION["cart_item"]);
		if(!empty($_SESSION["cart_item"])) {
			foreach($_SESSION["cart_item"] as $k => $v) {
			// print_r($v['userid']);
					if($_GET["code"] == $v['userid'])
					
						unset($_SESSION["cart_item"][$k]);				
					if(empty($_SESSION["cart_item"]))
						unset($_SESSION["cart_item"]);
			}
		}
	}

if($action == 'checkout'){
  $role = $_POST['role'];
  $strole = implode(',', $role);
  $userid = $_POST['userid'];
  $price = $_POST['price'];
  $stprice = implode(',', $price);
  $quantity = $_POST['quantity'];
  $uname = $_POST['uname'];
  $totalit = $_POST['totalit'];
  $totqty = $_POST['qty'];
  $paymentMethod = $_POST['paymentMethod'];
 
  include('urecipt.php');

   if($paymentMethod == 'cash'){
   		
		$membershipExpiry = date('Y-m-t',mktime(0,0,0,date("m"),date("d"),date("Y")+1));
		for($i=0;$i<=count($userid);$i++){
			$rolef = $role[$i];
			$useridf = $userid[$i];
			$pricef = $price[$i];
			$quantityf = $quantity[$i];
			$unamef = $uname[$i];
				$wpdb->update( 
					'ccp_users', 
					array( 
						'membership_expiry' => $membershipExpiry 
					), 
					array( 'id' => $useridf )
					);
			 $ordernum = $rolef . '-' . $useridf;
			 	$payinsert = array(
					user_id => $useridf,
					payment_id => '',
					payment_for => 'user',
					approved => '1',
					message => 'cash',
					created => date("Y-m-d"),
					amount => $pricef,
					auth_code => 'cash',
					payment_method => $paymentMethod,
					order_number => $ordernum
					);
			   $payinsertid = $wpdb->insert('payment', $payinsert);
			   
			   unset($_SESSION["cart_item"]);
			   
		}
   }else if($paymentMethod == 'Credit Card'){
   	    $_SESSION['price'] = $price;
		$_SESSION['role'] = $role;
		$_SESSION['quantity'] = $quantity;
		$_SESSION['userid'] = $userid;
        $_SESSION['uname'] = $uname;
		$_SESSION['paymentfor'] = "user";
		$_SESSION['total_qty'] = $totqty;
		$_SESSION['total_item'] = $totalit;
		$_SESSION['membershipExpiry'] = $membershipExpiry;
		$_SESSION['ordernumber'] = $ordernum;
	   wp_redirect(get_option('siteurl'). '/wp-admin/admin.php?page=online_payment');
   }
}
	 
	 include('checkout_user.php');


}

function checkout_product(){
global $wpdb;
	 //$complete = $_POST['complete'];
	$action = $_GET['action'];
	 if(isset($_POST['puser'])){
	 $userid = $_POST['puser'];
	 $user_meta=get_userdata($userid);
	 $user_login = $user_meta->user_login;
	 $urole = $user_meta->roles;
	 $productId = $_POST['product'];
	 $regularprice = $_POST['regularprice'];
     $rprice = intval(preg_replace('/[^\d.]/', '', $regularprice));
	 $saleprice = $_POST['saleprice'];
	 $pname = $_POST['pname'];
	 $quantity = $_POST['quantity'];
	 $total = $rprice * $quantity;
	
	 if($action == 'add'){
		 $itemArray = array($userid => array('userid' => $userid, 'productId' => $productId, 'usern' => $user_login, 'regularprice' => $rprice, 'saleprice' => $saleprice, 'pname' => $pname, 'quantity' => $quantity, 'stotal' => $total));
		 if(!empty($_SESSION["cart_item"])) {
			if(in_array($userid,array_keys($_SESSION["cart_item"]))) {
			
				foreach($_SESSION["cart_item"] as $k => $v) {
				
						if($userid == $k) {
							if(empty($_SESSION["cart_item"][$k]["quantity"])) {
								$_SESSION["cart_item"][$k]["quantity"] = 0;
							}
							$_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
						}
				 }
			 } else {
			 
						$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
					}
		} else {
			$_SESSION["cart_item"] = $itemArray;
		}
	}
}
//For Remove Cart Item
if($action == 'remove'){
		if(!empty($_SESSION["cart_item"])) {
			foreach($_SESSION["cart_item"] as $k => $v) {
					if($_GET["code"] == $v['userid'])
					
						unset($_SESSION["cart_item"][$k]);				
					if(empty($_SESSION["cart_item"]))
						unset($_SESSION["cart_item"]);
			}
		}
	}

	
if($action == 'checkout'){

    $useridf = $_POST['userid'];
	$productId = $_POST['productId'];
	$usern = $_POST['usern'];
	$regularprice = $_POST['regularprice'];
	$saleprice = $_POST['saleprice'];
	$pname = $_POST['pname'];
	$quantity = $_POST['quantity'];
	$stotal = $_POST['stotal'];
	$totalit = $_POST['totalit'];
	$totqty = $_POST['qty'];
	 $paymentMethod = $_POST['paymentMethod'];
	include('precipt.php');
	
	if($paymentMethod == 'cash'){
		for($i=0;$i<count($useridf);$i++){
			$user_meta=get_userdata($useridf[$i]);
	 		$user_login = $user_meta->user_login;
	 		$urole = $user_meta->roles;
	 		$ordernum = $urole . '-' . $useridf[$i];
			$pname = $pname[$i];
			$quantity = $quantity[$i];
			$regularprice = $regularprice[$i];
			$total = $stotal[$i];
			
			$payinsert = array(
				user_id => $useridf[$i],
				event_id => '',
				product_id => $productId[$i],
				payment_id => '',
				payment_for => 'product',
				approved => '1',
				message => 'cash',
				created => date("Y-m-d"),
				amount => $stotal[$i],
				auth_code => 'cash',
				payment_method => $paymentMethod,
				order_number => $ordernum
				);
			//$wpdb->show_errors();
			$payinsertid = $wpdb->insert('payment', $payinsert);
			//$wpdb->print_error();
			global $woocommerce;
			$post_date = date("Y-m-d H:i:s");
			$userresult = $wpdb->get_results("SELECT * FROM ccp_users WHERE id = $useridf[$i]", ARRAY_A);
			$user_email = $userresult[0]['email'];
			$uphone = $userresult[0]['phone'];
			$uaddress = $userresult[0]['address'];
			$city = $userresult[0]['city'];
			$uprovince = $userresult[0]['province_id'];
			
			$provienamql = $wpdb->get_results("SELECT name FROM ccp_provinces WHERE id = $uprovince", ARRAY_A);
			$provnam = $provienamql[0]['name'];
			$upostal = $userresult[0]['postal_code'];
			$ucountry = 'CA';
			
			$item_args['qty'] = $quantity[$i];
			
			$order = wc_create_order(['customer_id' => $useridf[$i]]);
			$order->set_total( $stotal[$i], 'total');
			$order->set_payment_method('Cash On Delivery');
			$order->add_product( wc_get_product( $productId[$i] ), $item_args['qty'], $item_args);
			$addressShipping = array(
				   'first_name' => $user_login,
				   'email'      => $user_email,
				   'phone'      => $uphone,
				   'address_1'  => $uaddress,
				   'address_2'  => $provnam,
				   'city'       => $city,
				   'state'      => $provnam,
				   'postcode'   => $upostal,
				   'country'    => 'CA');
			$order->set_address( $addressShipping, 'shipping' );
				$addressBilling = array(
				   'first_name' => $user_login,
				   'email'      => $user_email,
				   'phone'      => $uphone,
				   'address_1'  => $uaddress,
				   'address_2'  => $uprovince,
				   'city'       => $city,
				   'state'      => $provnam,
				   'postcode'   => $upostal,
				   'country'    => 'CA');
			$order->set_address( $addressBilling, 'billing' );
			$order->calculate_totals();
			$order->update_status('completed', 'order_note');
			 unset($_SESSION["cart_item"]);
		}
	}else if($paymentMethod == 'Credit Card'){
			$_SESSION['regularprice'] = $regularprice;
			$_SESSION['saleprice'] = $saleprice;
			$_SESSION['stotal'] = $stotal;
			$_SESSION['total_item'] = $totalit;
			$_SESSION['totqty'] = $totqty;
			$_SESSION['quantity'] = $quantity;
			$_SESSION['userid'] = $useridf;
			$_SESSION['uname'] = $usern;
			$_SESSION['productId'] = $productId;
			$_SESSION['pname'] = $pname;
			$_SESSION['paymentfor'] = "product";
		   wp_redirect(get_option('siteurl'). '/wp-admin/admin.php?page=online_payment');

	}else{
		echo "Please select payment method";
	}
}
include('checkout_product.php');

}
//booking data will insert wp_sww558lvqp_em_bookings
//wp_sww558lvqp_em_tickets_bookings

function checkout_event(){
global $wpdb;
$action = $_GET['action'];
if(isset($_POST['euser'])){
		$userId = $_POST['euser'];
		$eventId = $_POST['event'];
		$user_meta=get_userdata($userId);
		$usname = $user_meta->user_login;
	    $urole = $user_meta->roles;
		$uemail = $_POST['user_email'];
		$equantity0 = $_POST['equantity0'];
		$equantity1 = $_POST['equantity1'];
		$equantity2 = $_POST['equantity2'];
		$equantity3 = $_POST['equantity3'];
		$equantity4 = $_POST['equantity4'];
		$eprice0 = $_POST['eprice0'];
		$eprice1 = $_POST['eprice1'];
		$eprice2 = $_POST['eprice2'];
		$eprice3 = $_POST['eprice3'];
		$eprice4 = $_POST['eprice4'];
		$ename = $_POST['ename'];
		$totqtity = $equantity0 + $equantity1 + $equantity2 + $equantity3 + $equantity4;
		$pricsum = $eprice0 + $eprice1 + $eprice2 + $eprice3 + $eprice4;
		$totpric = ($equantity0 * $eprice0) + ($equantity1 * $eprice1) + ($equantity2 * $eprice2) + ($equantity3 * $eprice3) + ($equantity4 * $eprice4); 
		$ordernum = $urole[0] . '-' . $userId;
		
		if($action == 'add'){
			$itemArray = array($userId => array('userid' => $userId, 'eventId' => $eventId, 'usern' => $usname, 'equantity0' => $equantity0, 'equantity1' => $equantity1, 'equantity2' => $equantity2, 'equantity3' => $equantity3, 'equantity4' => $equantity4, 'eprice0' => $eprice0, 'eprice1' => $eprice1, 'eprice2' => $eprice2, 'eprice3' => $eprice3, 'eprice4' => $eprice4, 'ename' => $ename, 'totqtity' => $totqtity, 'totpric' => $totpric, 'pricsum' => $pricsum));
			
		 if(!empty($_SESSION["cart_item"])) {
			if(in_array($userId,array_keys($_SESSION["cart_item"]))) {
			
				foreach($_SESSION["cart_item"] as $k => $v) {
				
						if($userId == $k) {
							if(empty($_SESSION["cart_item"][$k]["totqtity"])) {
								$_SESSION["cart_item"][$k]["totqtity"] = 0;
							}
							$_SESSION["cart_item"][$k]["totqtity"] += $_POST["totqtity"];
						}
				 }
			 } else {
			 
						$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
					}
		} else {
			$_SESSION["cart_item"] = $itemArray;
		}
	}
}
//For Remove Cart Item
if($action == 'remove'){
		if(!empty($_SESSION["cart_item"])) {
			foreach($_SESSION["cart_item"] as $k => $v) {
					if($_GET["code"] == $v['userid'])
					
						unset($_SESSION["cart_item"][$k]);				
					if(empty($_SESSION["cart_item"]))
						unset($_SESSION["cart_item"]);
			}
		}
	}
	
if($action == 'checkout'){

	$paymentMethod = $_POST['paymentMethod'];
	$eventId = $_POST['eventId'];
	$userid = $_POST['userid'];
	$usern = $_POST['usern'];
	$ename = $_POST['ename'];
	$equantity0 = $_POST['equantity0'];
	$equantity1 = $_POST['equantity1'];
	$equantity2 = $_POST['equantity2'];
	$equantity3 = $_POST['equantity3'];
	$equantity4 = $_POST['equantity4'];
	$eprice0 = $_POST['eprice0'];
	$eprice1 = $_POST['eprice1'];
	$eprice2 = $_POST['eprice2'];
	$eprice3 = $_POST['eprice3'];
	$eprice4 = $_POST['eprice4'];
	$totqtity = $_POST['totqtity'];
	$totpric = $_POST['totpric'];
	$pricsum = $_POST['pricsum'];
	$totalit = $_POST['totalit'];
	$qty = $_POST['qty'];
	
	if($paymentMethod != 'Credit Card'){
	include('erecipt.php');
	for($e=0;$e<count($userid);$e++){
		$user_meta=get_userdata($userid[$e]);
	    $urole = $user_meta->roles;
		$totqtity = $equantity0[$e] + $equantity1[$e] + $equantity2[$e] + $equantity3[$e] + $equantity4[$e];
		$pricsum = $eprice0[$e] + $eprice1[$e] + $eprice2[$e] + $eprice3[$e] + $eprice4[$e];

		$ordernum = $urole[0] . '-' . $userid[$e];

			$payinsert = array(
				user_id => $userid[$e],
				event_id => $eventId[$e],
				product_id => '',
				payment_id => '',
				payment_for => 'event',
				approved => '1',
				message => 'cash',
				created => date("Y-m-d"),
				amount => $totpric[$e],
				auth_code => 'cash',
				payment_method => $paymentMethod,
				order_number => $ordernum
				);
		 		
		$payinsertid = $wpdb->insert('payment', $payinsert);	
		
		$tblbookings = $wpdb->prefix . 'em_bookings';
		$eminsert = array(
			event_id => $eventId[$e],
			person_id => $userid[$e],
			booking_spaces => $totqtity[$e],
			booking_comment => '',
			booking_date => date("Y-m-d H:i:s"),
			booking_status => 1,
			booking_price => $totpric[$e],
			booking_tax_rate => '',
			booking_taxes => '',
			booking_meta => ''
		);	
		$payinsertid = $wpdb->insert($tblbookings, $eminsert);
		$bookingId = $wpdb->insert_id;
		
		$ticketId = $wpdb->get_results("SELECT ticket_id FROM " . $wpdb->prefix . "em_tickets WHERE event_id = '$eventId[$e]' ", ARRAY_A);
		$ticket_id = $ticketId[0]['ticket_id'];
		$tbltickbook = $wpdb->prefix . 'em_tickets_bookings';
		$i = 0;
		foreach($ticketId as $tival){
		$ftiv = $tival['ticket_id'];
		if($i==0){
			$qty = $equantity0[$e];
			$pri = $eprice0[$e];
		}else if($i==1){
			$qty = $equantity1[$e];
			$pri = $eprice1[$e];
		}else if($i==2){
			$qty = $equantity2[$e];
			$pri = $eprice2[$e];
		}else if($i==3){
			$qty = $equantity3[$e];
			$pri = $eprice3[$e];
		}else if($i==4){
			$qty = $equantity4[$e];
			$pri = $eprice4[$e];
		}
		$emtinsert = array(
			booking_id => $bookingId,
			ticket_id => $ftiv,
			ticket_booking_spaces => $qty,
			ticket_booking_price => $pri,
		);	
		$payinsertid = $wpdb->insert($tbltickbook, $emtinsert);
		$i++;
		}
    }
	 unset($_SESSION["cart_item"]);
	}else if($paymentMethod == 'Credit Card'){
			$_SESSION['price'] = $totpric;
			$_SESSION['equantity'] = $totqtity;
			$_SESSION['userid'] = $userid;
			$_SESSION['uname'] = $usern;
			$_SESSION['eventId'] = $eventId;
			$_SESSION['evname'] = $ename;
			$_SESSION['paymentfor'] = "event";
			$_SESSION['equantity0'] = $equantity0;
			$_SESSION['equantity1'] = $equantity1;
			$_SESSION['equantity2'] = $equantity2;
			$_SESSION['equantity3'] = $equantity3;
			$_SESSION['equantity4'] = $equantity4;
			$_SESSION['eprice0'] = $eprice0;
			$_SESSION['eprice1'] = $eprice1;
			$_SESSION['eprice2'] = $eprice2;
			$_SESSION['eprice3'] = $eprice3;
			$_SESSION['eprice4'] = $eprice4;
			$_SESSION['pricsum'] = $pricsum;
			$_SESSION['total_item'] = $totalit;
			$_SESSION['total_qty'] = $qty; 
		   wp_redirect(get_option('siteurl'). '/wp-admin/admin.php?page=online_payment');
	
	}else{
		echo "Please Select Payment Method";
	}

}
include('checkout_event.php');
}
//Payment process

function onlinepayment(){
global $wpdb;
 global $current_user;
 get_currentuserinfo();
 		$user_email = $current_user->user_email;
        $cuusername = $current_user->user_login;
		$cuseid = $current_user->ID;
		$price = $_SESSION['price'];
		$userId = $_SESSION['userid'];
		$urole = $_SESSION['role'];
		$uname = $_SESSION['uname'];
		$quantity = $_SESSION['quantity'];
		$paymentfor = $_SESSION['paymentfor'];
		$total_qty = $_SESSION['total_qty'];
		$total_pric = $_SESSION['total_item'];
		$membershipExpiry = $_SESSION['membershipExpiry'];
		$ordernum = $_SESSION['ordernumber'];
		if(empty($_SESSION['productId'])){
			 $productid = '';
		}else{
			$productid = $_SESSION['productId'];
		}
		if(empty($_SESSION['eventId'])){
			 $eventid = '';
		}else{
			$eventid = $_SESSION['eventId'];
		}
		
		$userresult = $wpdb->get_results("SELECT * FROM ccp_users WHERE id = $cuseid", ARRAY_A);
		//$user_email = $userresult[0]['email'];
		
		$uphone = $userresult[0]['phone'];
		$uaddress = $userresult[0]['address'];
		$city = $userresult[0]['city'];
		$uprovince = $userresult[0]['province_id'];
		$provienamql = $wpdb->get_results("SELECT name FROM ccp_provinces WHERE id = $uprovince", ARRAY_A);
		$provnam = $provienamql[0]['name'];
	    $upostal = $userresult[0]['postal_code'];
		$ucountry = 'CA';
			
		include('payment.php');
		if(isset($_POST['chname'])){
					$chname = $_POST['chname'];
					$cnumber = $_POST['cnumber'];
					$expmonth = $_POST['expmonth'];
					$expyear = substr($_POST['expyear'], -2);
					$cvd = $_POST['cvd'];
					$getvalue = $wpdb->get_results("SELECT * from beanstream WHERE type = 't'", ARRAY_A);
					$merid = $getvalue[0]['merchant_id'];
					$passcod = $getvalue[0]['api_passcode'];
					//init api settings (beanstream dashboard > administration > account settings > order settings)
						//$merchant_id = '300204214'; //INSERT MERCHANT ID (must be a 9 digit string)
						//$api_key = 'ad719C3034BB4A279013c2cC9e16A7BE'; //INSERT API ACCESS PASSCODE
					$merchant_id = $merid; //INSERT MERCHANT ID (must be a 9 digit string)
					$api_key = $passcod; //INSERT API ACCESS PASSCODE
					$api_version = 'v1'; //default
					$platform = 'www'; //default
						
						
						//generate a random order number, and set a default $amount (only used for example functions)
						//test card number '4030000010001234'
					$order_number = bin2hex(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
					$amount = $total_pric;
						
        require_once(CHECKDIR . 'Beanstream/Gateway.php');
						//init new Beanstream Gateway object
					$beanstream = new \Beanstream\Gateway($merchant_id, $api_key, $platform, $api_version);
						
						//Card Payment Data
					$payment_data = array(
								'order_number' => $order_number,
								'amount' => $amount,
								'payment_method' => 'card',
								'card' => array(
									'name' => $chname,
									'number' => $cnumber,
									'expiry_month' => $expmonth,
									'expiry_year' => $expyear,
									'cvd' => $cvd
								),
								'billing' => array(
									'name' => $chname,
									'email_address' => $user_email,
									'phone_number' => $uphone,
									'address_line1' => $uaddress,
									'city' => $city,
									'province' => $provnam,
									'postal_code' => $upostal,
									'country' => 'CA'
								),
								/*'shipping' => array(
									'name' => 'Shipping Name',
									'email_address' => 'email@email.com',
									'phone_number' => '1234567890',
									'address_line1' => '789-123 Shipping St.',
									'city' => 'Shippingsville',
									'province' => 'BC',
									'postal_code' => 'V8J9I5',
									'country' => 'CA'
								)*/
						);
						$complete = TRUE; //set to FALSE for PA
						
						//Try to submit a Card Payment
						try {
						
							$result = $beanstream->payments()->makeCardPayment($payment_data, $complete);
							
							if($paymentfor == 'user'){
								for($i=0;$i<=count($userId);$i++){
								$rolef = $role[$i];
								$useridf = $userId[$i];
								$pricef = $price[$i];
								$quantityf = $quantity[$i];
								$unamef = $uname[$i];

								$payinsert = array(
									user_id => $useridf,
									event_id => $eventid,
									product_id => $productid,
									payment_id => $result['id'],
									payment_for => $paymentfor,
									approved => $result['approved'],
									message => $result['message'],
									created => $result['created'],
									amount => $pricef,
									auth_code => $result['auth_code'],
									payment_method => $result['payment_method'],
									order_number => $result['order_number']
								);
								//$wpdb->show_errors();
								$payinsertid = $wpdb->insert('payment', $payinsert);
									$wpdb->update( 
										'ccp_users', 
										array( 
											'membership_expiry' => $membershipExpiry 
										), 
										array( 'id' => $useridf )
										);
								}
							}
							if($paymentfor == 'product'){
									$useridf = $_SESSION['userid'];
									$ordernum = $result['order_number'];
									$pname = $_SESSION['pname'];
									$quantity = $_SESSION['quantity'];
									$regularprice = $_SESSION['regularprice'];
									$stotal = $_SESSION['stotal'];
								for($i=0;$i<count($useridf);$i++){
									$user_meta=get_userdata($useridf[$i]);
									$user_login = $user_meta->user_login;
									$urole = $user_meta->roles;
									$pname = $pname[$i];
									$quantity = $quantity[$i];
									$regularprice = $regularprice[$i];
									$total = $stotal[$i];
									
									$payinsert = array(
										user_id => $useridf[$i],
										event_id => $eventid,
										product_id => $productid[$i],
										payment_id => $result['id'],
										payment_for => 'product',
										approved => '1',
										message => $result['message'],
										created => $result['created'],
										amount => $stotal[$i],
										auth_code => $result['auth_code'],
										payment_method => $result['payment_method'],
										order_number => $result['order_number']
										);
									//$wpdb->show_errors();
									$payinsertid = $wpdb->insert('payment', $payinsert);
									//$wpdb->print_error();
									global $woocommerce;
									$post_date = date("Y-m-d H:i:s");
									$userresult = $wpdb->get_results("SELECT * FROM ccp_users WHERE id = $useridf[$i]", ARRAY_A);
									$user_email = $userresult[0]['email'];
									$uphone = $userresult[0]['phone'];
									$uaddress = $userresult[0]['address'];
									$city = $userresult[0]['city'];
									$uprovince = $userresult[0]['province_id'];
									
									$provienamql = $wpdb->get_results("SELECT name FROM ccp_provinces WHERE id = $uprovince", ARRAY_A);
									$provnam = $provienamql[0]['name'];
									$upostal = $userresult[0]['postal_code'];
									$ucountry = 'CA';
									
									$item_args['qty'] = $quantity[$i];
									
									$order = wc_create_order(['customer_id' => $useridf[$i]]);
									$order->set_total( $stotal[$i], 'total');
									$order->set_payment_method('Beanstream');
									$order->add_product( wc_get_product( $productid[$i] ), $item_args['qty'], $item_args);
									$addressShipping = array(
										   'first_name' => $user_login,
										   'email'      => $user_email,
										   'phone'      => $uphone,
										   'address_1'  => $uaddress,
										   'address_2'  => $provnam,
										   'city'       => $city,
										   'state'      => $provnam,
										   'postcode'   => $upostal,
										   'country'    => 'CA');
									$order->set_address( $addressShipping, 'shipping' );
										$addressBilling = array(
										   'first_name' => $user_login,
										   'email'      => $user_email,
										   'phone'      => $uphone,
										   'address_1'  => $uaddress,
										   'address_2'  => $uprovince,
										   'city'       => $city,
										   'state'      => $provnam,
										   'postcode'   => $upostal,
										   'country'    => 'CA');
									$order->set_address( $addressBilling, 'billing' );
									$order->calculate_totals();
									$order->update_status('completed', 'order_note');
								}
							}
							if($paymentfor == 'event'){
								$equantity = $_SESSION['equantity'];
								$equantity0 = $_SESSION['equantity0'];
								$equantity1 = $_SESSION['equantity1'];
								$equantity2 = $_SESSION['equantity2'];
								$equantity3 = $_SESSION['equantity3'];
								$equantity4 = $_SESSION['equantity4'];
								$eprice0 = $_SESSION['eprice0'];
								$eprice1 = $_SESSION['eprice1'];
								$eprice2 = $_SESSION['eprice2'];
								$eprice3 = $_SESSION['eprice3'];
								$eprice4 = $_SESSION['eprice4'];
								$pricsum = $_SESSION['pricsum'];

									for($e=0;$e<count($userId);$e++){
										$user_meta=get_userdata($userId[$e]);
										$urole = $user_meta->roles;
										$totqtity = $equantity0[$e] + $equantity1[$e] + $equantity2[$e] + $equantity3[$e] + $equantity4[$e];
										$pricsum = $eprice0[$e] + $eprice1[$e] + $eprice2[$e] + $eprice3[$e] + $eprice4[$e];
									
											$payinsert = array(
												user_id => $userId[$e],
												event_id => $eventid[$e],
												product_id => $productid,
												payment_id => $result['id'],
												payment_for => 'event',
												approved => '1',
												message => $result['message'],
												created => $result['created'],
												amount => $price[$e],
												auth_code => $result['auth_code'],
												payment_method => $result['payment_method'],
												order_number => $result['order_number']
												);
												
										$payinsertid = $wpdb->insert('payment', $payinsert);	
										
										$tblbookings = $wpdb->prefix . 'em_bookings';
										$eminsert = array(
											event_id => $eventid[$e],
											person_id => $userId[$e],
											booking_spaces => $quantity[$e],
											booking_comment => '',
											booking_date => date("Y-m-d H:i:s"),
											booking_status => 1,
											booking_price => $price[$e],
											booking_tax_rate => '',
											booking_taxes => '',
											booking_meta => ''
										);	
										$payinsertid = $wpdb->insert($tblbookings, $eminsert);
										$bookingId = $wpdb->insert_id;
										
										$ticketId = $wpdb->get_results("SELECT ticket_id FROM " . $wpdb->prefix . "em_tickets WHERE event_id = '$eventid[$e]' ", ARRAY_A);
										$ticket_id = $ticketId[0]['ticket_id'];
										$tbltickbook = $wpdb->prefix . 'em_tickets_bookings';
										$i = 0;
										foreach($ticketId as $tival){
										$ftiv = $tival['ticket_id'];
										if($i==0){
											$qty = $equantity0[$e];
											$pri = $eprice0[$e];
										}else if($i==1){
											$qty = $equantity1[$e];
											$pri = $eprice1[$e];
										}else if($i==2){
											$qty = $equantity2[$e];
											$pri = $eprice2[$e];
										}else if($i==3){
											$qty = $equantity3[$e];
											$pri = $eprice3[$e];
										}else if($i==4){
											$qty = $equantity4[$e];
											$pri = $eprice4[$e];
										}
										$emtinsert = array(
											booking_id => $bookingId,
											ticket_id => $ftiv,
											ticket_booking_spaces => $qty,
											ticket_booking_price => $pri,
										);	
										$payinsertid = $wpdb->insert($tbltickbook, $emtinsert);
										$i++;
										}
									}
									 unset($_SESSION["cart_item"]);
							}
							echo "Pament successful";
							exit;
							//wp_redirect(home_url()); 
							/*
							 * Handle successful transaction, payment method returns
							 * transaction details as result, so $result contains that data
							 * in the form of associative array.
							 */
						} catch (\Beanstream\Exception $e) {
							/*
							 * Handle transaction error, $e->code can be checked for a
							 * specific error, e.g. 211 corresponds to transaction being
							 * DECLINED, 314 - to missing or invalid payment information
							 * etc.
							 */
							 echo "<pre>";
							  print_r($e);
							  if(!empty($e) && empty($chname)){
							 // echo "Doing something wrong.";
							  echo "<pre>";
							  }
					}
			}
}