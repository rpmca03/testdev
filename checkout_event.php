<?php 
	global $wpdb;
	$plugins_url = plugins_url();
	if($action != 'checkout'){
?>

<form name="cartfrm" id="cartfrm" method="post" action="<?php echo admin_url(); ?>admin.php?page=event_area&action=add">
<table border="0" cellpadding="15px" cellspacing="10px">
	<tr>
		<th>User</th>
		<td><select id="euser" name="euser" style="width:300px;">
				<option value="">SELECT USER</option>
				<?php
				    $userlist = $wpdb->get_results("SELECT ID, user_login FROM wp_sww558lvqp_users WHERE ID NOT IN(1,3455,9371,9350) ORDER BY ID", ARRAY_A);
		
					foreach($userlist as $userval){
				?>
					<option value="<?php _e($userval['ID']); ?>"><?php _e($userval['user_login']); ?></option>
				<?
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<th>Event</th>
		<td><select id="event" name="event" style="width:300px;">
				<option value="">SELECT EVENT</option>
				<?php
				    $eventlist = $wpdb->get_results("SELECT event_id, event_name FROM wp_sww558lvqp_em_events WHERE event_start_date >= CURDATE() ORDER BY event_id", ARRAY_A);
		
					foreach($eventlist as $eventval){
				?>
					<option value="<?php _e($eventval['event_id']); ?>"><?php _e($eventval['event_name']); ?></option>
				<?
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<th>Event Price</th>
		<td><span id="eventid"></span></td>
	</tr>
	    <tr>
		<td><input type="submit" name="addtocart" value="Add Event"></td>
	</tr> 

	</form>

	<?php
	}
	if(isset($_SESSION["cart_item"])){
	$cartIt = $_SESSION["cart_item"];
	$cartIt['userid'];
		$item_total = 0;
		$qttotal = 0;
	?>	
	<form name="chkoutfrm" id="chkoutfrm" method="post" action="<?php echo admin_url(); ?>admin.php?page=event_area&action=checkout">
	<tr>
		<td colspan="2">
			<table border="0" cellpadding="10" cellspacing="1" width="100%">

				<tr>
					<th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th>
				</tr>
				<tr>
					<?php
						foreach($_SESSION["cart_item"] as $prodval){
						//echo $prodval['eventId'];
					?>
					<input type="hidden" name="eventId[]" value="<?php echo $prodval['eventId']; ?>">
					<input type="hidden" name="userid[]" value="<?php echo $prodval['userid']; ?>">
					<input type="hidden" name="usern[]" value="<?php echo $prodval['usern']; ?>">
					<input type="hidden" name="ename[]" value="<?php echo $prodval['ename']; ?>">
					<input type="hidden" name="equantity0[]" value="<?php echo $prodval['equantity0']; ?>">
					<input type="hidden" name="equantity1[]" value="<?php echo $prodval['equantity1']; ?>">
					<input type="hidden" name="equantity2[]" value="<?php echo $prodval['equantity2']; ?>">
					<input type="hidden" name="equantity3[]" value="<?php echo $prodval['equantity3']; ?>">
					<input type="hidden" name="equantity4[]" value="<?php echo $prodval['equantity4']; ?>">
					<input type="hidden" name="eprice0[]" value="<?php echo $prodval['eprice0']; ?>">
					<input type="hidden" name="eprice1[]" value="<?php echo $prodval['eprice1']; ?>">
					<input type="hidden" name="eprice2[]" value="<?php echo $prodval['eprice2']; ?>">
					<input type="hidden" name="eprice3[]" value="<?php echo $prodval['eprice3']; ?>">
					<input type="hidden" name="eprice4[]" value="<?php echo $prodval['eprice4']; ?>">
					<input type="hidden" name="totqtity[]" value="<?php echo $prodval['totqtity']; ?>">
					<input type="hidden" name="totpric[]" value="<?php echo $prodval['totpric']; ?>">
					<input type="hidden" name="pricsum[]" value="<?php echo $prodval['pricsum']; ?>">
	
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><?php echo stripslashes($prodval['ename']);?></td>
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><?php echo $prodval['pricsum'];?></td>
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><?php echo $prodval['totqtity']; ?></td>
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><?php echo $prodval['totpric'];?></td>
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><a href="<?php echo admin_url(); ?>admin.php?page=event_area&action=remove&code=<?php echo $prodval['userid']; ?>" class="btnRemoveAction">Remove Item</a></td>
				</tr>
								<?php
        						$item_total += ($prodval["pricsum"]*$prodval['totqtity']);
                                $qttotal += $prodval['totqtity'];
				 				}?>
				 <tr>
					<td colspan="5" align=right><strong>Total:</strong> <?php echo "$".$item_total; ?></td>
					<input type="hidden" name="totalit" value="<?php echo $item_total; ?>">
					<input type="hidden" name="qty" value="<?php echo $qttotal; ?>">
					</tr>
					

			</table>
		</td>
	</tr>  
	<tr>
		<td>Method:</td>
		<td><select name="paymentMethod">
			<option></option><option value="cash">Cash / Cheque</option>
			<option value="Credit Card">Credit Card</option>
			<option value="Pay Later">Pay Later</option>
		</select></td>
	</tr>
</table>
        
		<input type="submit" value="Complete" />
        <input type="hidden" name="completeTotal" />

</form>
<?php } ?>

<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script>
$(document).ready(function(){
    $('#event').change(function(){
     
        // show that something is loading
        $('#eventid').html("<b>Loading response...</b>");
         var vname = $("#euser").val();
		 var ename = $("#event").val();
        $.ajax({
            url: '<?php echo $plugins_url; ?>/checkout/event.php', 
			dataType: 'text',
			type: 'POST',
            data: $(this).serialize()
        })
        .done(function(data){
             
            // show the response
            $('#eventid').html(data);
             
        })
        .fail(function() {
         
            // just in case posting your form failed
            alert( vname );
             
        });
 
        // to prevent refreshing the whole page page
        return false;
 
    });
});
</script>