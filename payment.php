    <link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url( __FILE__ ); ?>css/styles.css">

	<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ ); ?>js/jquery-3.1.1.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="<?php echo plugin_dir_url( __FILE__ ); ?>js/jquery.payform.min.js" charset="utf-8"></script>
    <script src="<?php echo plugin_dir_url( __FILE__ ); ?>js/script.js"></script>

<h3>Amount Pay : $<?php echo $total_pric; ?></h3>

<form name="paymentfrm" action="" method="post" id="myform">
	<table border="0" cellpadding="3" cellspacing="20">
		<tr>
			<td>Card Holder Name</td>
			<td><input type="text" name="chname" value="<?php echo $cuusername; ?>" id="owner"></td>
		</tr>
		<tr>
			<td>Card Number</td>
			<td><input type="text" name="cnumber" id="cardNumber"></td>
		</tr>
		<tr>
			<td>Expiry Month</td>
			<td> <div class="form-group" id="expiration-date"><select id="expiration-month" name="expmonth">
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
            </select></td>
		</tr>
		<tr>
			<td>Expiry Year</td>
			<td> <select id="expyear" name="expyear">
                <?php 
                    $yearRange = 20;
                    $thisYear = date('Y');
                    $startYear = ($thisYear + $yearRange);
                 
                    foreach (range($thisYear, $startYear) as $year) 
                    {
                        if ( $year == $thisYear) {
                            print '<option value="'.$year.'" selected="selected">' . $year . '</option>';
                        } else {
                            print '<option value="'.$year.'">' . $year . '</option>';
                        }
                    }
                ?>
            </select></div></td>
		</tr>
		<tr>
			<td>CVD</td>
			<td><input type="password" name="cvd" id="cvv"></td>
		</tr>
		<tr>
			<td> <div class="form-group" id="credit_cards">
                        <img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/visa.jpg" id="visa">
                        <img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/mastercard.jpg" id="mastercard">
                        <img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/amex.jpg" id="amex">
                    </div></td>
					
			<td><input type="submit" name="completeTotal" value="Pay Now"  id="confirm-purchase"></td>
		</tr>
	</table>
</form>