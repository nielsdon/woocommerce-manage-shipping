<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */
 
 $url = get_permalink() . "?page=" . $_GET["page"];
?>
<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function(){
			jQuery("input[type='checkbox']").click(function(){
				var url = '';
				var thisCheck = jQuery(this);
				if (thisCheck.is (':checked')) {
				
					url = "<?php echo $url ?>&" + thisCheck.attr('name') + "=" +thisCheck.val();
					//alert(url);
					jQuery.get( url, function( data ) {
					  jQuery( "#result" ).html("Updated order item " +thisCheck.val());
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
	$order_item_id = "";
	$order_item_name = "";
	$order_id = "";
	echo "<form>\n";
	echo "<table><thead><tr><td>Order</td><td>Item</td><td>Meta</td><td>Shipped</td></tr></thead>\n<tbody>\n";
	foreach($orders as $item) {
		if($order_id != $item->ID && $order_id != "") {
			echo "<tr><td colspan=\"5\"><hr style=\"height: 4px; background: black\"/></td></tr>\n";
			echo "<tr>";
			echo "<td>{$order_id}</td>";
			echo "</tr>";
		}
		if($order_item_id != $item->order_item_id && $order_item_id != "") {
			echo "<tr><td colspan=\"5\"><hr/></td></tr>\n";
			echo "<tr>";
			echo "<td>&nbsp;</td><td>{$order_item_name}</td>";
			echo "<td>{$meta}</td>";
			echo "<td><input type=\"checkbox\" value=\"{$order_item_id}\" name=\"ship_order_item\"";
			if($shipped){ echo " checked=\"true\""; }
			echo "/>";
			echo $shipped;
			echo "</td>";
			echo "</tr>\n";	
			$meta = "";
		}
		$meta .= $item->meta_key . " : " . $item->meta_value . "<br/>\n";
		$order_item_id = $item->order_item_id;	
		$order_item_name = $item->order_item_name;
		$shipped = $item->shipped;
		$order_id = $item->ID;	
		/*
		if( $item->order_item_id != $order_item_id || $order_id != $item->ID) {
			if( $order_item_id != "" ) {
				echo "<td>$order_item_name</td>";
				echo "<td>$meta</td>";
				echo "<td><input type=\"checkbox\" value=\"$order_item_id\" name=\"ship_order_item\"";
				if($item->shipped){ echo " checked=\"true\""; }
				echo "/>";
				echo $item->shipped;
				echo "</td>";
				echo "</tr><tr><td colspan=\"5\"><hr/></td></tr>\n";
				$meta = "";
			}
			echo "<tr>";
			if($order_id != $item->ID) {
				$order_id = $item->ID;
				echo "<td colspan=\"5\"><hr style=\"height: 4px; background-color: black\"/></td></tr><tr>";
				echo "<td>$order_id</td>";
			} else {
				echo "<td>&nbsp;</td>";			
			}
			
			$order_item_id = $item->order_item_id;
			$order_item_name = $item->order_item_name;
			
		}	
		if($item->meta_key) {
			$meta .= $item->meta_key . " : " . $item->meta_value . "<br/>";
		}
		*/
	}
	echo "</tbody></table>\n";
	echo "</form>\n";
?>
</div>
