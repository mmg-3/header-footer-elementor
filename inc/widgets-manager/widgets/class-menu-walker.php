<?php
/**
 * HFE Menu Walker
 *
 * @package header-footer-elementor
 */

namespace HFE\WidgetsManager\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Menu_Walker.
 */
class Menu_Walker extends \Walker_Nav_Menu {

	/**
	 * Start element
	 *
	 * @since 1.3.0
	 * @param string $output Output HTML.
	 * @param object $item Individual Menu item.
	 * @param int    $depth Depth.
	 * @param array  $args Arguments array.
	 * @param int    $id Menu ID.
	 * @access public
	 */
	public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$args   = (object) $args;

		$class_names = '';
		$value       = '';
		$rel_xfn     = '';
		$rel_blank   = '';

		$classes = empty( $item->classes ) ? [] : (array) $item->classes;
		$submenu = $args->has_children ? ' hfe-has-submenu' : '';

		if ( 0 === $depth ) {
			array_push( $classes, 'parent' );
		}
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = ' class="' . esc_attr( $class_names ) . $submenu . ' hfe-creative-menu"';

		$output .= $indent . '<li id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';

		$atts           = array();
	
		if ( isset( $item->target ) && '_blank' === $item->target && isset( $item->xfn ) && false === strpos( $item->xfn, 'noopener' ) ) {
			$rel_xfn = ' noopener';
			$atts['rel'] = $rel_xfn;
		}
		if ( isset( $item->target ) && '_blank' === $item->target && isset( $item->xfn ) && empty( $item->xfn ) ) {
			$rel_blank = 'noopener';
			$atts['rel'] = $rel_blank;
		}

		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		$atts = apply_filters( 'hfe_nav_menu_attrs', $atts, $item, $args, $depth );
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		$item_output  = $args->has_children ? '<div class="hfe-has-submenu-container">' : '';
		$item_output .= $args->before;
		$item_output .= '<a' . $attributes;
		if ( 0 === $depth ) {
			$item_output .= ' class = "hfe-menu-item"';
		} else {
			$item_output .= in_array( 'current-menu-item', $item->classes ) ? ' class = "hfe-sub-menu-item hfe-sub-menu-item-active"' : ' class = "hfe-sub-menu-item"';
		}

		$item_output .= '>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		if ( $args->has_children ) {
			$item_output .= "<span class='hfe-menu-toggle sub-arrow hfe-menu-child-";
			$item_output .= $depth;
			$item_output .= "'><i class='fa'></i></span>";
		}
		$item_output .= '</a>';

		$item_output .= $args->after;
		$item_output .= $args->has_children ? '</div>' : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Display element
	 *
	 * @since 1.3.0
	 * @param object $element Individual Menu element.
	 * @param object $children_elements Child Elements.
	 * @param int    $max_depth Maximum Depth.
	 * @param int    $depth Depth.
	 * @param array  $args Arguments array.
	 * @param string $output Output HTML.
	 * @access public
	 */
	function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {

		$id_field = $this->db_fields['id'];

		if ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
		}
		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}

