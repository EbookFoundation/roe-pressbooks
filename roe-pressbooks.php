<?php
/*
Plugin Name: River of Ebooks for Pressbooks
Plugin URI: https://pressbooks.org
Description: RoE integration with Pressbooks
Version: 0.0.1
Author: Pressbooks (Book Oven Inc.)
Author URI: https://pressbooks.org
Requires PHP: 7.0
Pressbooks tested up to: 5.4.1
Text Domain: roe-pressbooks
License: GPL v3 or later
Network: True
*/

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
