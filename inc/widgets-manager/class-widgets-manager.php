<?php
/**
 * Widgets manager for Header Footer Elementor.
 *
 * @package     HFE
 * @author      HFE
 * @copyright   Copyright (c) 2018, HFE
 * @link        http://brainstormforce.com/
 * @since       HFE x.x.x
 */

namespace HFE\WidgetsManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module_Manager.
 */
class Widgets_Manager {
	/**
	 * Member Variable
	 *
	 * @var modules.
	 */
	private $_modules = []; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Register Modules.
	 *
	 * @since x.x.x
	 */
	public function register_modules() {
		$all_modules = $this->get_widget_list();

		foreach ( $all_modules as $module_name ) {
			$class_name = str_replace( '-', ' ', $module_name );

			$class_name = str_replace( ' ', '', ucwords( $class_name ) );

			$class_name = __NAMESPACE__ . '\\Widgets\\' . $class_name . '\Module';

			$this->modules[ $module_name ] = $class_name::instance();
		}
	}

	/**
	 * Get Modules.
	 *
	 * @param string $module_name Module Name.
	 *
	 * @since x.x.x
	 *
	 * @return Module_Base|Module_Base[]
	 */
	public function get_modules( $module_name = null ) {
		if ( $module_name ) {
			if ( isset( $this->modules[ $module_name ] ) ) {
				return $this->modules[ $module_name ];
			}
			return null;
		}

		return $this->_modules;
	}

	/**
	 * Required Files.
	 *
	 * @since x.x.x
	 */
	private function require_files() {
		require HFE_DIR . 'inc/widgets-manager/class-widgets-loader.php';

		$this->include_widgets_files();
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		//Register HFE Category.
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_widget_category' ] );

		//Widgets files.
		$this->require_files();
		$this->register_modules();
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since x.x.x
	 * @access public
	 */
	public function include_widgets_files() {
		$js_files    = $this->get_widget_script();
		$widget_list = $this->get_widget_list();

		if ( ! empty( $widget_list ) ) {
			foreach ( $widget_list as $handle => $data ) {
				require_once HFE_DIR . 'inc/widgets-manager/widgets/' . $data . '/class-module.php';
				require_once HFE_DIR . 'inc/widgets-manager/widgets/' . $data . '/widgets/class-' . $data . '.php';
			}
			require_once HFE_DIR . 'inc/widgets-manager/widgets/copyright/widgets/class-copyright-shortcode.php';
			require_once HFE_DIR . 'inc/widgets-manager/widgets/navigation-menu/widgets/class-menu-walker.php';
		}

		// Enqueue the widgets script.
		if ( ! empty( $js_files ) ) {
			foreach ( $js_files as $handle => $data ) {
				wp_register_script( $handle, HFE_URL . $data['path'], $data['dep'], HFE_VER, $data['in_footer'] );
			}
		}

		// Enqueue the widgets style.
		wp_enqueue_style( 'hfe-widgets-style', HFE_URL . 'inc/widgets-css/frontend.css', [], HFE_VER );
	}

	/**
	 * Returns Script array.
	 *
	 * @return array()
	 * @since x.x.x
	 */
	public static function get_widget_script() {
		$js_files = [
			'hfe-frontend-js' => [
				'path'      => 'inc/js/frontend.js',
				'dep'       => [ 'jquery' ],
				'in_footer' => true,
			],
		];

		return $js_files;
	}

	/**
	 * Returns Script array.
	 *
	 * @return array()
	 * @since x.x.x
	 */
	public static function get_widget_list() {
		$widget_list = [
			'mini-cart',
			'copyright',
			'navigation-menu',
			'page-title',
			'retina',
			'search-button',
			'site-logo',
			'site-tagline',
			'site-title',
		];

		return $widget_list;
	}

	/**
	 * Register Category
	 *
	 * @since x.x.x
	 * @param object $this_cat class.
	 */
	public function register_widget_category( $this_cat ) {
		$category = __( 'Header, Footer & Blocks', 'header-footer-elementor' );

		$this_cat->add_category(
			'hfe-widgets',
			[
				'title' => $category,
				'icon'  => 'eicon-font',
			]
		);

		return $this_cat;
	}
}
