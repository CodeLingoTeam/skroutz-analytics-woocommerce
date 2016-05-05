<?php

/**
 * The tracking functionality of the plugin.
 *
 * @link       www.skroutz.gr
 * @since      1.0.0
 *
 * @package    WC_Skroutz_Analytics_Tracking
 * @subpackage WC_Skroutz_Analytics_Tracking/includes
 */

/**
 * The tracking functionality of the plugin.
 *
 * @package    WC_Skroutz_Analytics_Tracking
 * @subpackage WC_Skroutz_Analytics_Tracking/includes
 * @author     Skroutz <info@skroutz.gr>
 */
class WC_Skroutz_Analytics_Tracking {

	/**
	* The shop account id provided by the admin settings
	* @var string
	*/
	private $shop_account_id;

	/**
	* The current order to be submitted
	* @var string
	*/
	private $order;

	/**
	* Define the core functionality of the plugin.
	*
	* Set the plugin name and the plugin version that can be used throughout the plugin.
	* Load the dependencies, define the locale, and set the hooks for the admin area and
	* the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function __construct( $shop_account_id ) {
		$this->shop_account_id = $shop_account_id;

	  	// Page tracking script
	    add_action( 'wp_enqueue_scripts', array( $this, 'load_analytics_tracking_script' ) );

	    // Ecommerce tracking
	    add_action( 'woocommerce_thankyou', array( $this, 'load_ecommerce_analytics' ) );
	}

	public function load_analytics_tracking_script() {
		wp_register_script(
			'sa_tracking',
		  	plugin_dir_url(dirname(__FILE__)) . 'assets/js/skroutz-analytics-tracking.js',
		  	'',
		  	WC_Skroutz_Analytics::PLUGIN_VERSION
		);

		wp_localize_script(
			'sa_tracking',
			WC_Skroutz_Analytics::PLUGIN_ID,
			array( 'shop_account_id' => $this->shop_account_id )
		);

		wp_enqueue_script( 'sa_tracking' );
	}

 	public function load_ecommerce_analytics( $order_id ) {
  		$this->order = new WC_Order( $order_id );

		add_action( 'wp_print_footer_scripts', array( $this, 'output_ecommerce_analytics_script' ) );
	}

	public function output_ecommerce_analytics_script() {
		$analytics_script = '<script type="text/javascript">';

		$analytics_script .= $this->create_action( 'addOrder', $this->prepare_order_data() );

		foreach ( $this->order->get_items() as $item ) {
			$analytics_script .= $this->create_action( 'addItem', $this->prepare_item_data( $item ) );
		}

		$analytics_script .= '</script>';

		echo $analytics_script;
	}

	/**
	* Builds an Analytics Ecommerce addOrder action.
	*
	* @param array $order The completed order to report.
	* @return string The JavaScript representation of an Analytics Ecommerce addOrder action.
	*/
	private function prepare_order_data() {
		$data = array(
			'order_id' => $this->order->get_order_number(),
			'revenue'  => $this->order->get_total(),
		    'shipping' => $this->order->get_total_shipping(),
		    'tax'      => $this->order->get_total_tax(),
		);

		return json_encode($data);
	}

	/**
	* Builds an Analytics Ecommerce addItem action.
	*
	* @param array $order The completed order to report.
	* @param array $item The purchesed product to report, part of this order.
	* @return string The JavaScript representation of an Analytics Ecommerce addItem action.
	*/
	private function prepare_item_data( $item ) {
		$product = $this->order->get_product_from_item( $item );

		$data = array(
			'order_id'    => $this->order->get_order_number(),
			'product_id'  => $this->items_product_id == 'id' ? $product->id : $product->get_sku(),
			'name'        => esc_js($product->get_title()),
			'price'       => $this->order->get_item_total( $item ),
			'quantity'    => (int)$item['qty'],
		);

		return json_encode($data);
	}

	private function create_action( $action, $data ) {
		return "sa('ecommerce', '$action', '{$data}');";
	}

}