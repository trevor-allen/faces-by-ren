<?php
/**
* _s functions and definitions.
*
* @link https://developer.wordpress.org/themes/basics/theme-functions/
*
* @package _s
*/

if ( ! function_exists( '_s_setup' ) ):
	/**
	* Sets up theme defaults and registers support for various WordPress features.
	*
	* Note that this function is hooked into the after_setup_theme hook, which
	* runs before the init hook. The init hook is too late for some features, such
	* as indicating support for post thumbnails.
	*/
	function _s_setup() {
		/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on _s, use a find and replace
		* to change '_s' to the name of your theme in all the template files.
		*/
		load_theme_textdomain( '_s', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
		add_theme_support( 'title-tag' );

		/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary', '_s' ),
			'footer-menu' => esc_html__( 'Footer Menu', '_s' ),
			'sidebar-menu' => esc_html__( 'Sidebar Menu', '_s' )
			) );

			/*
			* Switch default core markup for search form, comment form, and comments
			* to output valid HTML5.
			*/
			add_theme_support( 'html5', array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			) );

			/*
			* Enable support for Post Formats.
			* See https://developer.wordpress.org/themes/functionality/post-formats/
			*/
			add_theme_support( 'post-formats', array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
			) );

			// Set up the WordPress core custom background feature.
			add_theme_support( 'custom-background', apply_filters( '_s_custom_background_args', array(
				'default-color' => 'ffffff',
				'default-image' => '',
			) ) );
		}
	endif;
	add_action( 'after_setup_theme', '_s_setup' );

	/**
	* Set the content width in pixels, based on the theme's design and stylesheet.
	*
	* Priority 0 to make it available to lower priority callbacks.
	*
	* @global int $content_width
	*/
	function _s_content_width() {
		$GLOBALS['content_width'] = apply_filters( '_s_content_width', 640 );
	}
	add_action( 'after_setup_theme', '_s_content_width', 0 );

	/**
	* Register widget area.
	*
	* @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	*/
	function _s_widgets_init() {
		register_sidebar(array(
			'name'          => esc_html__( 'Sidebar', '_s' ),
			'id'            => 'sidebar-1',
			'description'   => '',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		));
	}
	add_action( 'widgets_init', '_s_widgets_init' );

	/**
	* Enqueue scripts and styles.
	*/
	function _s_scripts() {
		wp_enqueue_style( '_s-style', get_stylesheet_uri(), false, filemtime( get_stylesheet_uri() ) );

		wp_enqueue_script( '_s-navigation', get_template_directory_uri() . '/js/navigation.js', array(), filemtime( get_template_directory_uri() . '/js/navigation.js' ), true );

		wp_enqueue_script( '_s-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), filemtime( get_template_directory_uri() . '/js/skip-link-focus-fix.js' ), true );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
	add_action( 'wp_enqueue_scripts', '_s_scripts' );

	/**
	* Kill the automatic "p" when editing pages.
	*/
	remove_filter( 'the_content', 'wpautop' );

	/**
	* Add additional image size(s)
	* Mostly 16:9 aspect ratios because they're easy to use on the web.
	*/
	add_image_size( 'landscape', 1024, 512 );
	add_image_size( 'portrait', 800, 1200 );
	add_image_size( 'fwvga', 854, 480 );
	add_image_size( '720p', 1280, 720 );
	add_image_size( '1080p', 1920, 1080 );

	add_filter( 'image_size_names_choose', 'custom_sizes' );
	function custom_sizes( $sizes ) {
		return array_merge( $sizes, array(
			'landscape' => __( 'Landscape (1024x512)' ),
			'portrait' => __( 'Portrait (800x1200)' ),
			'fwvga' => __( 'FWVGA (854x480)' ),
			'720p' => __( '720p (1280×720)' ),
			'1080p' => __( '1080p (1920x1080)' )
			)
		);
	}

	/**
	* Remove emojis
	*/
	function disable_wp_emojicons() {
		// all actions related to emojis
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		// filter to remove TinyMCE emojis
		add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
	}
	add_action( 'init', 'disable_wp_emojicons' );

	function disable_emojicons_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}

	/**
	* Implement the Custom Header feature.
	*/
	// require get_template_directory() . '/inc/custom-header.php';

	/**
	* Custom template tags for this theme.
	*/
	// require get_template_directory() . '/inc/template-tags.php';

	/**
	* Custom functions that act independently of the theme templates.
	*/
	// require get_template_directory() . '/inc/extras.php';

	/**
	* Customizer additions.
	*/
	// require get_template_directory() . '/inc/customizer.php';

	/**
	* Implement a Custom Walker Nav Menu.
	*/
	// require get_template_directory() . '/inc/walker-nav-menu.php';

	/**
	* includes
	*
	* The $_s_includes array determines the code library included in your theme.
	* Add or remove files to the array as needed. Supports child theme overrides.
	*
	* Please note that missing files will produce a fatal error.
	*
	* @link https://github.com/roots/sage/pull/1042
	*/
	$_s_includes = [
		'inc/custom-header.php',    // Implement the Custom Header feature.
		'inc/template-tags.php',    // Custom template tags for this theme.
		'inc/extras.php',     		// Custom functions that act independently of the theme templates.
		'inc/customizer.php',    	// Customizer additions.
		'inc/walker-nav-menu.php',  // Implement a Custom Walker Nav Menu.
	];
	foreach ($_s_includes as $file) {
		if (!$filepath = locate_template($file)) {
			trigger_error(sprintf(__('Error locating %s for inclusion'), $file), E_USER_ERROR);
		}
		require_once $filepath;
	}
	unset($file, $filepath);
