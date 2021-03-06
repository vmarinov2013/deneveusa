<?php
require_once(TEMPLATEPATH.'/lib/init.php'); // Start Genesis Engine
require_once(STYLESHEETPATH.'/lib/init.php'); // Start Themedy Options

require_once(STYLESHEETPATH.'/custom/custom_breadcrumb.php');

add_theme_support( 'woocommerce' );

// Add WP Background Image Support
add_theme_support( 'custom-background', array(
	// Background color default
	'default-color' => 'FFFFFF',
	// Background image default
	'default-image' => get_stylesheet_directory_uri() . '/images/bg-body.png'
) );

// Add WP Header Image Support
add_theme_support( 'custom-header', array(
	// Header image default
	'default-image'			=> get_stylesheet_directory_uri() . '/images/header.png',
	// Header text display default
	'header-text'			=>  true,
	// Header text color default
	'default-text-color'		=> '333',
	// Header image width (in pixels)
	'flex-width'        => true,
	'width'				=> 230,
	// Header image height (in pixels)
	'flex-height'       => true,
	'height'			=> 80,
	// Header image random rotation default
	'random-default'		=> false,
	// Template header style callback
	'wp-head-callback'		=> 'header_style',
	// Admin header style callback
	'admin-head-callback'		=> 'admin_header_style',
	// Admin preview style callback
	'admin-preview-callback'	=> ''
) );

add_filter( 'genesis_seo_title', 'child_header_title', 10, 3 );

function child_header_title( $title, $inside, $wrap ) {
    $inside = sprintf( '<a href="http://deneveusa.com" title="Home Page">%s</a>', esc_attr( get_bloginfo( 'name' ) ), get_bloginfo( 'name' ) );
    return sprintf( '<%1$s id="title">%2$s</%1$s>', $wrap, $inside );
}

function header_style() {

	 $text_color = get_theme_mod( 'header_textcolor', get_theme_support( 'custom-header', 'default-text-color' ) );

	?><style type="text/css">
		.header-image #header #title-area {
			padding-left: 0;
			width: <?php echo get_custom_header()->width; ?>px;
            height: <?php echo get_custom_header()->height; ?>px;

		}
		#title-area #description, #title-area #title a, #title-area #title a:hover {
			color: #<?php echo $text_color; ?>;
		}
    </style><?php
}

function admin_header_style() {
	?><style type="text/css">
        #headimg {
            width: <?php echo get_custom_header()->width; ?>px;
            height: <?php echo get_custom_header()->height; ?>px;
        }
    </style><?php
}

// Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

// Add our styles and scripts
add_action('wp_enqueue_scripts', 'themedy_load_scripts');
function themedy_load_scripts() {
	wp_enqueue_style('themedy-child-theme-style', STYLES_URL.'/'.themedy_get_option('style').'.css',array(CHILD_THEME_CSS_DEPENDENCY),CHILD_THEME_VERSION,'screen');
	if (themedy_get_option('custom')) wp_enqueue_style('themedy-child-theme-custom-style', CHILD_URL.'/custom/custom.css',array(CHILD_THEME_CSS_DEPENDENCY),CHILD_THEME_VERSION,'screen');
	if (themedy_get_option('mobile_menu')) wp_enqueue_script('mobile_menu', CHILD_THEME_LIB_URL.'/js/jquery.mobilemenu.js', array('jquery'), '1.0', FALSE);
}

// Add custom body class if using custom.css stylesheet
if (themedy_get_option('custom')) {
	add_filter('body_class', 'add_custom_class');
	function add_custom_class($classes) {
		$classes[] = 'custom';
		return $classes;
	}
}

// Viewport scale (iPhone)
add_action('genesis_meta','themedy_viewport_meta');
function themedy_viewport_meta() {
	?>
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
    <?php
}

// Remove Header Widget Area
unregister_sidebar( 'header-right' );

// Remove the Secondary Nav option
add_theme_support( 'genesis-menus', array( 'primary' => 'Primary Navigation Menu' ) );

// Add the Nav Above Header
remove_action('genesis_after_header', 'genesis_do_nav');
add_action('genesis_header', 'genesis_do_nav');

// No Secondary Nav
remove_action('genesis_after_header', 'genesis_do_subnav');

// Breadcrumbs / Search area after header
remove_action('genesis_before_loop', 'genesis_do_breadcrumbs'); // Remove Breadcrumbs by default
add_action('genesis_after_header','themedy_secondary_area');
function themedy_secondary_area() { ?>
	<div id="secondary_area">
    	<div class="wrap">
        	<?php if (is_front_page()) {  ?>
                <p class="homepage_tag"><?php echo themedy_option('homepage_breadcrumb_text'); ?></p>
            <?php } else { ?>
				<div class="breadcrumb_wrap">
                    <?php custom_do_breadcrumbs(); ?>
                </div>
			<?php } ?>
            <div id="search_area">
            	<form method="get" class="search_form" action="<?php echo bloginfo('url'); ?>">
                    <p>
                    <input class="text_input" type="text" value="To search, type and hit enter..." name="s" id="s" onfocus="if (this.value == 'To search, type and hit enter...') {this.value = '';}" onblur="if (this.value == '') {this.value = 'To search, type and hit enter...';}" />
                    <input type="hidden" id="searchsubmit" value="Search" />
                    </p>
        		</form>
            </div>
        </div>
    </div>
<?php
}

// Breadcrumbs Customization
add_filter( 'genesis_breadcrumb_args', 'themedy_breadcrumb_args' );
function themedy_breadcrumb_args( $args ) {
    $args['home']                    = 'Home';
    $args['sep']                     = '</div><div class="crumb">';
    $args['list_sep']                = ', '; // Genesis 1.5 and later
    $args['prefix']                  = '<div class="breadcrumb">';
    $args['suffix']                  = '</div></div>';
    $args['labels']['prefix']        = '<div class="crumb crumb_home">';
    $args['labels']['author']        = 'Archives for ';
    $args['labels']['category']      = 'Archives for '; // Genesis 1.6 and later
    $args['labels']['tag']           = 'Archives for ';
    $args['labels']['date']          = 'Archives for ';
    $args['labels']['search']        = 'Search for ';
    $args['labels']['tax']           = '';
    $args['labels']['post_type']     = '';
    $args['labels']['404']           = ''; // Genesis 1.5 and later
    return $args;
}

// Product Sidebar 1
function themedy_do_product_sidebar() {
	if ( !dynamic_sidebar('primary-product-sidebar') ) {

		echo '<div class="widget widget_text"><div class="widget-wrap">';
			echo '<h4 class="widgettitle">';
				_e('Primary Product Sidebar', 'themedy');
			echo '</h4>';
			echo '<div class="textwidget"><p>';
				printf(__('This is the Primary Product Sidebar. You can add content to this area by visiting your <a href="%s">Widgets Panel</a> and adding new widgets to this area.', 'genesis'), admin_url('widgets.php'));
			echo '</p></div>';
		echo '</div></div>';

	}
}

// Product Sidebar 2
function themedy_do_product_sidebar_alt() {
	if ( !dynamic_sidebar('secondary-product-sidebar') ) {

		echo '<div class="widget widget_text"><div class="widget-wrap">';
			echo '<h4 class="widgettitle">';
				_e('Secondary Product Sidebar', 'themedy');
			echo '</h4>';
			echo '<div class="textwidget"><p>';
				printf(__('This is the Secondary Product Sidebar. You can add content to this area by visiting your <a href="%s">Widgets Panel</a> and adding new widgets to this area.', 'genesis'), admin_url('widgets.php'));
			echo '</p></div>';
		echo '</div></div>';

	}
}

// If Using WooCommerce, Add Products Sidebars
add_action('wp_head','themedy_woo_product_sidebars');
function themedy_woo_product_sidebars() {
	if (function_exists(is_woocommerce)) {
		if (is_woocommerce()) {
			remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
			remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );
			add_action( 'genesis_sidebar', 'themedy_do_product_sidebar' );
			add_action( 'genesis_sidebar_alt', 'themedy_do_product_sidebar_alt' );
		}
	}
}

// Add Footer Menu
add_action('genesis_footer','themedy_footer_menu');
function themedy_footer_menu() { ?>
		<div class="footer_menu">
        	<?php
        	if (function_exists('wp_nav_menu')) {
				wp_nav_menu(array(
					'theme_location' => 'footer',
					'container' => 'div',
					'menu_class' => 'menu footer-nav',
					'fallback_cb' => 0
				));
			}
			?>
        </div>
<?php }
register_nav_menu('footer', __('Footer Menu', 'themedy'));

// Customizes Footer Text (set in options)
if (themedy_get_option('footer')) {
	add_filter('genesis_footer_creds_text', 'custom_footer_creds_text');
	function custom_footer_creds_text($creds) {
    	$creds = themedy_get_option('footer_text');
    return $creds;
	}
}

// Removes go to top text
add_filter('genesis_footer_backtotop_text', 'footer_backtotop_filter');
function footer_backtotop_filter($backtotop) {
    $backtotop = '';
    return $backtotop;
}

// Register Sidebars
genesis_register_sidebar(array(
	'name' => 'Primary Product Sidebar',
	'id' => 'primary-product-sidebar',
	'description' => 'This is the primary product sidebar shown on product pages.',
));
genesis_register_sidebar(array(
	'name' => 'Secondary Product Sidebar',
	'id' => 'secondary-product-sidebar',
	'description' => 'This is the secondary product sidebar shown on product pages.',
));
genesis_register_sidebar(array(
	'name' => 'Homepage Widget Area 1',
	'id' => 'home-sidebar-1',
	'description' => 'This is sidebar 1 on the homepage.',
	'before_widget' =>  '<div id="%1$s" class="widget %2$s">',
	'after_widget' =>  '</div>',
	'before_title' => '<h4>',
	'after_title' => '</h4>'
));
genesis_register_sidebar(array(
	'name' => 'Homepage Widget Area 2',
	'id' => 'home-sidebar-2',
	'description' => 'This is sidebar 2 on the homepage.',
	'before_widget' =>  '<div id="%1$s" class="widget %2$s">',
	'after_widget' =>  '</div>',
	'before_title' => '<h4>',
	'after_title' => '</h4>'
));

if (is_file(CHILD_DIR.'/custom/custom_functions.php')) { include(CHILD_DIR.'/custom/custom_functions.php'); } // Include Custom Functions

//* Do NOT include the opening php tag
 
//* Customize the credits
add_filter( 'genesis_footer_creds_text', 'sp_footer_creds_text' );
function sp_footer_creds_text() {
	echo '<div class="creds"><p>';
	echo 'Copyright &copy; ';
	echo date('Y');
	echo ' &middot; <a href="http://deneveusa.com">DENEVE</a> &middot; All Rights Reserved';
	echo '</p></div>';
}


//* Make logo clickable

remove_action( 'genesis_header', 'genesis_do_header' );
add_action( 'genesis_header', 'new_do_header' );
function new_do_header() {

    $header_img = esc_url( get_header_image() );
    echo '<a href="' . site_url() . '">';
    genesis_markup( array(
        'html5'   => '<img %s>',
        'xhtml'   => '<img id="title-area" src="' . $header_img . '"/>',
        'context' => 'title-area',
    ) );
    
    do_action( 'genesis_site_title' );
    //do_action( 'genesis_site_description' );
    echo '</a>';
    if ( is_active_sidebar( 'header-right' ) || has_action( 'genesis_header_right' ) ) {
        genesis_markup( array(
            'html5'   => '<aside %s>',
            'xhtml'   => '<div class="widget-area">',
            'context' => 'header-widget-area',
        ) );

            do_action( 'genesis_header_right' );
            add_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
            dynamic_sidebar( 'header-right' );
            remove_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );

        genesis_markup( array(
            'html5' => '</aside>',
            'xhtml' => '</div>',
        ) );
    }

}

function excerpt_paragraph($html, $max_char = 50, $trail='...' )
{

    // temp var to capture the p tag(s)
    $matches= array();
    if ( preg_match( '/<p>[^>]+<\/p>/', $html, $matches) )
    {
        // found <p></p>
        $p = strip_tags($matches[0]);
    } else {
        $p = strip_tags($html);
    }
    //shorten without cutting words
    $p = short_str($p, $max_char );
    
    // remove trailing comma, full stop, colon, semicolon, 'a', 'A', space
    $p = rtrim($p, ',.;: aA' );

    // return nothing if just spaces or too short
    if (ctype_space($p) || $p=='' || strlen($p)<10) { return ''; }

    //return '<p>'.$p.$trail.'</p>';
    return $p.$trail;
}
//

/**
* shorten string but not cut words
* 
**/

function short_str( $str, $len, $cut = false )
{
    if ( strlen( $str ) <= $len ) { return $str; }
    $string = ( $cut ? substr( $str, 0, $len ) : substr( $str, 0, strrpos( substr( $str, 0, $len ), ' ' ) ) );
    return $string;
}