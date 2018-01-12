			<div align="center" style="width:70%;">
			<h3>Purchase Receipt for <?php echo $paymentMethod; ?></h3>
				<p> Date - <?php echo date('M d, Y H:i:s'); ?></p>
			<table>
				<tr>
					<th>Username</th><th>Products</th><th>Quantity</th><th>Price</th><th>Total</th>
				</tr>
				<tr>
				<?php for($r=0;$r<count($userid);$r++){?>
				
					<td><?php echo $usern[$r]; ?></td><td><?php echo stripslashes($ename[$r]); ?></td><td><?php echo $totqtity[$r]; ?></td><td><?php echo $totpric[$e]; ?></td><td><?php echo $pricsum[$e]; ?></td>
				</tr>
				<?php } ?>
				<tr>
					<td></td><td></td><td></td><td>Total</td><td><?php echo $totalit;?></td>
				</tr>
			</table>
			<p style="text-align:center;">Child Care Providers Resource Network of Ottawa (CCPRN)<br>30 Colonnade Rd, Unit 275 Ottawa, ON K2E 7J6<br>Tel: 613-749-5211 Fax: 749-6650 Email: info@ccprn.com </p>	
			</div>
