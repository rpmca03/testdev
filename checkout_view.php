<?php
global $wpdb;
$paymentsql = $wpdb->get_results("SELECT * FROM payment ", ARRAY_A);
?>
<table border="1" cellpadding="0" cellspacing="3" width="100%">
	<tr>
		<th>User Name</th><th>Product Name</th><th>Payment For</th><th>Payment Method</th><th>Amount</th>
	</tr>
	<tr style="text-align:center; ">
	<?php
		foreach($paymentsql as $viewpayment){
		     $userId = $viewpayment['user_id'];
			 $user_meta=get_userdata($userId);
			 $user_roles=$user_meta->roles; 
			 $usern= $user_meta->user_login;
			 $eventId = $viewpayment['event_id'];
			 $tblevent = $wpdb->prefix . 'em_events';
			 $postId = $wpdb->get_results("SELECT post_id, event_name FROM " . $tblevent . " WHERE event_id = '$eventId'", ARRAY_A);
			 $eventn = $postId[0]['event_name'];
			 $productId = $viewpayment['product_id'];
			 $tblproduct = $wpdb->prefix . 'posts';
			 $productname = $wpdb->get_results("SELECT post_title FROM " . $tblproduct . " WHERE post_type = 'product' AND ID = $productId", ARRAY_A);
			 $pname = $productname[0]['post_title'];
			 $paymentFor = $viewpayment['payment_for'];
			 $amount = $viewpayment['amount'];
			 $paymethod = $viewpayment['payment_method'];
			 if($paymentFor == 'event'){
			 	$prodname = $eventn;
				$paymentFor = 'event';
			 }else if ($paymentFor == 'product'){
			 	$prodname = $pname;
				$paymentFor = 'product';
			 }else{
			    $prodname = $user_roles[0];
				$paymentFor = 'user';
			 }
				echo "<td style=\"text-align:center; \">$usern</td><td style=\"text-align:center; \">$prodname</td><td style=\"text-align:center; \">$paymentFor</td><td style=\"text-align:center; \">$paymethod</td><td style=\"text-align:center; \">$amount</td>";
	echo "</tr>";
		}
	?>
</table>