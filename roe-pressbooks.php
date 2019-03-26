<?php
/*
Plugin Name: River of Ebooks for Pressbooks
Plugin URI: https://github.com/villa7/roe-pressbooks
Description: RoE integration with Pressbooks
Version: 1.0.0
Author: Free Ebook Foundation
Author URI: https://ebookfoundation.org/
Requires PHP: 7.0
Pressbooks tested up to: 5.4.1
Text Domain: roe-pressbooks
License: GPL v3 or later
Network: True
*/

define ('ROE_BASE_URL', 'http://ec2-18-219-223-27.us-east-2.compute.amazonaws.com');

// -------------------------------------------------------------------------------------------------------------------
// Check requirements
// -------------------------------------------------------------------------------------------------------------------
if ( ! function_exists( 'pb_meets_minimum_requirements' ) && ! @include_once( WP_PLUGIN_DIR . '/pressbooks/compatibility.php' ) ) { // @codingStandardsIgnoreLine
	add_action('admin_notices', function () {
		echo '<div id="message" class="error fade"><p>' . __( 'Cannot find Pressbooks install.', 'roe-pressbooks' ) . '</p></div>';
	});
	return;
} elseif ( ! pb_meets_minimum_requirements() ) {
	return;
}

// -------------------------------------------------------------------------------------------------------------------
// Class autoloader
// -------------------------------------------------------------------------------------------------------------------
\HM\Autoloader\register_class_path( 'ROE', __DIR__ . '/inc' );

// -------------------------------------------------------------------------------------------------------------------
// Composer autoloader
// -------------------------------------------------------------------------------------------------------------------
 if ( ! class_exists( '\ROE\ROEIntegration' ) ) {
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	} else {
		$title = __( 'Dependencies Missing', 'roe-pressbooks' );
		$body = __( 'Please run <code>composer install</code> from the root of the River of Ebooks for Pressbooks plugin directory.', 'roe-pressbooks' );
		$message = "<h1>{$title}</h1><p>{$body}</p>";
		wp_die( $message, $title );
	}
}

// -------------------------------------------------------------------------------------------------------------------
// Check for updates
// -------------------------------------------------------------------------------------------------------------------
if ( ! \Pressbooks\Book::isBook() ) {
	$updater = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/villa7/roe-pressbooks/',
		__FILE__,
		'roe-pressbooks'
	);
	$updater->setBranch( 'master' );
}

function roe_check_compatibility () {
	if ( is_plugin_active('roe-pressbooks/roe-pressbooks.php') && is_network_admin() ) {
		if ( ! \ROE\ROEIntegration::is_active() ) {
			add_action( 'network_admin_notices', '_roe_show_set_config' );
		}
	}
}

function _roe_show_set_config () {
	echo '<div class="notice notice-warning"><p style="display:inline-block;height:40px;line-height:40px">';
  _e('Please configure your site\'s publisher id and secret. It is required to publish to the River of Ebooks.', 'roe-pressbooks');
	echo '</p><a class="button" style="float:right;height:30px;margin:15px 0;" href="#">';
	_e('Settings', 'roe-pressbooks');
	echo '</a></div>';
}

roe_check_compatibility();

add_filter( 'pb_active_export_modules', function ( $modules ) {
	if ( isset( $_POST['export_formats']['roe'] ) && \ROE\ROEIntegration::is_active() ) {
	  $modules[] = '\ROE\ROEIntegration';
	}
	return $modules;
} );

add_filter( 'pb_export_formats', function ( $formats ) {
	if (\ROE\ROEIntegration::is_active()) {
		$formats['exotic']['roe'] = __( 'Send to River of Ebooks', 'pressbooks' );
	}
	return $formats;
} );

if (is_network_admin()) {
	new \ROE\Admin\ROEAdmin;
}
