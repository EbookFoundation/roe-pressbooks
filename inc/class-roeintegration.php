<?php

namespace ROE;

use Pressbooks\Modules\Export\Export;

class ROEIntegration extends Export {
	const VERSION = '1.0.0';
	protected $plugin_slug = 'roe-pressbooks';

	function __construct ( array $args ) {
		$this->bookTitle = get_bloginfo( 'name' );
		$this->bookMeta = \Pressbooks\Book::getBookInformation();
	}

	/**
	 * Return whether the plugin is usable.
	 * @since    1.0.0
	 * @return   presence of both key and secret.
	 */
	public static function is_active () {
		return get_site_option('roe_pressbooks_key') && get_site_option('roe_pressbooks_secret');
	}

	/**
	 * Create $output
	 * @return bool
	 */
	function convert () {
		/*$output = $this->transform(true);
		$this->output = $output;
		error_log(print_r($output, true));*/

		$siteurl = get_site_url(get_current_blog_id());
		$identifier = isset($this->bookMeta['pb_print_isbn']) ? $this->bookMeta['pb_print_isbn'] : "url:md5:".md5($siteurl);
		$timestamp = (new \DateTime())->format('c');
		$output = [
			"metadata" => [
				"@type" => "http://schema.org/Book",
				"title" => $this->bookMeta['pb_title'],
				"author" => $this->bookMeta['pb_authors'],
				"identifier" => $identifier,
				"publisher" => $siteurl,
				"language" => $this->bookMeta['pb_language'],
				"modified" => $timestamp
			],
			"links" => [],
			"images" => [
				[
					"href" => $this->bookMeta['pb_cover_image']
				]
			]
		];
		$this->output = $output;

		return $this->send();
	}

	/**
	 * Check the sanity of $this->output
	 *
	 * @return bool
	 */
	function validate () {
		return true;
	}

	function transform ($return = false) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( __( 'Invalid permission error', 'pressbooks' ) );
		}

		static $buffer;
		if ( ! function_exists( 'wxr_cdata' ) ) {
			ob_start();
			require_once( ABSPATH . 'wp-admin/includes/export.php' );
			@export_wp(); // @codingStandardsIgnoreLine
			$buffer = ob_get_clean();
		}
		if ( $return ) {
			return $buffer;
		} else {
			echo $buffer;
			return null;
		}
	}

	function send () {
		$url = ROE_BASE_URL . "/api/publish";
		$content = json_encode($this->output);
		$headers = join("\r\n", [
			"roe-key: " . get_site_option('roe_pressbooks_key'),
			"roe-secret: " . get_site_option('roe_pressbooks_secret'),
			"Content-Type: application/json",
		]);
		$response = wp_remote_post($url, [
			"headers" => $headers,
			"body" => $content
		]);

		if ( is_wp_error($response) || $response['response']['code'] !== 200 ) {
			return false;
		}

		return true;
	}
}

