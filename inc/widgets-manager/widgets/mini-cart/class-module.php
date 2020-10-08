<?php
/**
 * Elementor Classes.
 *
 * @package header-footer-elementor
 */

namespace HFE\WidgetsManager\Widgets\MiniCart;

use HFE\WidgetsManager\Widgets_Loader;
use HFE\WidgetsManager\Widgets\Cart\Widgets\MiniCart;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module.
 */
class Module extends Widgets_Loader {

	/**
	 * Get Widgets.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return [
			'Mini_Cart',
		];
	}

	/**
	 * Constructor.
	 */
	public function __construct() { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		parent::__construct();

		// Refresh the cart fragments.
		if ( class_exists( 'woocommerce' ) ) {
			add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'init_cart' ], 10, 0 );
			add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'wc_refresh_mini_cart_count' ] );
		}
	}

	/**
	 * Initialize the cart.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function init_cart() {
		$has_cart = is_a( WC()->cart, 'WC_Cart' );

		if ( ! $has_cart ) {
			$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
			WC()->session  = new $session_class();
			WC()->session->init();
			WC()->customer = new \WC_Customer( get_current_user_id(), true );
		}
	}

	/**
	 * Cart Fragments.
	 *
	 * Refresh the cart fragments.
	 *
	 * @since 1.5.0
	 * @param array $fragments Array of fragments.
	 * @access public
	 */
	public function wc_refresh_mini_cart_count( $fragments ) {

		$has_cart = is_a( WC()->cart, 'WC_Cart' );
		if ( ! $has_cart ) {
			return $fragments;
		}

		ob_start();

		$cart_type = get_option( 'hfe_cart_widget_type' );

		MiniCart::get_cart_link( $cart_type );

		$fragments['body:not(.elementor-editor-active) a.hfe-cart-container'] = ob_get_clean();

		return $fragments;
	}
}
