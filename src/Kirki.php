<?php
/**
 * Register Kirki fields, sections and panels through configuration.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @author    Craig Simpson <craig@craigsimpson.scot>
 * @copyright 2018, D2 Themes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Register Kirki fields, sections and panels through configuration.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * $core_kirki = [
 *     Kirki::CONFIG   => [
 *         'id'          => 'theme_name',
 *         'capability'  => 'edit_theme_options',
 *         'option_type' => 'theme_mod',
 *     ],
 *     Kirki::SECTIONS => [
 *         [
 *             Kirki::ID    => 'general',
 *             Kirki::TITLE => __( 'General Settings', 'textdomain' ),
 *         ],
 *     ],
 *     Kirki::FIELDS => [
 *         [
 *             'type'     => 'text',
 *             'settings' => 'license',
 *             'section'  => 'general',
 *             'label'    => __( 'License key', 'textdomain' ),
 *         ],
 *     ],
 * ];
 *
 * return [
 *     Kirki::class => $core_kirki,
 * ];
 * ```
 */
class Kirki extends Component {

	const ALPHA           = 'alpha';
	const CAPABILITY      = 'capability';
	const CHOICES         = 'choices';
	const CHOICE          = 'choice';
	const CONFIG          = 'config';
	const DEFAULTS        = 'default';
	const DESCRIPTION     = 'description';
	const ELEMENT         = 'element';
	const EXCLUDE         = 'exclude';
	const FIELDS          = 'fields';
	const FONTS           = 'fonts';
	const GOOGLE          = 'google';
	const ID              = 'id';
	const LABEL           = 'label';
	const LOADER          = 'loader';
	const MEDIA_QUERY     = 'media_query';
	const METHOD          = 'method';
	const MODE            = 'mode';
	const OUTPUT          = 'output';
	const PANEL           = 'panel';
	const PANELS          = 'panels';
	const PARTIAL_REFRESH = 'partial_refresh';
	const PATTERN_REPLACE = 'pattern_replace';
	const PREFIX          = 'prefix';
	const PRIORITY        = 'priority';
	const PROPERTY        = 'property';
	const REMOVE          = 'remove';
	const SCRIPTS         = 'scripts';
	const SECTION         = 'section';
	const SECTIONS        = 'sections';
	const SETTINGS        = 'settings';
	const STANDARD        = 'standard';
	const STYLES          = 'styles';
	const SUFFIX          = 'suffix';
	const TITLE           = 'title';
	const TOOLTIP         = 'tooltip';
	const TRANSPORT       = 'transport';
	const TYPE            = 'type';
	const UNITS           = 'units';
	const VALUE_PATTERN   = 'value_pattern';

	/**
	 * Initialize class.
	 *
	 * @since 3.3.0
	 *
	 * @return void
	 */
	public function init() {
		if ( array_key_exists( self::METHOD, $this->config ) ) {
			add_filter( 'kirki/dynamic_css/method', [ $this, 'write_to_file' ] );
		}

		if ( array_key_exists( self::LOADER, $this->config ) ) {
			add_filter( 'kirki_config', [ $this, 'remove_loader' ] );
		}

		if ( array_key_exists( self::CONFIG, $this->config ) ) {
			add_action( 'after_setup_theme', [ $this, 'add_config' ] );
		}

		if ( array_key_exists( self::REMOVE, $this->config ) ) {
			add_action( 'customize_register', [ $this, 'remove_defaults' ], 99 );
		}

		if ( array_key_exists( self::STYLES, $this->config ) ) {
			add_action( 'customize_controls_print_styles', [ $this, 'styles' ], 99 );
		}

		if ( array_key_exists( self::SCRIPTS, $this->config ) ) {
			add_action( 'customize_controls_print_scripts', [ $this, 'scripts' ], 999 );
		}

		if ( array_key_exists( self::PANELS, $this->config ) ) {
			$this->add_panels( $this->config[ self::PANELS ] );
		}

		if ( array_key_exists( self::SECTIONS, $this->config ) ) {
			$this->add_sections( $this->config[ self::SECTIONS ] );
		}

		if ( array_key_exists( self::FIELDS, $this->config ) ) {
			$this->add_fields( $this->config[ self::FIELDS ] );
		}
	}

	/**
	 * Sets the CSS output method.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function write_to_file() {
		return $this->config[ self::METHOD ];
	}

	/**
	 * Removes Kirki loader image.
	 *
	 * @since  1.2.0
	 *
	 * @param  $config the configuration array
	 *
	 * @return array
	 */
	public function remove_loader( $config ) {
		return wp_parse_args( $this->config[ self::LOADER ], $config );
	}

	/**
	 * Adds the theme's Kirki config.
	 *
	 * @since  1.2.0
	 *
	 * @link   https://aristath.github.io/kirki/docs/getting-started/config.html
	 *
	 * @return void
	 */
	public function add_config() {
		\Kirki::add_config( $this->config[ self::CONFIG ][ self::ID ], $this->config[ self::CONFIG ] );
	}

	/**
	 * Removes default Customizer settings.
	 *
	 * @since  1.2.0
	 *
	 * @param  object $wp_customize Global customizer object.
	 *
	 * @return void
	 */
	public function remove_defaults( $wp_customize ) {
		foreach ( $this->config[ self::REMOVE ] as $sub_config => $args ) {
			$function = 'remove_' . $args[0];
			$wp_customize->$function( $args[1] );
		}
	}

	/**
	 * Adds custom inline styles to Customizer screen.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function styles() {
		echo $this->config[ self::STYLES ];
	}

	/**
	 * Adds custom inline scripts to Customizer screen.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function scripts() {
		echo $this->config[ self::SCRIPTS ];
	}

	/**
	 * Adds Kirki panels.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Kirki panels config.
	 *
	 * @return void
	 */
	protected function add_panels( $config ) {
		foreach ( $config as $panel => $args ) {
			\Kirki::add_panel( $args['id'], $args );
		}
	}

	/**
	 * Adds Kirki sections.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Kirki sections config.
	 *
	 * @return void
	 */
	protected function add_sections( $config ) {
		foreach ( $config as $section => $args ) {
			\Kirki::add_section( $args['id'], $args );
		}
	}

	/**
	 * Adds Kirki fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Kirki fields config.
	 *
	 * @return void
	 */
	protected function add_fields( $config ) {
		foreach ( $config as $field => $args ) {
			\Kirki::add_field( $this->config[ self::CONFIG ][ self::ID ], $args );
		}
	}
}
