<?php
include_once('../../../wp-config.php');
include_once('../../../wp-load.php');
include_once('../../../wp-includes/wp-db.php');
global $wpdb;
    $userId = $_POST['puser'];
 	$productId = $_POST['product'];
	$user_meta=get_userdata($userId);
//print_r($user_meta);
	$user_roles=$user_meta->roles; 
	$user_login= $user_meta->user_login;

echo $price = get_post_meta( $productId, '_regular_price', true)  . "<br>";
echo $sale = get_post_meta( $productId, '_sale_price', true);
$productname = $wpdb->get_results("SELECT post_title FROM wp_sww558lvqp_posts WHERE post_type = 'product' AND ID = $productId", ARRAY_A);
$pname = $productname[0]['post_title'];
?>
<input type="hidden" name="userid" value="<?php echo $userId; ?>" >
<input type="hidden" name="productId" value="<?php echo $productId; ?>" >
<input type="hidden" name="user_login" value="<?php echo $user_login; ?>" >
<input type="hidden" name="regularprice" value="<?php echo $price; ?>" >
<input type="hidden" name="saleprice" value="<?php echo $sale; ?>" >
<input type="hidden" name="pname" value="<?php echo $pname; ?>" >
<input type="hidden" name="urole" value="<?php echo $user_roles; ?>" >