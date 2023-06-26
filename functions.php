<?php
/**
 * Twenty Twenty functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

/**
 * Table of Contents:
 * Theme Support
 * Required Files
 * Register Styles
 * Register Scripts
 * Register Menus
 * Custom Logo
 * WP Body Open
 * Register Sidebars
 * Enqueue Block Editor Assets
 * Enqueue Classic Editor Styles
 * Block Editor Settings
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function venquis_theme_support() {

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Custom background color.
	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'f5efe0',
		)
	);

	// Set content-width.
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 580;
	}

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// Set post thumbnail size.
	set_post_thumbnail_size( 1200, 9999 );

	// Add custom image size used in Cover Template.
	add_image_size( 'venquis-fullscreen', 1980, 9999 );

	// Custom logo.
	$logo_width  = 120;
	$logo_height = 90;

	// If the retina setting is active, double the recommended width and height.
	if ( get_theme_mod( 'retina_logo', false ) ) {
		$logo_width  = floor( $logo_width * 2 );
		$logo_height = floor( $logo_height * 2 );
	}

	add_theme_support(
		'custom-logo',
		array(
			'height'      => $logo_height,
			'width'       => $logo_width,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
			'navigation-widgets',
		)
	);

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Twenty Twenty, use a find and replace
	 * to change 'venquis' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'venquis' );

	// Add support for full and wide align images.
	add_theme_support( 'align-wide' );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	/*
	 * Adds starter content to highlight the theme on fresh sites.
	 * This is done conditionally to avoid loading the starter content on every
	 * page load, as it is a one-off operation only needed once in the customizer.
	 */
	if ( is_customize_preview() ) {
		require get_template_directory() . '/inc/starter-content.php';
		add_theme_support( 'starter-content', venquis_get_starter_content() );
	}

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	 * Adds `async` and `defer` support for scripts registered or enqueued
	 * by the theme.
	 */
	$loader = new venquis_Script_Loader();
	add_filter( 'script_loader_tag', array( $loader, 'filter_script_loader_tag' ), 10, 2 );

}

add_action( 'after_setup_theme', 'venquis_theme_support' );

/**
 * REQUIRED FILES
 * Include required files.
 */
require get_template_directory() . '/inc/template-tags.php';

// Handle SVG icons.
require get_template_directory() . '/classes/class-venquis-svg-icons.php';
require get_template_directory() . '/inc/svg-icons.php';

// Handle Customizer settings.
require get_template_directory() . '/classes/class-venquis-customize.php';

// Require Separator Control class.
require get_template_directory() . '/classes/class-venquis-separator-control.php';

// Custom comment walker.
require get_template_directory() . '/classes/class-venquis-walker-comment.php';

// Custom page walker.
require get_template_directory() . '/classes/class-venquis-walker-page.php';

// Custom script loader class.
require get_template_directory() . '/classes/class-venquis-script-loader.php';

// Non-latin language handling.
require get_template_directory() . '/classes/class-venquis-non-latin-languages.php';

// Custom CSS.
require get_template_directory() . '/inc/custom-css.php';

// Block Patterns.
require get_template_directory() . '/inc/block-patterns.php';

require get_template_directory() . '/bullhorn.php';

/**
 * Register and Enqueue Styles.
 */
function venquis_register_styles() {

	$theme_version = date('Ymdhis');

	wp_enqueue_style( 'venquis-style', get_stylesheet_uri(), array(), $theme_version );
	//wp_style_add_data( 'venquis-style', 'rtl', 'replace' );

	// Add output of Customizer settings as inline style.
	//wp_add_inline_style( 'venquis-style', venquis_get_customizer_css( 'front-end' ) );

	// Add print CSS.
	//wp_enqueue_style( 'venquis-print-style', get_template_directory_uri() . '/print.css', null, $theme_version, 'print' );
	wp_enqueue_style( 'venquis-bootstrap-style', get_template_directory_uri() . '/assets/css/bootstrap.min.css', null, $theme_version);
	wp_enqueue_style( 'venquis-bootstrap-glyphicons-style', get_template_directory_uri() . '/assets/css/bootstrap-glyphicons.min.css', null, $theme_version );
	wp_enqueue_style( 'venquis-slick-style', get_template_directory_uri() . '/assets/css/slick.css', null, $theme_version);
	wp_enqueue_style( 'venquis-slick-ww-style', get_template_directory_uri() . '/assets/css/slick-theme.css', null, $theme_version);
	wp_enqueue_style( 'venquis-swiper-style', get_template_directory_uri() . '/assets/css/swiper.css', null, $theme_version);
	wp_enqueue_style( 'venquis-buttons-style', get_template_directory_uri() . '/assets/css/buttons.css', null, $theme_version);
	wp_enqueue_style( 'venquis-responsive-style', get_template_directory_uri() . '/assets/css/responsive.css', null, $theme_version);
	wp_enqueue_style( 'venquis-intlTelInput', get_template_directory_uri() . '/assets/css/intlTelInput.css', null, $theme_version);
}

add_action( 'wp_enqueue_scripts', 'venquis_register_styles' );

/**
 * Register and Enqueue Scripts.
 */
function venquis_register_scripts() {

	$theme_version = date('Ymdhis');
	wp_enqueue_script( 'venquis-jquery-js-mini', get_template_directory_uri() . '/assets/js/jquery.min.js', array(), $theme_version, true );
	wp_enqueue_script( 'venquis-bootstrap-js-minified', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array(), $theme_version, true );
	wp_enqueue_script( 'venquis-slick-js', get_template_directory_uri() . '/assets/js/slick.min.js', array(), $theme_version, true );
	wp_enqueue_script( 'venquis-swiper-js', get_template_directory_uri() . '/assets/js/swiper.min.js', array(), $theme_version, true );
	wp_enqueue_script( 'venquis-main-js', get_template_directory_uri() . '/assets/js/venquis.js', array(), $theme_version, true );
	wp_enqueue_script( 'venquis-intlTelInput', get_template_directory_uri() . '/assets/js/intlTelInput.js', array(), $theme_version, true );
}

add_action( 'wp_enqueue_scripts', 'venquis_register_scripts' );

/**
 * Fix skip link focus in IE11.
 *
 * This does not enqueue the script because it is tiny and because it is only for IE11,
 * thus it does not warrant having an entire dedicated blocking script being loaded.
 *
 * @link https://git.io/vWdr2
 */
function venquis_skip_link_focus_fix() {
	// The following is minified via `terser --compress --mangle -- assets/js/skip-link-focus-fix.js`.
	?>
<script>
/(trident|msie)/i.test(navigator.userAgent) && document.getElementById && window.addEventListener && window
    .addEventListener("hashchange", function() {
        var t, e = location.hash.substring(1);
        /^[A-z0-9_-]+$/.test(e) && (t = document.getElementById(e)) && (/^(?:a|select|input|button|textarea)$/i
            .test(t.tagName) || (t.tabIndex = -1), t.focus())
    }, !1);
</script>
<?php
}
add_action( 'wp_print_footer_scripts', 'venquis_skip_link_focus_fix' );

/** Enqueue non-latin language styles
 *
 * @since Twenty Twenty 1.0
 *
 * @return void
 */
function venquis_non_latin_languages() {
	$custom_css = venquis_Non_Latin_Languages::get_non_latin_css( 'front-end' );

	if ( $custom_css ) {
		wp_add_inline_style( 'venquis-style', $custom_css );
	}
}

add_action( 'wp_enqueue_scripts', 'venquis_non_latin_languages' );

/**
 * Register navigation menus uses wp_nav_menu in five places.
 */
function venquis_menus() {

	$locations = array(
		'primary'  => __( 'Desktop Horizontal Menu', 'venquis' ),
		'expanded' => __( 'Desktop Expanded Menu', 'venquis' ),
		'mobile'   => __( 'Mobile Menu', 'venquis' ),
		'footer'   => __( 'Footer Menu', 'venquis' ),
		'social'   => __( 'Social Menu', 'venquis' ),
	);

	register_nav_menus( $locations );
}

add_action( 'init', 'venquis_menus' );

/**
 * Get the information about the logo.
 *
 * @param string $html The HTML output from get_custom_logo (core function).
 * @return string
 */
function venquis_get_custom_logo( $html ) {

	$logo_id = get_theme_mod( 'custom_logo' );

	if ( ! $logo_id ) {
		return $html;
	}

	$logo = wp_get_attachment_image_src( $logo_id, 'full' );

	if ( $logo ) {
		// For clarity.
		$logo_width  = esc_attr( $logo[1] );
		$logo_height = esc_attr( $logo[2] );

		// If the retina logo setting is active, reduce the width/height by half.
		if ( get_theme_mod( 'retina_logo', false ) ) {
			$logo_width  = floor( $logo_width / 2 );
			$logo_height = floor( $logo_height / 2 );

			$search = array(
				'/width=\"\d+\"/iU',
				'/height=\"\d+\"/iU',
			);

			$replace = array(
				"width=\"{$logo_width}\"",
				"height=\"{$logo_height}\"",
			);

			// Add a style attribute with the height, or append the height to the style attribute if the style attribute already exists.
			if ( strpos( $html, ' style=' ) === false ) {
				$search[]  = '/(src=)/';
				$replace[] = "style=\"height: {$logo_height}px;\" src=";
			} else {
				$search[]  = '/(style="[^"]*)/';
				$replace[] = "$1 height: {$logo_height}px;";
			}

			$html = preg_replace( $search, $replace, $html );

		}
	}

	return $html;

}

add_filter( 'get_custom_logo', 'venquis_get_custom_logo' );

if ( ! function_exists( 'wp_body_open' ) ) {

	/**
	 * Shim for wp_body_open, ensuring backward compatibility with versions of WordPress older than 5.2.
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

/**
 * Include a skip to content link at the top of the page so that users can bypass the menu.
 */
function venquis_skip_link() {
	echo '<a class="skip-link screen-reader-text" href="#site-content">' . __( 'Skip to the content', 'venquis' ) . '</a>';
}

add_action( 'wp_body_open', 'venquis_skip_link', 5 );

/**
 * Register widget areas.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function venquis_sidebar_registration() {

	// Arguments used in all register_sidebar() calls.
	$shared_args = array(
		'before_title'  => '<h2 class="widget-title subheading heading-size-3">',
		'after_title'   => '</h2>',
		'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
		'after_widget'  => '</div></div>',
	);

	// Footer #1.
	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name'        => __( 'Footer #1', 'venquis' ),
				'id'          => 'sidebar-1',
				'description' => __( 'Widgets in this area will be displayed in the first column in the footer.', 'venquis' ),
			)
		)
	);

	// Footer #2.
	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name'        => __( 'Footer #2', 'venquis' ),
				'id'          => 'sidebar-2',
				'description' => __( 'Widgets in this area will be displayed in the second column in the footer.', 'venquis' ),
			)
		)
	);

}

add_action( 'widgets_init', 'venquis_sidebar_registration' );

/**
 * Enqueue supplemental block editor styles.
 */
function venquis_block_editor_styles() {

	// Enqueue the editor styles.
	wp_enqueue_style( 'venquis-block-editor-styles', get_theme_file_uri( '/assets/css/editor-style-block.css' ), array(), wp_get_theme()->get( 'Version' ), 'all' );
	wp_style_add_data( 'venquis-block-editor-styles', 'rtl', 'replace' );

	// Add inline style from the Customizer.
	wp_add_inline_style( 'venquis-block-editor-styles', venquis_get_customizer_css( 'block-editor' ) );

	// Add inline style for non-latin fonts.
	wp_add_inline_style( 'venquis-block-editor-styles', venquis_Non_Latin_Languages::get_non_latin_css( 'block-editor' ) );

	// Enqueue the editor script.
	wp_enqueue_script( 'venquis-block-editor-script', get_theme_file_uri( '/assets/js/editor-script-block.js' ), array( 'wp-blocks', 'wp-dom' ), wp_get_theme()->get( 'Version' ), true );
}

add_action( 'enqueue_block_editor_assets', 'venquis_block_editor_styles', 1, 1 );

/**
 * Enqueue classic editor styles.
 */
function venquis_classic_editor_styles() {

	$classic_editor_styles = array(
		'/assets/css/editor-style-classic.css',
	);

	add_editor_style( $classic_editor_styles );

}

add_action( 'init', 'venquis_classic_editor_styles' );

/**
 * Output Customizer settings in the classic editor.
 * Adds styles to the head of the TinyMCE iframe. Kudos to @Otto42 for the original solution.
 *
 * @param array $mce_init TinyMCE styles.
 * @return array TinyMCE styles.
 */
function venquis_add_classic_editor_customizer_styles( $mce_init ) {

	$styles = venquis_get_customizer_css( 'classic-editor' );

	if ( ! isset( $mce_init['content_style'] ) ) {
		$mce_init['content_style'] = $styles . ' ';
	} else {
		$mce_init['content_style'] .= ' ' . $styles . ' ';
	}

	return $mce_init;

}

add_filter( 'tiny_mce_before_init', 'venquis_add_classic_editor_customizer_styles' );

/**
 * Output non-latin font styles in the classic editor.
 * Adds styles to the head of the TinyMCE iframe. Kudos to @Otto42 for the original solution.
 *
 * @param array $mce_init TinyMCE styles.
 * @return array TinyMCE styles.
 */
function venquis_add_classic_editor_non_latin_styles( $mce_init ) {

	$styles = venquis_Non_Latin_Languages::get_non_latin_css( 'classic-editor' );

	// Return if there are no styles to add.
	if ( ! $styles ) {
		return $mce_init;
	}

	if ( ! isset( $mce_init['content_style'] ) ) {
		$mce_init['content_style'] = $styles . ' ';
	} else {
		$mce_init['content_style'] .= ' ' . $styles . ' ';
	}

	return $mce_init;

}

add_filter( 'tiny_mce_before_init', 'venquis_add_classic_editor_non_latin_styles' );

/**
 * Block Editor Settings.
 * Add custom colors and font sizes to the block editor.
 */
function venquis_block_editor_settings() {

	// Block Editor Palette.
	$editor_color_palette = array(
		array(
			'name'  => __( 'Accent Color', 'venquis' ),
			'slug'  => 'accent',
			'color' => venquis_get_color_for_area( 'content', 'accent' ),
		),
		array(
			'name'  => _x( 'Primary', 'color', 'venquis' ),
			'slug'  => 'primary',
			'color' => venquis_get_color_for_area( 'content', 'text' ),
		),
		array(
			'name'  => _x( 'Secondary', 'color', 'venquis' ),
			'slug'  => 'secondary',
			'color' => venquis_get_color_for_area( 'content', 'secondary' ),
		),
		array(
			'name'  => __( 'Subtle Background', 'venquis' ),
			'slug'  => 'subtle-background',
			'color' => venquis_get_color_for_area( 'content', 'borders' ),
		),
	);

	// Add the background option.
	$background_color = get_theme_mod( 'background_color' );
	if ( ! $background_color ) {
		$background_color_arr = get_theme_support( 'custom-background' );
		$background_color     = $background_color_arr[0]['default-color'];
	}
	$editor_color_palette[] = array(
		'name'  => __( 'Background Color', 'venquis' ),
		'slug'  => 'background',
		'color' => '#' . $background_color,
	);

	// If we have accent colors, add them to the block editor palette.
	if ( $editor_color_palette ) {
		add_theme_support( 'editor-color-palette', $editor_color_palette );
	}

	// Block Editor Font Sizes.
	add_theme_support(
		'editor-font-sizes',
		array(
			array(
				'name'      => _x( 'Small', 'Name of the small font size in the block editor', 'venquis' ),
				'shortName' => _x( 'S', 'Short name of the small font size in the block editor.', 'venquis' ),
				'size'      => 18,
				'slug'      => 'small',
			),
			array(
				'name'      => _x( 'Regular', 'Name of the regular font size in the block editor', 'venquis' ),
				'shortName' => _x( 'M', 'Short name of the regular font size in the block editor.', 'venquis' ),
				'size'      => 21,
				'slug'      => 'normal',
			),
			array(
				'name'      => _x( 'Large', 'Name of the large font size in the block editor', 'venquis' ),
				'shortName' => _x( 'L', 'Short name of the large font size in the block editor.', 'venquis' ),
				'size'      => 26.25,
				'slug'      => 'large',
			),
			array(
				'name'      => _x( 'Larger', 'Name of the larger font size in the block editor', 'venquis' ),
				'shortName' => _x( 'XL', 'Short name of the larger font size in the block editor.', 'venquis' ),
				'size'      => 32,
				'slug'      => 'larger',
			),
		)
	);

	add_theme_support( 'editor-styles' );

	// If we have a dark background color then add support for dark editor style.
	// We can determine if the background color is dark by checking if the text-color is white.
	if ( '#ffffff' === strtolower( venquis_get_color_for_area( 'content', 'text' ) ) ) {
		add_theme_support( 'dark-editor-style' );
	}

}

add_action( 'after_setup_theme', 'venquis_block_editor_settings' );

/**
 * Overwrite default more tag with styling and screen reader markup.
 *
 * @param string $html The default output HTML for the more tag.
 * @return string
 */
function venquis_read_more_tag( $html ) {
	return preg_replace( '/<a(.*)>(.*)<\/a>/iU', sprintf( '<div class="read-more-button-wrap"><a$1><span class="faux-button">$2</span> <span class="screen-reader-text">"%1$s"</span></a></div>', get_the_title( get_the_ID() ) ), $html );
}

add_filter( 'the_content_more_link', 'venquis_read_more_tag' );

/**
 * Enqueues scripts for customizer controls & settings.
 *
 * @since Twenty Twenty 1.0
 *
 * @return void
 */
function venquis_customize_controls_enqueue_scripts() {
	$theme_version = wp_get_theme()->get( 'Version' );

	// Add main customizer js file.
	wp_enqueue_script( 'venquis-customize', get_template_directory_uri() . '/assets/js/customize.js', array( 'jquery' ), $theme_version, false );

	// Add script for color calculations.
	wp_enqueue_script( 'venquis-color-calculations', get_template_directory_uri() . '/assets/js/color-calculations.js', array( 'wp-color-picker' ), $theme_version, false );

	// Add script for controls.
	wp_enqueue_script( 'venquis-customize-controls', get_template_directory_uri() . '/assets/js/customize-controls.js', array( 'venquis-color-calculations', 'customize-controls', 'underscore', 'jquery' ), $theme_version, false );
	wp_localize_script( 'venquis-customize-controls', 'venquisBgColors', venquis_get_customizer_color_vars() );
}

add_action( 'customize_controls_enqueue_scripts', 'venquis_customize_controls_enqueue_scripts' );

/**
 * Enqueue scripts for the customizer preview.
 *
 * @since Twenty Twenty 1.0
 *
 * @return void
 */
function venquis_customize_preview_init() {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_script( 'venquis-customize-preview', get_theme_file_uri( '/assets/js/customize-preview.js' ), array( 'customize-preview', 'customize-selective-refresh', 'jquery' ), $theme_version, true );
	wp_localize_script( 'venquis-customize-preview', 'venquisBgColors', venquis_get_customizer_color_vars() );
	wp_localize_script( 'venquis-customize-preview', 'venquisPreviewEls', venquis_get_elements_array() );

	wp_add_inline_script(
		'venquis-customize-preview',
		sprintf(
			'wp.customize.selectiveRefresh.partialConstructor[ %1$s ].prototype.attrs = %2$s;',
			wp_json_encode( 'cover_opacity' ),
			wp_json_encode( venquis_customize_opacity_range() )
		)
	);
}

add_action( 'customize_preview_init', 'venquis_customize_preview_init' );

/**
 * Get accessible color for an area.
 *
 * @since Twenty Twenty 1.0
 *
 * @param string $area The area we want to get the colors for.
 * @param string $context Can be 'text' or 'accent'.
 * @return string Returns a HEX color.
 */
function venquis_get_color_for_area( $area = 'content', $context = 'text' ) {

	// Get the value from the theme-mod.
	$settings = get_theme_mod(
		'accent_accessible_colors',
		array(
			'content'       => array(
				'text'      => '#000000',
				'accent'    => '#cd2653',
				'secondary' => '#6d6d6d',
				'borders'   => '#dcd7ca',
			),
			'header-footer' => array(
				'text'      => '#000000',
				'accent'    => '#cd2653',
				'secondary' => '#6d6d6d',
				'borders'   => '#dcd7ca',
			),
		)
	);

	// If we have a value return it.
	if ( isset( $settings[ $area ] ) && isset( $settings[ $area ][ $context ] ) ) {
		return $settings[ $area ][ $context ];
	}

	// Return false if the option doesn't exist.
	return false;
}

/**
 * Returns an array of variables for the customizer preview.
 *
 * @since Twenty Twenty 1.0
 *
 * @return array
 */
function venquis_get_customizer_color_vars() {
	$colors = array(
		'content'       => array(
			'setting' => 'background_color',
		),
		'header-footer' => array(
			'setting' => 'header_footer_background_color',
		),
	);
	return $colors;
}

/**
 * Get an array of elements.
 *
 * @since Twenty Twenty 1.0
 *
 * @return array
 */
function venquis_get_elements_array() {

	// The array is formatted like this:
	// [key-in-saved-setting][sub-key-in-setting][css-property] = [elements].
	$elements = array(
		'content'       => array(
			'accent'     => array(
				'color'            => array( '.color-accent', '.color-accent-hover:hover', '.color-accent-hover:focus', ':root .has-accent-color', '.has-drop-cap:not(:focus):first-letter', '.wp-block-button.is-style-outline', 'a' ),
				'border-color'     => array( 'blockquote', '.border-color-accent', '.border-color-accent-hover:hover', '.border-color-accent-hover:focus' ),
				'background-color' => array( 'button', '.button', '.faux-button', '.wp-block-button__link', '.wp-block-file .wp-block-file__button', 'input[type="button"]', 'input[type="reset"]', 'input[type="submit"]', '.bg-accent', '.bg-accent-hover:hover', '.bg-accent-hover:focus', ':root .has-accent-background-color', '.comment-reply-link' ),
				'fill'             => array( '.fill-children-accent', '.fill-children-accent *' ),
			),
			'background' => array(
				'color'            => array( ':root .has-background-color', 'button', '.button', '.faux-button', '.wp-block-button__link', '.wp-block-file__button', 'input[type="button"]', 'input[type="reset"]', 'input[type="submit"]', '.wp-block-button', '.comment-reply-link', '.has-background.has-primary-background-color:not(.has-text-color)', '.has-background.has-primary-background-color *:not(.has-text-color)', '.has-background.has-accent-background-color:not(.has-text-color)', '.has-background.has-accent-background-color *:not(.has-text-color)' ),
				'background-color' => array( ':root .has-background-background-color' ),
			),
			'text'       => array(
				'color'            => array( 'body', '.entry-title a', ':root .has-primary-color' ),
				'background-color' => array( ':root .has-primary-background-color' ),
			),
			'secondary'  => array(
				'color'            => array( 'cite', 'figcaption', '.wp-caption-text', '.post-meta', '.entry-content .wp-block-archives li', '.entry-content .wp-block-categories li', '.entry-content .wp-block-latest-posts li', '.wp-block-latest-comments__comment-date', '.wp-block-latest-posts__post-date', '.wp-block-embed figcaption', '.wp-block-image figcaption', '.wp-block-pullquote cite', '.comment-metadata', '.comment-respond .comment-notes', '.comment-respond .logged-in-as', '.pagination .dots', '.entry-content hr:not(.has-background)', 'hr.styled-separator', ':root .has-secondary-color' ),
				'background-color' => array( ':root .has-secondary-background-color' ),
			),
			'borders'    => array(
				'border-color'        => array( 'pre', 'fieldset', 'input', 'textarea', 'table', 'table *', 'hr' ),
				'background-color'    => array( 'caption', 'code', 'code', 'kbd', 'samp', '.wp-block-table.is-style-stripes tbody tr:nth-child(odd)', ':root .has-subtle-background-background-color' ),
				'border-bottom-color' => array( '.wp-block-table.is-style-stripes' ),
				'border-top-color'    => array( '.wp-block-latest-posts.is-grid li' ),
				'color'               => array( ':root .has-subtle-background-color' ),
			),
		),
		'header-footer' => array(
			'accent'     => array(
				'color'            => array( 'body:not(.overlay-header) .primary-menu > li > a', 'body:not(.overlay-header) .primary-menu > li > .icon', '.modal-menu a', '.footer-menu a, .footer-widgets a', '#site-footer .wp-block-button.is-style-outline', '.wp-block-pullquote:before', '.singular:not(.overlay-header) .entry-header a', '.archive-header a', '.header-footer-group .color-accent', '.header-footer-group .color-accent-hover:hover' ),
				'background-color' => array( '.social-icons a', '#site-footer button:not(.toggle)', '#site-footer .button', '#site-footer .faux-button', '#site-footer .wp-block-button__link', '#site-footer .wp-block-file__button', '#site-footer input[type="button"]', '#site-footer input[type="reset"]', '#site-footer input[type="submit"]' ),
			),
			'background' => array(
				'color'            => array( '.social-icons a', 'body:not(.overlay-header) .primary-menu ul', '.header-footer-group button', '.header-footer-group .button', '.header-footer-group .faux-button', '.header-footer-group .wp-block-button:not(.is-style-outline) .wp-block-button__link', '.header-footer-group .wp-block-file__button', '.header-footer-group input[type="button"]', '.header-footer-group input[type="reset"]', '.header-footer-group input[type="submit"]' ),
				'background-color' => array( '#site-header', '.footer-nav-widgets-wrapper', '#site-footer', '.menu-modal', '.menu-modal-inner', '.search-modal-inner', '.archive-header', '.singular .entry-header', '.singular .featured-media:before', '.wp-block-pullquote:before' ),
			),
			'text'       => array(
				'color'               => array( '.header-footer-group', 'body:not(.overlay-header) #site-header .toggle', '.menu-modal .toggle' ),
				'background-color'    => array( 'body:not(.overlay-header) .primary-menu ul' ),
				'border-bottom-color' => array( 'body:not(.overlay-header) .primary-menu > li > ul:after' ),
				'border-left-color'   => array( 'body:not(.overlay-header) .primary-menu ul ul:after' ),
			),
			'secondary'  => array(
				'color' => array( '.site-description', 'body:not(.overlay-header) .toggle-inner .toggle-text', '.widget .post-date', '.widget .rss-date', '.widget_archive li', '.widget_categories li', '.widget cite', '.widget_pages li', '.widget_meta li', '.widget_nav_menu li', '.powered-by-wordpress', '.to-the-top', '.singular .entry-header .post-meta', '.singular:not(.overlay-header) .entry-header .post-meta a' ),
			),
			'borders'    => array(
				'border-color'     => array( '.header-footer-group pre', '.header-footer-group fieldset', '.header-footer-group input', '.header-footer-group textarea', '.header-footer-group table', '.header-footer-group table *', '.footer-nav-widgets-wrapper', '#site-footer', '.menu-modal nav *', '.footer-widgets-outer-wrapper', '.footer-top' ),
				'background-color' => array( '.header-footer-group table caption', 'body:not(.overlay-header) .header-inner .toggle-wrapper::before' ),
			),
		),
	);

	/**
	* Filters Twenty Twenty theme elements
	*
	* @since Twenty Twenty 1.0
	*
	* @param array Array of elements
	*/
	return apply_filters( 'venquis_get_elements_array', $elements );
}


function custom_post_type() {

	$labels = array(
		'name'                => _x( 'Case Study', 'Post Type General Name', 'venquis' ),
		'singular_name'       => _x( 'Case Study', 'Post Type Singular Name', 'venquis' ),
		'menu_name'           => __( 'Case Study', 'venquis' ),
		'parent_item_colon'   => __( 'Parent Case Study', 'venquis' ),
		'all_items'           => __( 'All Case Study', 'venquis' ),
		'view_item'           => __( 'View Case Study', 'venquis' ),
		'add_new_item'        => __( 'Add New Case Study', 'venquis' ),
		'add_new'             => __( 'Add Case Study', 'venquis' ),
		'edit_item'           => __( 'Edit Case Study', 'venquis' ),
		'update_item'         => __( 'Update Case Study', 'venquis' ),
		'search_items'        => __( 'Search Case Study', 'venquis' ),
		'not_found'           => __( 'Not Found', 'venquis' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'venquis' ),
	);
	
	$args = array(
		'label'               => __( 'casestudy', 'venquis' ),
		'description'         => __( 'Case Study and reviews', 'venquis' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
		'taxonomies'          => array( 'topics', 'casestudy_categories' , 'tags'),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-media-text',
	);

	register_post_type( 'casestudy', $args );
}

add_action( 'init', 'custom_post_type', 0 );

function create_casestudy_taxonomies() {
    $labels = array(
        'name'              => _x( 'Casestudy Categories', 'taxonomy general name' ),
        'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Categories' ),
        'all_items'         => __( 'All Categories' ),
        'parent_item'       => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category:' ),
        'edit_item'         => __( 'Edit Category' ),
        'update_item'       => __( 'Update Category' ),
        'add_new_item'      => __( 'Add New Category' ),
        'new_item_name'     => __( 'New Category Name' ),
        'menu_name'         => __( 'Categories' ),
    );

    $args = array(
        'hierarchical'      => true, // Set this to 'false' for non-hierarchical taxonomy (like tags)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'categories' ),
    );

    register_taxonomy( 'casestudy_categories', array( 'casestudy' ), $args );
}
add_action( 'init', 'create_casestudy_taxonomies', 0 );
function custom_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function new_excerpt_more($more) {
    global $post;
    return '';
   }
add_filter('excerpt_more', 'new_excerpt_more');

function get_trending_insights_shortcode() { 
	$lang = pll_current_language();
	$args = array(
		'post_type' => 'post',
		'lang' => $lang,
		'posts_per_page' => 2,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post_status'      => 'publish',
		'meta_key'         => 'featured',
		'meta_value'       => 1
	);
	$posts = get_posts( $args );

	$message = '<div class="row">';
	foreach($posts as $post):
		$message .= sprintf('<div class="col-sm-6 col-lg-6 col-md-6 mb-4">
			<div class="card h-100">
				<div class="card-image">
				<a href="%s"><img height="350" class="card-img-top" src="%s" alt=""></a>
				</div>
				<div class="card-body">
				<span>%s</span>
				<p class="card-text">%s<a class="link link--arrowed" href="%s">
				<svg class="hover-arrow-icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
					<g fill="none" stroke="#5A15FF" stroke-width="1.5" stroke-linejoin="round" stroke-miterlimit="10">
					<circle class="arrow-icon--circle" cx="16" cy="16" r="15.12"></circle>
					<path class="arrow-icon--arrow" d="M 19 10.5 L 25 16 l -6 6.4 M 6 16 h 19"></path>
					</g>
				</svg>
				</a></p>
				</div>
			</div>
		</div>', get_permalink($post->ID), wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'medium' ), get_field("insight_read", $post->ID), $post->post_title, get_permalink($post->ID));
	endforeach;
	$message .= '</div>';

	return $message;
} 
// register shortcode
add_shortcode('trending_insights', 'get_trending_insights_shortcode');

function get_trending_casestudies_shortcode() { 
	$lang = pll_current_language();
	$args = array(
		'post_type' => 'casestudy',
		'lang' => $lang,
		'posts_per_page' => 4,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post_status'      => 'publish',
		'meta_key'         => 'featured',
		'meta_value'       => 1
	);
	$posts = get_posts( $args );
	$message = '%s<div class="row">%s</div>';
	$i=0;
	$seconds = $first = '';
	foreach($posts as $post):
		if($i == 0):
			$first = sprintf('<div class="row h-100 mb-4 hide-in-mobile">
			<div class="col-lg-12"><div class="card"><div class="card-horizontal">
			<div class="img-square-wrapper"><a href="%s"><img src="%s" class="img-fluid" alt="icon"></a>
			</div><div class="card-body linear-box">
			<div class="case-shape hide-shape"><a href="%s"><span>%s</span><br>%s</a></div>
			<p class="lead">%s <a class="link link--arrowed" href="%s"><svg class="hover-arrow-icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
			<g fill="none" stroke="#5A15FF" stroke-width="1.5" stroke-linejoin="round" stroke-miterlimit="10">
			<circle class="arrow-icon--circle" cx="16" cy="16" r="15.12"></circle>
			<path class="arrow-icon--arrow" d="M 19 10.5 L 25 16 l -6 6.4 M 6 16 h 19"></path>
			</g></svg></a></p></div></div></div></div></div>', 
			get_permalink($post->ID),
			wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'medium' ),
			get_permalink($post->ID),
			get_field("company", $post->ID),
			@get_the_terms( $post->ID, 'casestudy_categories' )[0]->name,
			$post->post_title,
			get_permalink($post->ID)
		);
		else:
			$seconds .= sprintf('<div class="col-sm-6 col-lg-4 col-md-6 mb-4"><div class="card h-100">
			  <div class="card-image"><a href="%s"><img class="card-img-top" src="%s" alt=""></a>
				<div class="shape case-shape"><a href="%s"><span>%s</span><br>%s</a>
				</div></div><div class="card-body">
			   <p class="card-text">%s <a class="link link--arrowed" href="%s"><svg class="hover-arrow-icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
			   <g fill="none" stroke="#5A15FF" stroke-width="1.5" stroke-linejoin="round" stroke-miterlimit="10">
			   <circle class="arrow-icon--circle" cx="16" cy="16" r="15.12"></circle>
			   <path class="arrow-icon--arrow" d="M 19 10.5 L 25 16 l -6 6.4 M 6 16 h 19"></path>
			   </g></svg></a></p>
			  </div></div></div>', get_permalink($post->ID), wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'medium' ),
			get_permalink($post->ID), get_field("company", $post->ID), @get_the_terms( $post->ID, 'casestudy_categories' )[0]->name,
			substr($post->post_title, 0, 180), get_permalink($post->ID));
		endif;
		$i++;
	endforeach;
	return sprintf($message, $first, $seconds);
}

// register shortcode
add_shortcode('trending_casestudies', 'get_trending_casestudies_shortcode');

function testimonials_post_type() {

	$labels = array(
		'name'                => _x( 'Testimonials', 'Post Type General Name', 'venquis' ),
		'singular_name'       => _x( 'Testimonial', 'Post Type Singular Name', 'venquis' ),
		'menu_name'           => __( 'Testimonials', 'venquis' ),
		'parent_item_colon'   => __( 'Parent Testimonials', 'venquis' ),
		'all_items'           => __( 'All Testimonials', 'venquis' ),
		'view_item'           => __( 'View Testimonials', 'venquis' ),
		'add_new_item'        => __( 'Add New Testimonial', 'venquis' ),
		'add_new'             => __( 'Add Testimonial', 'venquis' ),
		'edit_item'           => __( 'Edit Testimonial', 'venquis' ),
		'update_item'         => __( 'Update Testimonial', 'venquis' ),
		'search_items'        => __( 'Search Testimonials', 'venquis' ),
		'not_found'           => __( 'Not Found', 'venquis' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'venquis' ),
	);
	
	$args = array(
		'label'               => __( 'Testimonial', 'venquis' ),
		'description'         => __( 'Testimonial from clients', 'venquis' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
		'taxonomies'          => array(),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 6,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-businessman',
	);

	register_post_type( 'testimonial', $args );
}
add_action( 'init', 'testimonials_post_type', 0 );

function get_client_testimonials_shortcode() {
	$lang = pll_current_language();
	$args = array(
		'post_type' => 'testimonial',
		'lang' => $lang,
		'posts_per_page' => 5,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post_status'      => 'publish',
	);
	$posts = get_posts( $args );
	$message = '<div class="slider-content"><div class="slider single-item">%s</div></div>';
	$content = '';
	foreach($posts as $post):
		$content .= sprintf('<div class="quote-container">
			<div class="quote"><blockquote>
				<p>%s</p><cite><p class="author-name">%s</p><span class="author-des">%s</span>
				<img src="%s"></cite></blockquote></div>
			</div>', 
			$post->post_title,
			get_field("client_name", $post->ID),
			get_field("client_position", $post->ID),
			wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'small')
		);

	endforeach;
	return sprintf($message, $content);

}
// register shortcode
add_shortcode('client_testimonials', 'get_client_testimonials_shortcode');


function people_post_type() {

	$labels = array(
		'name'                => _x( 'Peoples', 'Post Type General Name', 'venquis' ),
		'singular_name'       => _x( 'People', 'Post Type Singular Name', 'venquis' ),
		'menu_name'           => __( 'Peoples', 'venquis' ),
		'parent_item_colon'   => __( 'Parent Peoples', 'venquis' ),
		'all_items'           => __( 'All Peoples at Venquis', 'venquis' ),
		'view_item'           => __( 'View Peoples at Venquis', 'venquis' ),
		'add_new_item'        => __( 'Add New People', 'venquis' ),
		'add_new'             => __( 'Add People', 'venquis' ),
		'edit_item'           => __( 'Edit People', 'venquis' ),
		'update_item'         => __( 'Update People', 'venquis' ),
		'search_items'        => __( 'Search People', 'venquis' ),
		'not_found'           => __( 'Not Found', 'venquis' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'venquis' ),
	);
	
	$args = array(
		'label'               => __( 'Peoples', 'venquis' ),
		'description'         => __( 'Peoples at Venquis', 'venquis' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
		'taxonomies'          => array('people_categories', 'topics' , 'tags'),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 7,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-businesswoman'
	);

	register_post_type( 'people', $args );
}
add_action( 'init', 'people_post_type', 0 );

function create_people_taxonomies() {
    $labels = array(
        'name'              => _x( 'People Categories', 'taxonomy general name' ),
        'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Categories' ),
        'all_items'         => __( 'All Categories' ),
        'parent_item'       => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category:' ),
        'edit_item'         => __( 'Edit Category' ),
        'update_item'       => __( 'Update Category' ),
        'add_new_item'      => __( 'Add New Category' ),
        'new_item_name'     => __( 'New Category Name' ),
        'menu_name'         => __( 'Categories' ),
    );

    $args = array(
        'hierarchical'      => true, // Set this to 'false' for non-hierarchical taxonomy (like tags)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'categories' ),
    );

    register_taxonomy( 'people_categories', array( 'people' ), $args );
}
add_action( 'init', 'create_people_taxonomies', 0 );

function get_venquis_peoples_shortcode() {
	$lang = pll_current_language();
	$args = array(
		'post_type' => 'people',
		'lang' => $lang,
		'posts_per_page' => -1,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post_status'      => 'publish',
	);
	$posts = get_posts( $args );
	$message = '';
	$i = 0;
	foreach($posts as $post):
		$class = "";
		if ($i > 3){ $class = "people_view_more"; }
		$message .= sprintf('<div class="col-sm-6 col-lg-4 col-xl-3 col-md-6 mb-5 people-full-list %s">
			<div class="card h-100">
			<div class="card-image">
				<a href="%s"><img class="card-img-top" src="%s" alt=""></a>
				<div class="shape case-shape">
				<a href="%s"><span>%s</span><br>%s</a>
				</div>
			</div>
			</div>
			</div>', 
			$class,
			get_permalink($post->ID),
			wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'medium'),
			get_permalink($post->ID),
			$post->post_title,
			get_field("position", $post->ID)
		);
	$i++;
	endforeach;
	return $message;
}
// register shortcode
add_shortcode('venquis_peoples', 'get_venquis_peoples_shortcode');

function teamwords_post_type() {

	$labels = array(
		'name'                => _x( 'Team words', 'Post Type General Name', 'venquis' ),
		'singular_name'       => _x( 'Team word', 'Post Type Singular Name', 'venquis' ),
		'menu_name'           => __( 'Team words', 'venquis' ),
		'parent_item_colon'   => __( 'Parent Team word', 'venquis' ),
		'all_items'           => __( 'All Team word', 'venquis' ),
		'view_item'           => __( 'View Team word', 'venquis' ),
		'add_new_item'        => __( 'Add Team word', 'venquis' ),
		'add_new'             => __( 'Add Team word', 'venquis' ),
		'edit_item'           => __( 'Edit Team word', 'venquis' ),
		'update_item'         => __( 'Update Team word', 'venquis' ),
		'search_items'        => __( 'Search Team word', 'venquis' ),
		'not_found'           => __( 'Not Found', 'venquis' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'venquis' ),
	);
	
	$args = array(
		'label'               => __( 'Team word', 'venquis' ),
		'description'         => __( 'Team words about Venquis', 'venquis' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
		'taxonomies'          => array(),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 8,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-businesswoman'
	);

	register_post_type( 'teamwords', $args );
}
add_action( 'init', 'teamwords_post_type', 0 );

function get_venquis_teamwords_shortcode(){
	$lang = pll_current_language();
	$args = array(
		'post_type' => 'teamwords',
		'lang' => $lang,
		'posts_per_page' => -1,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post_status'      => 'publish',
	);
	$posts = get_posts( $args );
	$message = '';
	foreach($posts as $post):
		$bg_field = get_field("background_colour", $post->ID);
		$bg_color = (strlen($bg_field) > 1) ? $bg_field : '#33394F';
		$style = 'style="background: linear-gradient(108.34deg, '.$bg_color.' 0%, '.$bg_color.' 100%);"';

		$message .= sprintf('<div class="swiper-slide" %s>
			<div class="box-content mb-30">
			<span class="team-title mb-lg-5 mb-md-3 mb-sm-3">%s</span>
			<div class="title" data-swiper-parallax="-300" data-swiper-parallax-duration="1500">%s</div>
			<div class="subtitle" data-swiper-parallax="-200">
				<div class="box-info mt-4" data-swiper-parallax="-300" data-swiper-parallax-duration="1500">
				<h5>%s</h5><h5>%s</h5></div>
			</div></div><div class="team-members">
			<img src="%s" class="img-fluid" data-swiper-parallax="-1000" data-swiper-parallax-duration="2000">
			</div></div>',
			$style,
			pll__("Take our team’s word for it"),
			$post->post_content,
			get_field("author_name", $post->ID),
			get_field("position", $post->ID),
			wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'full')
		);
	endforeach;
	return $message;
}

add_shortcode('venquis_teamwords', 'get_venquis_teamwords_shortcode');

function get_know_us_shortcode() {
	$lang = pll_current_language();
	$args = array(
		'post_type' => 'people',
		'lang' => $lang,
		'posts_per_page' => 3,
		'meta_key'         => 'featured',
		'meta_value'       => 1,
		'tax_query' => array(
			array(
			  'taxonomy' => 'people_categories',
			  'field' => 'slug',
			  'terms' => 'talent-acquisition'
			)
			),
		'post_status'      => 'publish'
	);
	$posts = get_posts( $args );
	$message = '';
	foreach($posts as $post):
		$message .= sprintf('<div class="col-lg-4 col-md-6 col-sm-6 mb-5">
		<div class="card h-100"><div class="card-image">
			<a href="%s"><img class="card-img-top" src="%s" alt=""></a>
			<div class="shape case-shape">
			  <a href="%s"><span>%s</span><br>%s</a>
			</div></div>
		  	<div class="card-body">
			<p class="card-texts min-body">%s <a class="link link--arrowed" href="%s">
			<svg class="hover-arrow-icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
				<g fill="none" stroke="#5A15FF" stroke-width="1.5" stroke-linejoin="round" stroke-miterlimit="10">
				<circle class="arrow-icon--circle" cx="16" cy="16" r="15.12"></circle>
				<path class="arrow-icon--arrow" d="M 19 10.5 L 25 16 l -6 6.4 M 6 16 h 19"></path>
				</g>
			</svg>
			</a></p>
			<div class="row align-items-center">
			  <div class="col-lg-12">
				<div class="team-social"><a href="tel:%d"><img src="/wp-content/themes/venquis/assets/images/callphone.png"></a>
				<a href="%s" target="blank"><img src="/wp-content/themes/venquis/assets/images/linkedin.png">
				</a>
				<a href="mailto:%s"><img src="/wp-content/themes/venquis/assets/images/mail.png"></a>
				</div>
			</div></div></div></div></div>', 
			get_permalink($post->ID),
			wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'medium'),
			get_permalink($post->ID),
			$post->post_title,
			get_field("position", $post->ID),
			limit_text(strip_tags($post->post_content), 28),
			get_permalink($post->ID),
			get_field("phone", $post->ID),
			get_field("linkedin_profile", $post->ID),
			get_field("email_id", $post->ID)
		);
	endforeach;
	return $message;
}
// register shortcode
add_shortcode('know_us', 'get_know_us_shortcode');


function limit_text($text, $limit) {
    if (str_word_count($text, 0) > $limit) {
        $words = str_word_count($text, 2);
        $pos   = array_keys($words);
        $text  = substr($text, 0, $pos[$limit]);
    }
    return $text;
}

function hire_survey_post_type() {

	$labels = array(
		'name'                => _x( 'Hiring Survey', 'Post Type General Name', 'venquis' ),
		'singular_name'       => _x( 'Hiring Survey', 'Post Type Singular Name', 'venquis' ),
		'menu_name'           => __( 'Hiring Survey', 'venquis' ),
		'parent_item_colon'   => __( 'Parent Hiring Survey', 'venquis' ),
		'all_items'           => __( 'All Hiring Survey', 'venquis' ),
		'view_item'           => __( 'View QuHiring Surveyiz', 'venquis' ),
		'add_new_item'        => __( 'Add Hiring Survey', 'venquis' ),
		'add_new'             => __( 'Add Hiring Survey', 'venquis' ),
		'edit_item'           => __( 'Edit Hiring Survey', 'venquis' ),
		'update_item'         => __( 'Update Hiring Survey', 'venquis' ),
		'search_items'        => __( 'Search Hiring Survey', 'venquis' ),
		'not_found'           => __( 'Not Found', 'venquis' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'venquis' ),
	);
	
	$args = array(
		'label'               => __( 'Hiring Survey', 'venquis' ),
		'description'         => __( 'Hiring Survey about Venquis', 'venquis' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor','revisions', 'custom-fields'),
		'taxonomies'          => array(),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => false,
		'menu_position'       => 2,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => false,
		'menu_icon'           => 'dashicons-businesswoman',
	);

	register_post_type('survey', $args );
}
add_action( 'init', 'hire_survey_post_type', 0 );

function hiring_post_type() {

	$labels = array(
		'name'                => _x( 'Hire', 'Post Type General Name', 'venquis' ),
		'singular_name'       => _x( 'Hire', 'Post Type Singular Name', 'venquis' ),
		'menu_name'           => __( 'Hire', 'venquis' ),
		'parent_item_colon'   => __( 'Parent Hiring', 'venquis' ),
		'all_items'           => __( 'All Hiring', 'venquis' ),
		'view_item'           => __( 'View Hiring', 'venquis' ),
		'add_new_item'        => __( 'Add Hiring', 'venquis' ),
		'add_new'             => __( 'Add Hiring', 'venquis' ),
		'edit_item'           => __( 'Edit Hiring', 'venquis' ),
		'update_item'         => __( 'Update Hiring', 'venquis' ),
		'search_items'        => __( 'Search Hiring', 'venquis' ),
		'not_found'           => __( 'Not Found', 'venquis' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'venquis' ),
	);
	
	$args = array(
		'label'               => __( 'Hire', 'venquis' ),
		'description'         => __( 'Hiring at Venquis', 'venquis' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
		'taxonomies'          => array(),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 9,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-businesswoman'
	);

	register_post_type( 'hire', $args );
}
add_action( 'init', 'hiring_post_type', 0 );

function get_skills_shortcode() {
	$lang = pll_current_language();
	$args = array(
		'post_type' => 'hire',
		'lang' => $lang,
		'posts_per_page' => 6,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post_status'      => 'publish',
	);
	$posts = get_posts( $args );
	$message = '';
	$skills = [];
	foreach($posts as $post):
		while ( have_rows('skill_sets', $post->ID) ) : the_row();
			$skill = get_sub_field('skill_name', $post->ID);
			if (strlen($skill) > 40){
				continue;
			}
        	$skills[$skill] = get_permalink($post->ID);
    	endwhile;
	endforeach;
	$i = 1;
	$results = $items = '';
	foreach($skills as $key => $value):
		
		if($i == 16):
			$url = (($lang =='de') ? $lang.'/' : '').'hire';
			$items .= sprintf('<li class="list-group-item"><a href="/%s">View more</a></li>', $url);
		else:
			$items .= sprintf('<li class="list-group-item"><a href="%s">%s</a></li>', $value, $key);
		endif;
		if($i % 4 == 0):
			$results .= sprintf('<div class="col-lg-3"><ul class="list-group">%s</ul></div>', $items);
			$items = '';
		endif;
		if($i == 16):
			break;
		endif;			
		$i++;
	endforeach;
	return $results;
}
// register shortcode
add_shortcode('get_skills', 'get_skills_shortcode');

function get_leaderships_shortcode() {
	$lang = pll_current_language();
	$args = array(
		'post_type' => 'hire',
		'lang' => $lang,
		'posts_per_page' => 6,
		'orderby' => 'meta_value',
        'meta_key' => 'sort_order',
		'order'            => 'ASC',
		'post_status'      => 'publish',
	);
	$posts = get_posts( $args );
	$response = '';
	
	foreach($posts as $post):
		$skills = [];
		while ( have_rows('skill_sets', $post->ID) ) : the_row();
        	array_push($skills, get_sub_field('skill_name', $post->ID));
    	endwhile;
		$response .= sprintf('<div class="col-sm-6 col-md-6 col-lg-4 list-box"><a href="%s">
        <div class="box-card"><div class="imgBx"><img src="%s/assets/images/shape-right.png" class="img-shape" alt="icon">
        <div class="icon"><img src="%s" width="90px" class="img-fluid" alt="icon"></div>
        <div class="content"><h3 class="title">%s</h3><p class="description">%s</p></div><div class="arrow-icon">
        <span class="arrow-circle"><img src="%s/assets/images/arrow.png" class="img-fluid" alt="icon"></span>
        <circle class="arrow-icon--circle" cx="16" cy="16" r="15.12"></circle></div></div></div></a></div>',
		get_permalink($post->ID),
		get_template_directory_uri(),
		get_field('skill_icons_image', $post->ID),
		get_field('skill_heading', $post->ID),
		implode(', ', $skills),
		get_template_directory_uri()
	);
	endforeach;
	return $response;
}
// register shortcode
add_shortcode('get_leaderships', 'get_leaderships_shortcode');


add_action( 'init', 'get_contacts_post_type' );
function get_contacts_post_type() {
	register_post_type( 'contacts', array(
	'labels' => array(
		'name' => 'Contacts',
		'singular_name' => 'Contact',
	),
	'description' => 'Contacts on this blog.',
	'publicly_queryable'  => false,
	'public' => true,
	'menu_position' => 2,
	'supports' => array( 'title', 'editor', 'custom-fields' )
	));
}



add_action( 'wp_ajax_save_contatus_form', 'save_contatus_form' );
add_action( 'wp_ajax_nopriv_save_contatus_form', 'save_contatus_form' );
function save_contatus_form()
{
		$post_id = wp_insert_post(array (
		'post_type' => 'contacts',
		'post_title' => "Enquiry-",
		'post_content' => '',
		'post_status' => 'publish',
		'comment_status' => 'closed',
		'ping_status' => 'closed',
	));
	if ($post_id) {
		add_post_meta($post_id, 'full_name', $_POST['full_name']);
		add_post_meta($post_id, 'email_id', $_POST['email']);
		add_post_meta($post_id, 'message', $_POST['message']);
		add_post_meta($post_id, 'enquiry', $_POST['enquiry']);
		add_post_meta($post_id, 'country', $_POST['country']);

	}
	$my_post['ID'] = $post_id;
    $my_post['post_title'] = "Enquiry-".$post_id;
	wp_update_post( $my_post );

	$user_data = array(
		'Name' => $_POST['full_name'],
		'Email' => $_POST['email'],
		'Message' => $_POST['message'],
		'Enquiry' => $_POST['enquiry'],
		'Country' => $_POST['country']
	);

	$logo_url = get_template_directory_uri();
	ob_start();
	include (get_template_directory() . '/email/contact.php');
	$email_content = ob_get_contents();
	ob_end_clean();
	$headers = array('Content-Type: text/html; charset=UTF-8');
	$bullhornnew = new BullHornAPI('', '', $_POST);
	$bullhornnew->contact_form();
	wp_mail("sanal.nayana@reckon.com", "Contact details", $email_content, $headers);
	die(json_encode(["message" => sprintf("Thanks %s, we’ll get back to you soon.", $_POST['full_name'])]));
	
}

add_filter( 'manage_contacts_posts_columns', 'smashing_filter_contacts_columns' );
function smashing_filter_contacts_columns( $columns ) {
  $columns['message'] = __( 'Message' );
  $columns['full_name'] = __( 'Full Name', 'smashing' );
  $columns['enquiry'] = __( 'enquiry', 'smashing' );
  $columns['email_id'] = __( 'email_id', 'smashing' );
  
  return $columns;
}

add_action( 'manage_contacts_posts_custom_column', 'smashing_contacts_column', 10, 2);
function smashing_contacts_column( $column, $post_id ) {
	if ( 'message' === $column ) {
		echo get_field('message', $post_id);
	}
	if ( 'full_name' === $column ) {
		echo  get_field('full_name', $post_id);
	}
	if ( 'enquiry' === $column ) {
		echo get_field('enquiry', $post_id);
	}
	if ( 'email_id' === $column ) {
		echo get_field('email_id', $post_id);
	}
}

add_filter( 'manage_post_posts_columns', 'featured_column_columns' );
add_filter( 'manage_casestudy_posts_columns', 'featured_column_columns' );
add_filter( 'manage_careers_posts_columns', 'featured_column_columns' );
add_filter( 'manage_charity_posts_columns', 'featured_column_columns' );
add_filter( 'manage_people_posts_columns', 'featured_column_columns' );
function featured_column_columns( $columns ) {
	$columns['featured'] = '<div class="dashicons dashicons-star-half"></div>';;
	return $columns;
  }
add_action( 'manage_post_posts_custom_column', 'featured_column', 10, 2);
add_filter( 'manage_casestudy_posts_custom_column', 'featured_column',  10, 2);
add_filter( 'manage_careers_posts_custom_column', 'featured_column',  10, 2);
add_filter( 'manage_charity_posts_custom_column', 'featured_column',  10, 2);
add_filter( 'manage_people_posts_custom_column', 'featured_column',  10, 2);
function featured_column( $column, $post_id ) {
	if ( 'featured' === $column ) {
		if (get_field('featured', $post_id)){
			echo '<div class="dashicons dashicons-star-filled"></div>';
		} else {
			echo '<div class="dashicons dashicons-star-empty"></div>';
		}
	}
}

function get_mostdemand_talent_shortcode() {
	$lang = pll_current_language();
	$args = array(
		'post_type' => 'hire',
		'lang' => $lang,
		'posts_per_page' => -1,
		'orderby' => 'meta_value',
        'meta_key' => 'sort_order',
		'order'            => 'ASC',
		'post_status'      => 'publish',
	);
	$posts = get_posts( $args );
	$response = '';
	$j = 1;
	foreach($posts as $post):
		$skills = [];
		$skill = [];
		while ( have_rows('skill_sets', $post->ID) ) : the_row();
			$skill = get_sub_field('skill_name', $post->ID);
			$skills[$skill] = get_permalink($post->ID);
		endwhile;
		$new_skills = explode(", ", get_field('skills', $post->ID));
		foreach($new_skills as $key):
		  $skills[$key] = get_permalink($post->ID);
		endforeach;
		$results = $items = '';
		$skills  = array_splice($skills, 0, 7);
		$i = 1;
		foreach($skills as $key => $value):
			$results .= sprintf('<li class="list-group-item"><a href="%s">%s</a></li>', $value, $key);
			if($i % 5 == 0):
				$class = "";
				if ($j>1):
					$class = "d-block d-sm-none d-md-block";
				endif;
				$response .= sprintf('<div class="col-lg-3 col-md-6 mb-lg-5 mb-sm-3 ">
					<h4 class="list-group-title mostdemand %s">%s</h4>
					<ul class="list-group mostdemand %s">%s</ul></div>', $class,
					get_field('skill_heading', $post->ID), $class, $results);
				$results= '';
				$j++;
			endif;
			$i++;
		endforeach;
	endforeach;
	return $response;
}
// register shortcode
add_shortcode('mostdemand_talent', 'get_mostdemand_talent_shortcode');

function wpex_pagination($wp_query, $pages='pages') {
	$prev_arrow = is_rtl() ? '→' : '←';
	$next_arrow = is_rtl() ? '←' : '→';
	global $wp;
	$total = $wp_query->max_num_pages;
	if( $total > 1 )  {
		$paged = @$_GET[$pages];
		if( !$current_page = $paged )
			$current_page = 1;
		$format = '?'.$pages.'=%#%';
		$args = array(
			'base'         => home_url( $wp->request ).'%_%',
			'format'		=> $format,
			'current'		=> max(1, $paged),
			'total' 		=> $total,
			//'mid_size'		=> 3,
			'type' 			=> 'plain',
			'prev_text'		=> $prev_arrow,
			'next_text'		=> $next_arrow,
		);
		//print_r($args);die();
		echo paginate_links($args);
	}
}

add_action('init', 'register_custom_menu');
function register_custom_menu() {
    register_nav_menu('custom_contact_menu', __('Custom Contact Menu'));
	register_nav_menu('custom_social_menu', __('Custom Social Menu'));
	register_nav_menu('custom_about_menu', __('Custom About Menu'));
}


add_action( 'wp_ajax_register_job_seeker', 'register_job_seeker' );
add_action( 'wp_ajax_nopriv_register_job_seeker', 'register_job_seeker' );
function register_job_seeker(){

	$nonce  = $_POST['nonce'];
	if ( ! wp_verify_nonce( $nonce, 'reg-nonce' ) ) {
		die(json_encode(['message' => 'Token as expired please refresh the page.']));
	}

	$email = $_POST['email'];
	$full_name = $_POST['firstname'].' '.$_POST['lastname'];
	if(email_exists($email) == false){
		$user_data = array(
			'user_pass' => wp_generate_password(),
			'user_login' => $email,
			'user_email' => $email,
			'display_name' => $full_name,
			'nickname' => $full_name,
			'first_name' => $_POST['firstname'],
			'last_name' => $_POST['lastname'],
			'role' => get_option('default_role')
		);
		$user_id = wp_insert_user( $user_data );
		$existing = FALSE;
	} else {
		$user_id = get_user_by('email', $email)->ID;
		$existing = TRUE;
	}
	$enquiry = getservices($_POST['enquiry']);
	update_user_meta( $user_id, 'job_title', $_POST['job_title'] );
	update_user_meta( $user_id, 'location', $_POST['location'] );
	update_user_meta( $user_id, 'enquiry', $enquiry );
	update_user_meta( $user_id, 'role_type', $_POST['role_type'] );
	update_user_meta( $user_id, 'mobile_number', $_POST['full_number']);


	if( @$_FILES['fileupload']['error'] === UPLOAD_ERR_OK ) {

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $_FILES['fileupload']['tmp_name']);
		$file_size = filesize($_FILES['fileupload']['tmp_name']);

		if ($file_size > 2097152) {
			return ["message" => "File size less than 2MB is expected."];
		}
		$ext = pathinfo(basename($_FILES['fileupload']['name']), PATHINFO_EXTENSION);
		
		if ($ext == 'docx') {
			$allowed_mime = array("application/msword", "application/zip", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/pdf");
		} else {
			$allowed_mime = array("application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/pdf");
		}
		if (!in_array($mime, $allowed_mime)){
			return ["message" => "Not a valid file type, expecting only Doc/Docx and PDF"];
		}

		$uploaddir = wp_upload_dir();
		$file = $_FILES['fileupload'];
		$uploadfile = $uploaddir['path'] . '/' . basename( $file['name'] );
		move_uploaded_file( $file['tmp_name'] , $uploadfile );
		$filename = basename( $uploadfile );
		$wp_filetype = wp_check_filetype(basename($filename), null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
		);
		$attach_id = wp_insert_attachment( $attachment, $uploadfile );
		update_user_meta( $user_id, 'resume', $attach_id );
	}
	// $scheduled = wp_schedule_single_event(time()+20, 'create_bullhorn_user', [$user_id, $existing, $_POST]);
	do_action( 'create_bullhorn_user', $user_id, $existing, $_POST);
	if ($scheduled == FALSE) {
		$bullhorn = new BullHornAPI($user_id, $existing, $_POST);
		$bullhorn->run();
	}
	trigger_emails($_POST, $user_id);
	die(json_encode(['user' => $full_name]));
}

add_action('create_bullhorn_user', 'bullhorn_user', 1, 3);
function bullhorn_user($user_id, $existing, $data)
{
	$bullhorn = new BullHornAPI($user_id, $existing, $data);
	list($candidate_id, $status) = $bullhorn->run();
	if ($status == FALSE){
		$update_count = get_user_meta($user_id, 'update_count', TRUE);
		update_user_meta( $user_id, 'update_count', $update_count+1 );
		wp_schedule_single_event(time()+10, 'create_bullhorn_user', [$user_id, TRUE, $data]);
	}
	update_user_meta( $user_id, 'candidate_id', $candidate_id );
}


add_action( 'init', 'get_careers_post_type' );
function get_careers_post_type() {
	register_post_type( 'careers', array(
	'labels' => array(
		'name' => 'Careers',
		'singular_name' => 'Career',
	),
	'description' => 'Careers on this blog.',
	'publicly_queryable'  => false,
	'public' => true,
	'menu_position' => 2,
	'supports' => array( 'title', 'editor', 'custom-fields' )
	));
}

function get_current_openings(){
	$lang = pll_current_language();
	$args = array(
		'post_type'        => 'careers',
		'lang'             => $lang,
		'posts_per_page'   => 3,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post_status'      => 'publish',
		'meta_key'         => 'featured',
		'meta_value'       => 1
	);
	$posts = get_posts( $args );
	$response = '';
	foreach($posts as $post):
		$response .= sprintf('<div class="col-lg-4"><div class="icon-content"><div class="content"><h3 class="title mb-2">%s</h3>
			<p class="locations">%s</p><p class="description">%s...</p></div><div class="current-opening-link block">
			<a href="%s" target="_blank" class="active-link">%s</a><a href="%s" target="_blank" class="btn btn-outline-primary">%s</a>
			</div></div></div>',
			$post->post_title,
			get_field('locations', $post->ID),
			limit_text(strip_tags($post->post_content), 28),
			get_field('read_more', $post->ID),
			pll__('Read more'),
			get_field('apply_link', $post->ID),
			pll__('Apply Now')
		);
	endforeach;
	return $response;
}
add_shortcode('current_openings', 'get_current_openings');

add_action( 'init', 'get_charity_post_type' );
function get_charity_post_type() {
	register_post_type( 'charity', array(
	'labels' => array(
		'name' => 'Charity',
		'singular_name' => 'Charity',
	),
	'description' => 'Charity and fundraising.',
	'publicly_queryable'  => true,
	'public' => true,
	'menu_position' => 3,
	'supports' => array( 'title', 'editor', 'custom-fields', 'thumbnail')
	));
}

function get_mad_details(){
	$page = pll_get_post(get_page_by_path( 'about-us' )->ID);
	$response = sprintf('<div class="card"><div class="card-horizontal"><div class="col-lg-6 img-square-wrapper path-overlay">
		<img src="%s" class="img-fluid" alt="icon">
		</div><div class="col-lg-6 card-body linear-box nominate-box"><div class="nominate-pic">
		<img src="%s"></div><div class="case-shape nominate-shape">
		<a href="%s"><span>%s</span><br>%s</a><p class="lead">%s</p>
		</div><div class="nominate-tag"><a href="%s" class="active-link">%s</a>
		</div></div></div></div>', 
		get_field('mad_featured_image', $page),
		get_field('mad_logo', $page),
		get_field('mad_url', $page),
		get_field('mad_title', $page),
		get_field('mad_hash_tag', $page),
		get_field('mad_content', $page),
		get_field('mad_nominate_link', $page),
		pll__('Nominate here')
	);
	
	return $response;
}
add_shortcode('mad33', 'get_mad_details');

function get_charity(){
	$lang = pll_current_language();
	$args = array(
		'post_type'        => 'charity',
		'lang'             => $lang,
		'posts_per_page'   => 2,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post_status'      => 'publish',
		'meta_key'         => 'featured',
		'meta_value'       => 1
	);
	$posts = get_posts( $args );
	$response = '';
	foreach($posts as $post):
		if(get_field('donation_link', $post->ID)):
			$donate_link = sprintf('<div class="row donate-here-tag"><div class="col-lg-12"><a href="%s" class="active-link">%s</a></div></div>', get_field('donation_link', $post->ID), pll__('Donate here'));
		else:
			$donate_link = '';
		endif;
		$response .= sprintf('<div class="col-sm-6 col-lg-6 col-xl-6 col-md-6 mb-4">
			<div class="card h-100"><div class="card-image">
			<a href="%s"><img class="card-img-top" src="%s" alt=""></a>
			<div class="shape case-shape donation-shape"><a href="%s"><span>%s</span><br>%s</a></div>
			</div><div class="card-body"><p class="card-text">%s <a class="link link--arrowed" href="%s"><svg class="hover-arrow-icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
			<g fill="none" stroke="#5A15FF" stroke-width="1.5" stroke-linejoin="round" stroke-miterlimit="10">
			<circle class="arrow-icon--circle" cx="16" cy="16" r="15.12"></circle>
			<path class="arrow-icon--arrow" d="M 19 10.5 L 25 16 l -6 6.4 M 6 16 h 19"></path>
			</g></svg></a></p>%s</div></div></div>', 
			get_permalink($post->ID),
			wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'medium'),
			get_permalink($post->ID),
			$post->post_title,
			get_field('hash_tags', $post->ID),
			limit_text(strip_tags($post->post_content), 22),
			get_permalink($post->ID),
			$donate_link
		);
	endforeach;
	return $response;
}
add_shortcode('charitys', 'get_charity');
 
add_action('init', function() {
	pll_register_string('hello-world', 'Hello world');
	pll_register_string('page-not-found', 'Page not found');
	pll_register_string('404-content-message', '404-content-message');
	pll_register_string('Top-talent-is-in-high-demand', 'Top talent is in high demand');
	pll_register_string('Top-talent-is-in-high-demand-hire', 'Top talent is in high demand hire');
	pll_register_string('Ready-to-get-started', 'Ready to get started');
	pll_register_string('Book-a-consultation', 'Book a consultation');
	pll_register_string('Let-your-projects-delivered', 'Lets get your projects delivered');
	pll_register_string('footer-about', 'About');
	pll_register_string('footer-contact', 'Contact');
	pll_register_string('footer-social', 'Social');
	pll_register_string('popup-title', 'Join our global community of experts');
	pll_register_string('popup-welcome-to-venquis', 'Welcome to Venquis');
	pll_register_string('popup-details-received', 'Details received');
	pll_register_string('popup-for-showing-interest', 'for showing interest');
	pll_register_string('popup-thanks', 'Thanks');
	pll_register_string('join-first-name', 'First name');
	pll_register_string('join-last-name', 'Last name');
	pll_register_string('join-email', 'Email');
	pll_register_string('join-mobile-number', 'Mobile number');
	pll_register_string('join-start-typing', 'Start typing...');
	pll_register_string('join-job-title', 'Job title');
	pll_register_string('join-your-country', 'Your Country');
	pll_register_string('join-bussiness-sector', 'Bussiness Sector');
	pll_register_string('join-role-type-preference', 'Role type preference');
	pll_register_string('join-contract', 'Contract');
	pll_register_string('join-permanent', 'Permanent');
	pll_register_string('join-both', 'Both');
	pll_register_string('join-upload-CV', 'Upload CV');
	pll_register_string('join-drag-drop-CV-or', 'Drag & drop CV or ');
	pll_register_string('join-button-send', 'Send');
	pll_register_string('join-t-and-c', 'I give consent to Venquis holding my data under the Venquis <a href="#">Terms & Conditions</a>.');
	pll_register_string('join-company-trust-deliver', 'Companies trust Venquis to deliver top talent');
	pll_register_string('validation-first-name', 'Please enter your first name');
	pll_register_string('validation-last-name', 'Please enter your last name');
	pll_register_string('validation-email', 'Please enter a valid email address');
	pll_register_string('validation-mobile', 'Please enter a valid mobile number');
	pll_register_string('validation-job', 'Please enter job title');
	pll_register_string('validation-location', 'Please enter a location');
	pll_register_string('validation-file', 'File should be pdf or doc');
	pll_register_string('validation-full-name', 'Please enter your full name');
	pll_register_string('validation-msg', 'Please add message');
	pll_register_string('join-team-creating', 'Join the team creating positive change');
	pll_register_string('recruitment-experts', 'Talk to one of our recruitment experts');
	pll_register_string('you-might-also-like', 'You might also like');
	pll_register_string('see-all-blogs', 'See all blogs');
	pll_register_string('get-in-Touch', 'Get in Touch');
	pll_register_string('contact-name', 'Name');
	pll_register_string('contact-email', 'Email');
	pll_register_string('contact-enquiry', 'Enquiry (optional)');
	pll_register_string('contact-message', 'Your Message');
	pll_register_string('contact-schedule', 'Schedule a call with us');
	pll_register_string('survey-msg-1', 'Let’s show you the cost of staffing your project, and hiring timescales. Plus all the other tools to make hiring easy');
	pll_register_string('survey-msg-2', 'A few more questions will help us understand your needs better...');
	pll_register_string('survey-msg-3', 'Nearly there...');
	pll_register_string('survey-msg-4', 'Last question...');
	pll_register_string('survey-t-and-c', 'By completing signup, you are agreeing to Venquis <a href="#">Terms of Service</a>, <a href="#">Privacy Policy</a>, and <a href="#">Cookie Policy</a>.');
	pll_register_string('survey-finals', "Success! Let's connect you with the talent you need");
	pll_register_string('careers-meet-you', "We can’t wait to meet you");
	pll_register_string('careers-talk', 'Talk to us');
	pll_register_string('careers-know-us', 'Get to know us');
	pll_register_string('join-out-team', 'Join our team');
	pll_register_string('click-to-browse', 'Click to browse');
	pll_register_string('find-your-place', 'Find your place @Venquis');
	pll_register_string('talent-work', 'Talent @work');
	pll_register_string('join-success-msg', 'for showing interest. We will review your CV and come back to you as soon as possible. <br/><br/>In the meantime, you might find these useful:');
	pll_register_string('hire-solve-hire', 'Solve your <br> hiring challenge');
	pll_register_string('hire-talk-to-recruiter', 'Talk to one of our recruitment experts');
	pll_register_string('hire-access-de', 'Access all developer skillsets');
	pll_register_string('hire-help-perfect-fit', 'How we helped other<br>companies find the perfect fit');
	pll_register_string('hire-find-leading-global', 'Find out why leading global businesses trust Venquis to staff their business transformation projects.');
	pll_register_string('quiz-book-call', 'Book a call to access the talent now');
	pll_register_string('quiz-next-appointment', 'Next available appointment');
	pll_register_string('quiz-book', 'Book');
	pll_register_string('quiz-submit-btn', 'Email me my cost and availability report');
	pll_register_string('quiz-field-company', 'Company Name (optional)');
	pll_register_string('quiz-field-mobile', 'Mobile Number');
	pll_register_string('quiz-details-received', 'Details Recived');
	pll_register_string('quiz-succ-msg', 'We will send you your Cost and Availability Report shortly to');
	pll_register_string('quiz-succ-msg-2', 'Please also check your spam folder in case it got lost there.');
	pll_register_string('quiz-success-msg', 'In the meantime, you might find these useful');
	pll_register_string('careers-all-jobs', 'See All Jobs');
	pll_register_string('home-solve-staffing-challenge', 'Solve your staffing challenge and scale effortlessly');
	pll_register_string('hiring', 'Hiring');
	pll_register_string('looking-for-a-job', 'Looking for a job');
	pll_register_string('please-select', 'Please select');
	pll_register_string('select-your-country', 'Select your country');
	pll_register_string('similar-people', 'Similar People');
	pll_register_string('read-more', 'Read more');
	pll_register_string('all', 'All');
	pll_register_string('apply-now', 'Apply Now');
	pll_register_string('nominate-here', 'Nominate here');
	pll_register_string('donate-here', 'Donate here');
	pll_register_string('exit-form', 'Exit form');
	pll_register_string('book-an-appointment', 'Book an appointment');
	pll_register_string('skip-question', 'Skip question');
	pll_register_string('previous', 'Previous');
	pll_register_string('next-step', 'Next step');
	pll_register_string('most-in-demand-talent', 'Most in demand talent');
	pll_register_string('what-our-clients-say', 'What our clients say');
	pll_register_string('see-all-insights', 'See all insights');
	pll_register_string('trending-insights', 'Trending Insights');
	pll_register_string('more-agile-staffing-solutions', 'More agile<br>staffing solutions');
	pll_register_string('get-started', 'Get Started');
	pll_register_string('see-all', 'See all');
	pll_register_string('You-might-also-like', 'You might also like');
	pll_register_string('teams-word-for-it', 'Take our team’s word for it');
	pll_register_string('Access-all-skillsets', 'Access all %s skillsets');
	pll_register_string('Unsure-what-skills-you-need', 'Unsure what skills you need');
	pll_register_string('You', 'You');
	pll_register_string('for-agile-staffing-solutions', 'Ready for agile staffing solutions');
	pll_register_string('Solve-your-hiring-challenge', 'Solve your hiring challenge');
	pll_register_string('I-am-hiring', "I’m hiring");
	pll_register_string('I-am-job-hunting', "I’m job hunting");
	pll_register_string('Other', 'Other');
	pll_register_string('Want-know-working-at-Venquis', 'Want to know more about working at Venquis');
	pll_register_string('Book-chat-of-our-team', 'Book a chat with one of our team');
	pll_register_string('Next', 'Next');
	pll_register_string('Previous', 'Previous');
	pll_register_string('or', 'OR');
	pll_register_string('book-a-meeting-with', "Book a meeting with");
	pll_register_string('careers', 'Careers');
	pll_register_string('Book-a-call-to-access-talent-now', 'Book a call to access talent now');
	pll_register_string('validation-enquiry', 'Please select enquiry option');
	pll_register_string('register-your-interest', 'Register your interest');
	pll_register_string('hire-button', 'Hire Now');
	pll_register_string('contact-button', 'Contact Us');
	pll_register_string('Lets-get-you-the-talent-you-need', 'Let’s get you the talent you need');
	pll_register_string('Solve-your-staffing-challenge-right-now', 'Solve your staffing challenge right now');
	pll_register_string('Our-work-with-leading-brands', 'Our work with leading brands');
	pll_register_string('Get-advice-from-the-Project-Team-Lead','Get advice from the Project Team Lead');
});

function bullhorn_add_settings_page() {
    add_options_page( 'Bullhorn API Settings', 'Bullhorn API Settings', 'manage_options', 'bullhorn-plugin', 'bullhorn_settings_page' );
}
add_action( 'admin_menu', 'bullhorn_add_settings_page' );

function bullhorn_settings_page() {
    ?>
<h2>Bullhorn API Settings</h2>
<form action="options.php" method="post">
    <?php 
        settings_fields( 'bullhorn_settings_options' );
        do_settings_sections( 'bullhorn_settings' ); ?>
    <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
</form>
<?php
}
function dbi_register_settings() {
	register_setting( 'bullhorn_settings_options', 'bullhorn_settings_options', 'bullhorn_settings_options_validate' );
	add_settings_section( 'api_settings', 'API Settings', 'bullhorn_settings_text', 'bullhorn_settings' );

	add_settings_field( 'bullhorn_api_secret_key', 'Bullhorn Secret key', 'bullhorn_api_secret_key', 'bullhorn_settings', 'api_settings' );
	add_settings_field( 'bullhorn_api_client_id', 'Bullhorn Client ID', 'bullhorn_api_client_id', 'bullhorn_settings', 'api_settings' );
	add_settings_field( 'bullhorn_api_username', 'Bullhorn Username', 'bullhorn_api_username', 'bullhorn_settings', 'api_settings' );
	add_settings_field( 'bullhorn_api_password', 'Bullhorn Password', 'bullhorn_api_password', 'bullhorn_settings', 'api_settings' );
	add_settings_field( 'email_to_on_job_received', 'Emails to', 'email_to_on_job_received', 'bullhorn_settings', 'api_settings' );
	add_settings_field( 'email_to_on_survey_received', 'Email for survey', 'email_to_on_survey_received', 'bullhorn_settings', 'api_settings' );
}
add_action( 'admin_init', 'dbi_register_settings' );

function bullhorn_settings_text() {
    echo '<p>Here you can set all the options for using the API</p>';
}

function bullhorn_api_secret_key() {
    $options = get_option( 'bullhorn_settings_options' );
    echo "<input class='regular-text' id='bullhorn_api_secret_key' name='bullhorn_settings_options[secret_key]' type='text' value='" . esc_attr( $options['secret_key'] ) . "' />";
}

function bullhorn_api_client_id() {
    $options = get_option( 'bullhorn_settings_options' );
    echo "<input class='regular-text' id='bullhorn_api_client_id' name='bullhorn_settings_options[client_id]' type='text' value='" . esc_attr( $options['client_id'] ) . "' />";
}

function bullhorn_api_username() {
    $options = get_option( 'bullhorn_settings_options' );
    echo "<input class='regular-text' id='bullhorn_api_username' name='bullhorn_settings_options[username]' type='text' value='" . esc_attr( $options['username'] ) . "' />";
}
function bullhorn_api_password() {
    $options = get_option( 'bullhorn_settings_options' );
    echo "<input class='regular-text' id='bullhorn_api_password' name='bullhorn_settings_options[password]' type='text' value='" . esc_attr( $options['password'] ) . "' />";
}
function email_to_on_job_received() {
    $options = get_option( 'bullhorn_settings_options' );
    echo "<input class='regular-text' id='bullhorn_api_email_to' name='bullhorn_settings_options[email_to]' type='text' value='" . esc_attr( $options['email_to'] ) . "' />";
}
function email_to_on_survey_received() {
    $options = get_option( 'bullhorn_settings_options' );
    echo "<input class='regular-text' id='bullhorn_api_survey_email' name='bullhorn_settings_options[survey_email]' type='text' value='" . esc_attr( $options['survey_email'] ) . "' />";
}

function getRealIpAddr(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

add_action('init', 'detect_browser_location');
function detect_browser_location(){
	if (!isset($_COOKIE['geo_code'])) {
		$geo = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".getRealIpAddr()));
		$countrycode = $geo->geoplugin_countryCode;
		setcookie('geo_code', $countrycode, time() + (10 * 365 * 24 * 60 * 60), "/");
		if ($countrycode && $countrycode == 'DE') {
			$url = get_bloginfo('url').'/de/';//pll_home_url('DE');
			header("Location: $url");die();
		}
	}
}

function trigger_emails($user_data, $user_id){
	$remove_keys = ['nonce', 'action', 'submit', 'mobile_number', 'agree'];
	foreach($remove_keys as $key){
		unset($user_data[$key]);
	}
	$user_data['enquiry'] = getservices($user_data['enquiry']);
	$field_keys = [
		"firstname" => "First name",
		"lastname" => "Last name",
		"email" => "Email",
		"full_number" => "Mobile number",
		"job_title" => "Job title",
		"location" => "Location",
		"role_type" => "Role type",
		"enquiry" => "Bussiness Sector"
	];

	foreach($field_keys as $key => $value){
		$user_data[$value] = $user_data[$key];
		unset($user_data[$key]);
	}

	$resume = wp_get_attachment_url(get_user_meta($user_id, 'resume', FALSE)[0]);
	if ($resume){
		$user_data['Resume'] = $resume;
		$user_data['User profile'] = '<a href="'.home_url().'/wp-admin/user-edit.php?user_id='.$user_id.'">Link</a>';
	}
	$logo_url = get_template_directory_uri();
	ob_start();
	include (get_template_directory() . '/email/job_request.php');
	$email_content = ob_get_contents();
	ob_end_clean();
	$options = get_option( 'bullhorn_settings_options' );
	if(@$options['email_to']){
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail($options['email_to'], "Job Application details", $email_content, $headers);
	}
}

add_action( 'wp_ajax_surveyform', 'surveyform' );
add_action( 'wp_ajax_nopriv_surveyform', 'surveyform' );
function surveyform()
{
	$nonce  = $_POST['nonce'];
	if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
		die(json_encode(['message' => 'Token as expired please refresh the page.']));
	}
	$post_id  = $_POST['post_id'];
	if (get_post($post_id)) {
		add_post_meta($post_id, 'first_name', $_POST['firstname']);
		add_post_meta($post_id, 'last_name', $_POST['lastname']);
		add_post_meta($post_id, 'email', $_POST['email']);
		add_post_meta($post_id, 'mobile_number', $_POST['full_number']);
		add_post_meta($post_id, 'company_name', $_POST['companyname']);

		$hiring_for = get_field("hiring_for", $post_id);
		if (is_array($hiring_for)){
			$hiring_for = implode(",", $hiring_for);
		}
		$talent_do_you_need = get_field("what_type_of_talent_do_you_need", $post_id);
		if (is_array($talent_do_you_need)){
			$talent_do_you_need = implode(",", $talent_do_you_need);
		}

		$user_data = array(
			'Name' => $_POST['firstname']." ".$_POST['lastname'],
			'Email' => $_POST['email'],
			'Mobile Number' => $_POST['full_number'],
			'Company name' => $_POST['companyname'],
			'How quickly do you need to initiate this change' => get_field("how_quickly_do_you_need_to_initiate_this_change", $post_id),
			'What type of service do you need' => get_field("service_needed", $post_id),
			'And what type of project you are hiring for' => $hiring_for,
			'What type of talent do you need' => $talent_do_you_need,
			'OK, and what skills do you need' => get_field("tags", $post_id),
			'What type of staff contracts do you prefer' => get_field("staff_you_prefer", $post_id),
			'LINK' => '<a href="'.home_url().'/wp-admin/post.php?action=edit&post='.$post_id.'">Survey Link</a>'
		);
	
		$logo_url = get_template_directory_uri();
		ob_start();
		include (get_template_directory() . '/email/survey.php');
		$email_content = ob_get_contents();
		ob_end_clean();
		
		$options = get_option( 'bullhorn_settings_options' );
		if(@$options['survey_email']){
			$headers = array('Content-Type: text/html; charset=UTF-8');
			wp_mail($options['survey_email'], "Survey details", $email_content, $headers);
		}
		die(json_encode(["email" => $_POST['email']]));
	} else {
		die(json_encode(["message" => sprintf("Not a valid request please refresh the page.")]));
	}
}

function getservices($service_id){
	$services = [
		1100148 => "Aerospace & Defence",
		1100149 => "Agriculture",
		1100150 => "Asset Management",
		1100001 => "Automotive",
		1100151 => "Broadcasting",
		1100029 => "Computer Software",
		1100152 => "Consultancy",
		1100153 => "Cyber and Digital",
		1100154 => "Energy&Utilities",
		1100155 => "Engineering",
		1100005 => "Financial Services",
		1100156 => "Fintech",
		1100157 => "FMCG",
		1100158 => "Healthcare & Pharmaceuticals",
		1100159 => "Hedge Funds",
		1100066 => "Information Technology and Services",
		1100067 => "Insurance",
		1100160 => "Insurtech",
		1100071 => "Investment Banking",
		1100161 => "Logistics"
	];
	if(array_key_exists($service_id, $services)){
		return $services[$service_id];
	}
	return 'Logistics';
}

function add_acf_menu_pages()
{
    acf_add_options_page(array(
        'page_title' => 'Theme options',
        'menu_title' => 'Theme options',
        'menu_slug' => 'theme-options',
        'capability' => 'manage_options',
        'position' => 61.1,
        'redirect' => true,
        'icon_url' => 'dashicons-admin-customizer',
        'update_button' => 'Save options',
        'updated_message' => 'Options saved',
    ));
}
add_action('acf/init', 'add_acf_menu_pages');

add_action( 'wp_ajax_bookingtime', 'bookingtime' );
add_action( 'wp_ajax_nopriv_bookingtime', 'bookingtime' );
function bookingtime()
{
	$post_id  = $_POST['post_id'];
	if (get_post($post_id)) {
		add_post_meta($post_id, 'bookingtime', $_POST['bookingtime']);
		die(json_encode(["message" => "Successfully created an appointment"]));
	} else {
		die(json_encode(["message" => "Not a valid request please refresh the page."]));
	}
}

/**
 * Removes the default scheduled event used to delete old export files.
 */
remove_action( 'init', 'wp_schedule_delete_old_privacy_export_files' );

/**
 * Removes the hook attached to the default scheduled event for removing
 * old export files.
 */
remove_action( 'wp_privacy_delete_old_export_files', 'wp_privacy_delete_old_export_files' );
add_action('wp_privacy_delete_old_export_files', 'wp_privacy_delete_old_export_files_new');

function wp_privacy_delete_old_export_files_new(){
	return TRUE;
}


add_filter( 'upload_mimes', 'my_myme_types', 1, 1 );
function my_myme_types( $mime_types ) {
  $mime_types['svg'] = 'image/svg+xml';     // Adding .svg extension
  $mime_types['json'] = 'application/json'; // Adding .json extension
  
  return $mime_types;
}
@ini_set( 'upload_max_size' , '10M' );
@ini_set( 'post_max_size', '10M');
@ini_set( 'max_execution_time', '300' );


function casestudy_category_single_template( $single_template ) {
	  global $post;
	  $terms = wp_get_post_terms( $post->ID, 'casestudy_categories');
	 
	  $term  = wp_list_pluck( $terms, 'slug' );
		   if (in_array('campaign',$term))  {
			$single_template = get_template_directory() . '/single-campaign.php';
			}
			return $single_template;						   	
}
add_filter( 'single_template', 'casestudy_category_single_template');
