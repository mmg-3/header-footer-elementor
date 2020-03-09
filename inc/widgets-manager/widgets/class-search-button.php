<?php
/**
 * Elementor Classes.
 *
 * @package header-footer-elementor
 */
namespace HFE\WidgetsManager\Widgets;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * HFE Search Button.
 *
 * HFE widget for Search Button.
 *
 * @since x.x.x
 */
class Search_Button extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @since x.x.x
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'hfe-search-button';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since x.x.x
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Search', 'header-footer-elementor' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since x.x.x
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fas fa-search';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since x.x.x
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'hfe-widgets' ];
	}

	/**
	 * Register Search Button controls.
	 *
	 * @since x.x.x
	 * @access protected
	 */
	protected function _register_controls() {
		$this->register_general_content_controls();
		$this->register_search_style_controls();
	}
	/**
	 * Register Archive Title General Controls.
	 *
	 * @since x.x.x
	 * @access protected
	 */

	protected function register_general_content_controls() {
		$this->start_controls_section(
			'section_general_fields',
			[
				'label' => __( 'Search Button', 'header-footer-elementor' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __( 'Layout', 'header-footer-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text' => __( 'Text', 'header-footer-elementor' ),
					'icon' => __( 'Icon', 'header-footer-elementor' ),
					'icon_text' => __( 'Text with Icon', 'header-footer-elementor' ),
				],
				// 'prefix_class' => '',
			]
		);

		$this->add_control(
			'placeholder',
			[
				'label' => __( 'Placeholder', 'header-footer-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Search', 'header-footer-elementor' ) . '...',
			]
		);

		$this->add_control(
				'search_icon',
				array(
					'label'            => __( 'Select Icon', 'header-footer-elementor' ),
					'type'             => Controls_Manager::ICONS,
					'default'          => array(
						'value'   => 'fas fa-search',
						'library' => 'fa-solid',
					),
					'condition'        => array(
						'layout' => ['icon','icon_text'], 
					),
					'render_type' => 'template',
				)
			);

		$this->add_control(
			'size',
			[
				'label' => __( 'Size', 'header-footer-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
				'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .hfe-search-form-container' => 'min-height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .hfe-search-submit' => 'min-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .hfe-search-form__input' => 'padding-left: calc({{SIZE}}{{UNIT}} / 1); padding-right: calc({{SIZE}}{{UNIT}} / 1)',
				],
				'condition' => [
					'layout!' => 'icon',
				],
			]
		);
		
		$this->add_control(
			'button_align',
			[
				'label' => __( 'Alignment', 'header-footer-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'header-footer-elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'header-footer-elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'header-footer-elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .hfe-search-button-wrapper' => 'text-align: {{VALUE}}',
				],
				'condition' => [
					'layout' => 'icon',
				],
			]
		);

		$this->add_control(
			'toggle_icon_size',
			[
				'label' => __( 'Size', 'header-footer-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 33,
				],
				'selectors' => [
					'{{WRAPPER}} .hfe-search-icon-toggle i' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'layout' => 'icon',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function register_search_style_controls() {
		$this->start_controls_section(
			'section_input_style',
			[
				'label' => __( 'Input', 'header-footer-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} input[type="search"].elementor-search-form__input',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->start_controls_tabs( 'tabs_input_colors' );

		$this->start_controls_tab(
			'tab_input_normal',
			[
				'label' => __( 'Normal', 'header-footer-elementor' ),
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label' => __( 'Text Color', 'header-footer-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [],
			]
		);

		$this->add_control(
			'input_background_color',
			[
				'label' => __( 'Background Color', 'header-footer-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [],
			]
		);

		$this->add_control(
			'input_border_color',
			[
				'label' => __( 'Border Color', 'header-footer-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'input_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-search-form__container',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_input_focus',
			[
				'label' => __( 'Hover', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'input_text_color_focus',
			[
				'label' => __( 'Text Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:not(.elementor-search-form--skin-full_screen) .elementor-search-form--focus .elementor-search-form__input,
					{{WRAPPER}} .elementor-search-form--focus .elementor-search-form__icon,
					{{WRAPPER}} .elementor-lightbox .dialog-lightbox-close-button:hover,
					{{WRAPPER}}.elementor-search-form--skin-full_screen input[type="search"].elementor-search-form__input:focus' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'input_background_color_focus',
			[
				'label' => __( 'Background Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:not(.elementor-search-form--skin-full_screen) .elementor-search-form--focus .elementor-search-form__container' => 'background-color: {{VALUE}}',
					'{{WRAPPER}}.elementor-search-form--skin-full_screen input[type="search"].elementor-search-form__input:focus' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'input_border_color_focus',
			[
				'label' => __( 'Border Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:not(.elementor-search-form--skin-full_screen) .elementor-search-form--focus .elementor-search-form__container' => 'border-color: {{VALUE}}',
					'{{WRAPPER}}.elementor-search-form--skin-full_screen input[type="search"].elementor-search-form__input:focus' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'input_box_shadow_focus',
				'selector' => '{{WRAPPER}} .elementor-search-form--focus .elementor-search-form__container',
				'fields_options' => [
					'box_shadow_type' => [
						'separator' => 'default',
					],
				],
			]
		);

		$this->end_controls_tab();

	}

	protected function render(){
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'input', [
				'placeholder' => $settings['placeholder'],
				'class' => 'hfe-search-form__input',
				'type' => 'search',
				'name' => 's',
				'title' => __( 'Search', 'header-footer-elementor' ),
				'value' => get_search_query(),
			]
		);
		
		?>
		<form class="hfe-search-button-wrapper" role="search" action="<?php echo home_url(); ?>" method="get">
			<?php if ( 'icon' === $settings['layout'] ) { ?>
			<div class="hfe-search-icon-toggle">
				<i class="fa fa-search" aria-hidden="true"></i>
			</div>
			<?php } else { ?>
			<div class="hfe-search-form-container">
				<?php if ( 'text' === $settings['layout'] ) { ?>
					<div class="hfe-search-text">
						<input <?php echo $this->get_render_attribute_string( 'input' ); ?>>
					</div>
				<?php } else { ?>
					<input <?php echo $this->get_render_attribute_string( 'input' ); ?>>
						<button class="hfe-search-submit" type="submit">
							<i class="fa fa-search" aria-hidden="true"></i>
						</button>
				<?php } ?>
			</div>
		<?php } ?>
		</form>


		<?php
	}






}


