<?php
include_once('../../../wp-config.php');
include_once('../../../wp-load.php');
include_once('../../../wp-includes/wp-db.php');
global $wpdb;
    $userId = $_POST['euser'];
    $eventId = $_POST['event'];

	$user_meta=get_userdata($userId);
    //print_r($user_meta);
	$user_roles=$user_meta->roles; 
	$user_login= $user_meta->user_login;
	$user_email = $user_meta->user_email;
	$eventdata = $wpdb->get_results("SELECT * FROM wp_sww558lvqp_em_tickets WHERE event_id = '$eventId' ", ARRAY_A);
	//echo "SELECT * FROM `wp_sww558lvqp_em_tickets WHERE event_id = '$eventId' ";
	//print_r($eventdata);
	?>
	<input type="hidden" name="user_login" value="<?php echo $edata['user_login']; ?>">
	<input type="hidden" name="user_roles" value="<?php echo $edata['user_roles']; ?>">
	<input type="hidden" name="user_email" value="<?php echo $edata['user_email']; ?>">
	<?php
	$i = 0;
	foreach($eventdata as $edata){
	    $ticketid = $edata['ticket_id'];
			$sqlticketbook = $wpdb->get_results("SELECT SUM(ticket_booking_spaces) as totalval FROM `wp_sww558lvqp_em_tickets_bookings` WHERE ticket_id = '$ticketid' GROUP BY ticket_id", ARRAY_A);
		$booked = $sqlticketbook[0]['totalval'];
		$totalspac = $edata['ticket_spaces'];
		$availab = $totalspac - $booked;  
		echo $edata['ticket_name'] . ' $' . $edata['ticket_price'] . " Available : " . $availab . "&nbsp;"; ?>
		<input type="text" style="width:100px;" id="equantity<?php echo $i; ?>" name="equantity<?php echo $i; ?>" ><br>
		<input type="hidden" name="eprice<?php echo $i;?>" value="<?php echo $edata['ticket_price']; ?>">
		<input type="hidden" name="ticketn<?php echo $i;?>" value="<?php echo $edata['ticket_name']; ?>">
		
		
<?php		
		$i++;
	}
	$postId = $wpdb->get_results("SELECT post_id, event_name FROM `wp_sww558lvqp_em_events` WHERE event_id = '$eventId'", ARRAY_A);
	//print_r($eventdata);
	$ticket_name = $eventdata[0]['ticket_name'];
	$ticket_price = $eventdata[0]['ticket_price'];
	$ticket_start = $eventdata[0]['ticket_start'];
	$ticket_end = $eventdata[0]['ticket_end'];
	$ticket_spaces = $eventdata[0]['ticket_spaces'];
	$pId = $postId[0]['post_id']; 
    $key_1_value = get_post_meta( $pId);
	//print_r($key_1_value);
	$eventsd = get_post_meta( $pId, '_event_start_date', true);
	$evented = get_post_meta( $pId, '_event_end_date', true);
	$eventst = get_post_meta( $pId, '_event_start_time', true);
	$eventet = get_post_meta( $pId, '_event_end_time', true);
	$eventad = get_post_meta( $pId, '_event_all_day', true);
	//$facilator = get_post_meta( $pId, 'Facilator', true);
	//$space = get_post_meta( $pId, '_event_spaces', true);
     echo "<br>";
	 echo "Event Start Date : " . $eventsd . " " . $eventst;
     echo "<br>";
	 echo "Event End Date : " . $evented . " " . $eventet;
?>
	<input type="hidden" name="ename" value="<?php echo $postId[0]['event_name']; ?>">
