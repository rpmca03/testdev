<?php
include_once('../../../wp-config.php');
include_once('../../../wp-load.php');
include_once('../../../wp-includes/wp-db.php');
global $wpdb;
    $userId = $_POST['user'];
	$user_meta=get_userdata($userId);
//print_r($user_meta);
	$user_roles=$user_meta->roles; 
	$user_login= $user_meta->user_login;
	//echo($user_login);
    $urole = $user_roles[0];
	if($urole == 'Caregiver Member'){
		$price = 25.00;
	}else if($urole == 'Parent Associate'){
		$price = 25.00;
	}else if($urole == 'Organization Associate'){
		$price = 25.00;
	}else if($urole == 'Caregiver Non-Member'){
		$price = 0.00;
	}else if($urole == 'Parent Non-Associate'){
		$price = 0.00;
	}else {
		$price = 0.00;
	}
	 echo $membership = $urole . ' - $' . $price;
?>
<input type="hidden" name="urole" value="<?php echo $urole; ?>" >
<input type="hidden" name="price" value="<?php echo $price; ?>" >
<input type="hidden" name="user_login" value="<?php echo $user_login; ?>" >