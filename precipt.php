			<div align="center" style="width:70%;">
			<h3>Purchase Receipt</h3>
				<p><?php echo $user_login; ?> - <?php echo date('M d, Y H:i:s'); ?></p>
			<table>
				<tr>
					<th>Products</th><th>Quantity</th><th>Price</th><th>Total</th>
				</tr>
				<tr>
				<?php for($i=0;$i<count($useridf);$i++){ ?>
					<td><?php echo $pname[$i]; ?></td><td><?php echo $quantity[$i]; ?></td><td><?php echo $regularprice[$i]; ?></td><td><?php echo $stotal[$i]; ?></td>
				</tr>
				<?php } ?>
				<tr>
				    <td></td><td></td><td>Total : </td><td><?php echo $totalit; ?></td>
				</tr>
			</table>
			<p style="text-align:center;">Child Care Providers Resource Network of Ottawa (CCPRN)<br>30 Colonnade Rd, Unit 275 Ottawa, ON K2E 7J6<br>Tel: 613-749-5211 Fax: 749-6650 Email: info@ccprn.com </p>	
			</div>
