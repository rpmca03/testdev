<?php 
	global $wpdb;
	$plugins_url = plugins_url();
	$users = get_users( [ 'role__in' => [ 'Caregiver Member', 'Parent Associate', 'Organization Associate' ] ] );
	/* global $current_user;
      $cuser = get_currentuserinfo();
      echo 'Username: ' . $current_user->user_login . "\n";
      echo 'User email: ' . $current_user->user_email . "\n";
      echo 'User level: ' . $current_user->user_level . "\n";
      echo 'User first name: ' . $current_user->user_firstname . "\n";
      echo 'User last name: ' . $current_user->user_lastname . "\n";
      echo 'User display name: ' . $current_user->display_name . "\n";
      echo 'User ID: ' . $current_user->ID . "\n";*/

	if($action != 'checkout'){
?>

<form name="cartfrm" id="cartfrm" method="post" action="<?php echo admin_url(); ?>admin.php?page=user_area&action=add">
<table border="0" cellpadding="15px" cellspacing="10px">
	<tr>
		<th>User</th>
		<td><select id="user" name="user" style="width:300px;">
				<option value="">SELECT USER</option>
				<?php
				   // $userlist = $wpdb->get_results("SELECT ID, user_login FROM wp_sww558lvqp_users WHERE ID NOT IN(1,3455,9371,9350) ORDER BY ID", ARRAY_A);
		
					foreach($users as $userval){
				?>
					<option value="<?php _e($userval->ID); ?>"><?php _e($userval->user_login); ?></option>
				<?
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<th>Member Type </th><td><span id="stateid"></span></td>
	</tr>
    <tr><input type="hidden" name="quantity" value="1">
		<td><input type="submit" name="addtocart" value="Add Account Renewal"></td>
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

	<form name="chkoutfrm" id="chkoutfrm" method="post" action="<?php echo admin_url(); ?>admin.php?page=user_area&action=checkout">    
	<tr>
		<td colspan="2">
			<table border="0" cellpadding="10" cellspacing="1" width="100%">

				<tr>
					<th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th>
				</tr>
				<tr>
					<?php
						foreach($_SESSION["cart_item"] as $prodval){
						
					?>
					<input type="hidden" name="role[]" value="<?php echo $prodval['role']; ?>">
					<input type="hidden" name="userid[]" value="<?php echo $prodval['userid']; ?>">
					<input type="hidden" name="price[]" value="<?php echo $prodval['price']; ?>">
					<input type="hidden" name="quantity[]" value="<?php echo $prodval['quantity']; ?>">
					<input type="hidden" name="uname[]" value="<?php echo $prodval['usern']; ?>">
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><?php echo $prodval['role'];?></td>
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><?php echo $prodval['price'];?></td>
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><?php echo $prodval['quantity']; ?></td>
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><?php echo $prodval['price'];?></td>
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><a href="<?php echo admin_url(); ?>admin.php?page=user_area&action=remove&code=<?php echo $prodval['userid']; ?>" class="btnRemoveAction">Remove Item</a></td>
				</tr>
								<?php
        						$item_total += ($prodval["price"]*$prodval['quantity']);
                                $qttotal += $prodval['quantity'];
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
                    <th>Method:</th>
                    <td><select name="paymentMethod">
						<option></option><option value="cash">Cash / Cheque</option>
						<option value="Credit Card">Credit Card</option>
					</select></td>
                </tr>
</table>
        <input type="submit" value="Complete" />
        <input type="hidden" name="completeTotal" value="user" />
</form>
<?php } ?>

<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script>
$(document).ready(function(){
    $('#user').change(function(){
     
        // show that something is loading
        $('#stateid').html("<b>Loading response...</b>");
         var vname = $("#user").val()
        $.ajax({
            url: '<?php echo $plugins_url; ?>/checkout/member.php', 
			dataType: 'text',
			type: 'POST',
            data: $(this).serialize()
        })
        .done(function(data){
             
            // show the response
            $('#stateid').html(data);
             
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