<?php
/**
 * UAEL Module Base.
 *
 * @package UAEL
 */

namespace HFE\WidgetsManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Module Base
 *
 * @since x.x.x
 */
abstract class Widgets_Loader {

	/**
	 * Reflection
	 *
	 * @var reflection
	 */
	private $reflection;

	/**
	 * Reflection
	 *
	 * @var instances
	 */
	protected static $instances = array();

	/**
	 * Class name to Call
	 *
	 * @since x.x.x
	 */
	public static function class_name() {
		return get_called_class();
	}

	/**
	 * Check if this is a widget.
	 *
	 * @since 1.12.0
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public function is_widget() {
		return true;
	}

	/**
	 * Class instance
	 *
	 * @since x.x.x
	 *
	 * @return static
	 */
	public static function instance() {
		$class_name = static::class_name();
		if ( empty( static::$instances[ $class_name ] ) ) {
			static::$instances[ $class_name ] = new static();
		}

		return static::$instances[ $class_name ];
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->reflection = new \ReflectionClass( $this );
		// Register category.

		add_action( 'elementor/widgets/widgets_registered', array( $this, 'init_widgets' ) );
	}

	/**
	 * Init Widgets
	 *
	 * @since x.x.x
	 */
	public function init_widgets() {

		$widget_manager = \Elementor\Plugin::instance()->widgets_manager;

		foreach ( $this->get_widgets() as $widget ) {
			
			$class_name = $this->reflection->getNamespaceName() . '\Widgets\\' . $widget;

			if ( $this->is_widget() ) {
				$widget_manager->register_widget_type( new $class_name() );
			}
			
		}
	}

	/**
	 * Get Widgets
	 *
	 * @since x.x.x
	 *
	 * @return array
	 */
	public function get_widgets() {
		return array();
	}
}
