<?php

namespace ROE;

class ROEIntegration {
	const VERSION = '1.0.0';
	protected $plugin_slug = 'roe-pressbooks';

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected static $instance = null;

	private function __construct () {

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance () {
		// If the single instance hasn't been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Returns merged array of all ROE user options
	 * @since 1.0.2
	 * @return array
	 */
	private function getUserOptions () {
		$other = get_option('roe_other_settings', []);
		$result = @array_merge($other);

		return $result;
	}

	/**
	 * Fired when the plugin is activated.
	 * @since    1.0.0
	 */
	function activate () {
		if (!current_user_can('activate_plugins')) {
			return;
		}
		add_site_option('roe-pressbooks-activated', true);
	}

	/**
	 * Fired when the plugin is deactivated.
	 * @since    1.0.0
	 */
	function deactivate () {
		if (!current_user_can('activate_plugins')) {
			return;
		}
		delete_site_option('roe-pressbooks-activated');
	}

	/**
	 * Return the plugin slug.
	 * @since    1.0.0
	 * @return    Plugin slug variable.
	 */
	function getPluginSlug () {
		return $this->plugin_slug;
	}
}

