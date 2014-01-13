<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * Woocommerce Manage Shipping
 *
 * @package   woocommerce-manage-shipping
 * @author    Niels Donninger <niels@donninger.nl>
 * @license   GPL-2.0+
 * @link      http://donninger.nl
 * @copyright 2013 Donninger Consultancy
 */
 
 $url = get_permalink() . "?page=" . $_GET["page"];
?>
<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function(){
			jQuery("input[type='checkbox']").click(function(){
				//PART 1: save/undo shipping metadata on order item level
				var url = '';
				var thisCheck = jQuery(this);
				if (thisCheck.is (':checked')) {
					url = "<?php echo $url ?>&" + thisCheck.attr('name') + "=" +thisCheck.val();
					//alert(url);
					jQuery.get( url, function( data ) {
					  jQuery( "#result_" +thisCheck.val() ).html("Item " +thisCheck.val() +" is shipped!");
					});				
				} else {
					url = "<?php echo $url ?>&undo=1&" + thisCheck.attr('name') + "=" +thisCheck.val();
					jQuery.get( url, function( data ) {
					  jQuery( "#result_" +thisCheck.val() ).html("Shipping of item " +thisCheck.val() +" was undone");
					});
				}

				//PART 2: if all order items are shipped, complete order
				var orderClass = thisCheck.attr('class');
				var allIsShipped = true;
				jQuery("."+orderClass).each(function(){
					//alert(jQuery(this).is (':checked'));
					if(!jQuery(this).is (':checked')) {
						allIsShipped = false;
						//alert(jQuery(this).val() +" is not checked");
					}
				});
				var orderId = orderClass.substring(12); //order id is after 12th position in orderClass
				//alert(orderId);
				if(allIsShipped) { //all items checked: all items sent
					//save order status to 'completed'					
					url = "<?php echo $url ?>&complete_order=" +orderId;
					jQuery.get( url, function() {
						//hiding order rows
						jQuery(".order_" +orderId).css("background","green");
						jQuery(".order_" +orderId).hide(1000);
						console.log(url);
					});
				}
			});
		});
		//]]>
</script>
<div id="result"></div>
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<!-- @TODO: Provide markup for your options page here. -->
	<?php
	$orders = $this->get_orders();
	echo "<table><thead><tr><td>Order</td><td>Item</td><td>Meta</td><td>Shipped</td></tr></thead>\n<tbody>\n";
	foreach($orders as $order_id => $order) {
		echo "<tr class=\"order_{$order_id}\"><td colspan=\"5\"><hr style=\"height: 4px; background: black\"/></td></tr>\n";
		echo "<tr class=\"order_{$order_id}\">";
		echo "<td colspan=\"5\"><h2>Order #{$order_id}</h2></td>";
		echo "</tr>";
		foreach($order as $order_item_id => $order_item) {
			echo "<tr class=\"order_{$order_id}\"><td colspan=\"5\"><hr/></td></tr>\n";
			echo "<tr class=\"order_{$order_id}\">";
			echo "<td>&nbsp;</td><td>" . $order_item["name"] . "</td>";
			echo "<td>" . $order_item["meta"] . "</td>";
			echo "<td><input type=\"checkbox\" class=\"order_items_{$order_id}\" value=\"{$order_item_id}\" name=\"ship_order_item\"";
			if($order_item["shipped"]){ echo " checked=\"true\""; }
			echo "/>";
			echo "</td>";
			echo "<td><div id=\"result_{$order_item_id}\">";
			echo $order_item["shipped"];
			echo "</div></td>";
			echo "</tr>\n";	
		}
	}
	echo "</tbody></table>\n";
	echo "</form>\n";
?>
</div>
