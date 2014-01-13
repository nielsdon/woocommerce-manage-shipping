<?php
/**
 * Woocommerce Manage Shipping
 *
 * @package   woocommerce-manage-shipping
 * @author    Niels Donninger <niels@donninger.nl>
 * @license   GPL-2.0+
 * @link      http://donninger.nl
 * @copyright 2013 Donninger Consultancy
 */

/**

 */
class Woocommerce_Manage_Shipping_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * @TODO:
		 *
		 * - Rename "Plugin_Name" to the name of your initial plugin class
		 *
		 */
		$plugin = Woocommerce_Manage_Shipping::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		//add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_submenu_page' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		//add_action( '@TODO', array( $this, 'action_method_name' ) );
		//add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Woocommerce_Manage_Shipping::VERSION );

		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Woocommerce_Manage_Shipping::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Manage shipping', $this->plugin_slug ),
			__( 'Manage shipping', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}
	
	public function add_plugin_submenu_page() {
		/*
		 * Add a settings page for this plugin to the Woocommerce menu.
		 *
		 */
		$this->plugin_screen_hook_suffix = add_submenu_page(
			'woocommerce',
			__( 'Manage shipping', $this->plugin_slug ),
			__( 'Manage shipping', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		if($_GET["ship_order_item"]) {
			if(!$_GET["undo"]) { //box was checked
				$this->ship_order_item($_GET["ship_order_item"]);
			} else { //box was unchecked: undo shipping
				$this->undo_ship_order_item($_GET["ship_order_item"]);				
			}
		} elseif($_GET["complete_order"]) {
			$this->complete_order($_GET["complete_order"]);
		} else {
			include_once( 'views/admin.php' );
		}
	}

	private function ship_order_item($order_item) {
		woocommerce_add_order_item_meta($order_item,"shipped",date("r"));
		/*
		global $wpdb;
		$prefix = $wpdb->prefix;
		
		//echo "Shipping $order_item";
		$query = "INSERT INTO {$prefix}woocommerce_order_itemmeta (order_item_id, meta_key, meta_value) VALUES ('{$order_item}','shipped',NOW())";
		$wpdb->query($query);
		*/
	}
	
	private function undo_ship_order_item($order_item) {
		woocommerce_delete_order_item_meta($order_item,"shipped");
/*
		global $wpdb;
		$prefix = $wpdb->prefix;
		
		//echo "Shipping $order_item";
		$query = "DELETE FROM {$prefix}woocommerce_order_itemmeta WHERE order_item_id='{$order_item}' AND meta_key='shipped'";
		$wpdb->query($query);
		*/
	}
	
	/*
	* sets order status to 'completed'
	*/
	private function complete_order($order_id) {
		wp_set_post_terms($order_id, "completed", "shop_order_status");
	}
	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}
	
	public function get_orders() {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$query = "SELECT *, q.meta_value AS quantity, s.meta_value AS shipped
					FROM {$prefix}posts o
					LEFT JOIN {$prefix}woocommerce_order_items i ON (i.order_id=o.ID)
					LEFT JOIN {$prefix}woocommerce_order_itemmeta s ON (s.order_item_id=i.order_item_id AND s.meta_key LIKE 'shipped')
					LEFT JOIN {$prefix}woocommerce_order_itemmeta q ON (q.order_item_id=i.order_item_id AND q.meta_key LIKE '_qty')

					LEFT JOIN {$prefix}woocommerce_order_itemmeta m ON (m.order_item_id=i.order_item_id)

					WHERE o.post_type='shop_order' 
					AND i.order_item_type='line_item'
					AND m.meta_key NOT LIKE '_qty' AND m.meta_key NOT LIKE '_tax_class' AND m.meta_key NOT LIKE '_product_id' AND m.meta_key NOT LIKE '_variation_id' AND m.meta_key NOT LIKE '_line_subtotal' AND m.meta_key NOT LIKE '_line_total' AND m.meta_key NOT LIKE '_line_tax' AND m.meta_key NOT LIKE '_line_subtotal_tax' AND m.meta_key NOT LIKE 'purchased' AND m.meta_key NOT LIKE 'shipped' 
					AND o.ID IN (
						SELECT o.ID
						FROM {$prefix}posts o
						JOIN {$prefix}term_relationships r ON (r.object_id=o.ID)
						JOIN {$prefix}term_taxonomy x ON (r.term_taxonomy_id=x.term_taxonomy_id)
						JOIN {$prefix}terms t ON (x.term_id=t.term_id)
					  WHERE
					  	t.slug='processing')
					 ORDER BY o.ID DESC, i.order_item_id
					 ";
		$orders = array();
		foreach($wpdb->get_results($query) as $item) {
			$orders[$item->ID][$item->order_item_id]["meta"] .= $item->meta_key . " : " . $item->meta_value . "<br/>\n";
			$orders[$item->ID][$item->order_item_id]["shipped"] = $item->shipped;
			$orders[$item->ID][$item->order_item_id]["name"] = $item->order_item_name;
			$orders[$item->ID][$item->order_item_id]["quantity"] = $item->quantity;
		}
		//return $wpdb->get_results($query);
		return $orders;

	}

}
