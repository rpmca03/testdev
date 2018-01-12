<?php 
	global $wpdb;
	$plugins_url = plugins_url();
	if($action != 'checkout'){
?>

<form name="cartfrm" id="cartfrm" method="post" action="<?php echo admin_url(); ?>admin.php?page=product_area&action=add">
<table border="0" cellpadding="15px" cellspacing="10px">
	<tr>
		<th>User</th>
		<td><select id="puser" name="puser" style="width:300px;">
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
		<th>Poduct</th>
		<td><select id="product" name="product" style="width:300px;">
				<option value="">SELECT PRODUCT</option>
				<?php
				    $productlist = $wpdb->get_results("SELECT ID, post_title FROM wp_sww558lvqp_posts WHERE post_type = 'product' GROUP BY post_title", ARRAY_A);
		
					foreach($productlist as $productval){
				?>
					<option value="<?php _e($productval['ID']); ?>"><?php _e($productval['post_title']); ?></option>
				<?
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<th>Product Price </th><td>$<span id="productid"></span></td>
	</tr>
	<tr>
		<th>Quantity</th>
		<td><input type="text" name="quantity"></td>
	</tr>
	    <tr><!--<input type="hidden" name="quantity" value="1">-->
		<td><input type="submit" name="addtocart" value="Add Product"></td>
	</tr> 
	</form>

	<?php
	}
	//print_r($_SESSION["cart_item"]);
	if(isset($_SESSION["cart_item"])){
	$cartIt = $_SESSION["cart_item"];
	
		$item_total = 0;
		$qttotal = 0;
	?>	
		<form name="chkoutfrm" id="chkoutfrm" method="post" action="<?php echo admin_url(); ?>admin.php?page=product_area&action=checkout">    
	<tr>
		<td colspan="2">
			<table border="0" cellpadding="10" cellspacing="1" width="100%">

				<tr>
					<th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th>
				</tr>
				<tr>
					<?php
						foreach($_SESSION["cart_item"] as $prodval){
						//print_r($prodval);
					?>
					<input type="hidden" name="userid[]" value="<?php echo $prodval['userid']; ?>">
					<input type="hidden" name="productId[]" value="<?php echo $prodval['productId']; ?>">
					<input type="hidden" name="usern[]" value="<?php echo $prodval['usern']; ?>">
					<input type="hidden" name="regularprice[]" value="<?php echo $prodval['regularprice']; ?>">
					<input type="hidden" name="saleprice[]" value="<?php echo $prodval['saleprice']; ?>">
					<input type="hidden" name="pname[]" value="<?php echo $prodval['pname']; ?>">
					<input type="hidden" name="quantity[]" value="<?php echo $prodval['quantity']; ?>">
					<input type="hidden" name="stotal[]" value="<?php echo $prodval['stotal']; ?>">
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><?php echo $prodval['pname'];?></td>
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><?php echo $prodval['regularprice'];?></td>
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><?php echo $prodval['quantity']; ?></td>
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><?php echo $prodval['stotal'];?></td>
					<td style="text-align:center;border-bottom:#F0F0F0 1px solid;"><a href="<?php echo admin_url(); ?>admin.php?page=product_area&action=remove&code=<?php echo $prodval['userid']; ?>" class="btnRemoveAction">Remove Item</a></td>
				</tr>
								<?php
        						$item_total += ($prodval["regularprice"]*$prodval['quantity']);
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
		<td>Method:</td>
		<td><select name="paymentMethod">
			<option></option><option value="cash">Cash / Cheque</option>
			<option value="Credit Card">Credit Card</option>
		</select></td>
	</tr>
</table>
        <input type="submit" value="Complete" name="complete" />
        <!--<input type="hidden" name="completeTotal" />-->
</form>
<?php } ?>

<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script>
$(document).ready(function(){
    $('#product').change(function(){
     
        // show that something is loading
        $('#productid').html("<b>Loading response...</b>");
         var vname = $("#puser").val();
		 var pname = $("#product").val();
        $.ajax({
            url: '<?php echo $plugins_url; ?>/checkout/product.php', 
			dataType: 'text',
			type: 'POST',
            data: $(this).serialize()
        })
        .done(function(data){
             
            // show the response
            $('#productid').html(data);
             
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