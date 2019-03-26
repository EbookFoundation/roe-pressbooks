<?php

namespace ROE\Admin;

class ROEAdmin {
	function __construct () {
		add_action('network_admin_menu', [$this, '_roe_admin_menu']);
		add_action('network_admin_edit_roepressbooksaction', [$this, '_roe_save_settings']);
	}

	function _roe_admin_menu () {
		add_submenu_page('settings.php', 'RoE Options', 'River of Ebooks', 'manage_network_options', 'roe-pressbooks', [$this, '_roe_plugin_options']);
	}

	function _roe_plugin_options () {
		if (!current_user_can('manage_network_options'))  {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		require_once(__DIR__ . '/template.php');
	}

	function _roe_save_settings () {
		check_admin_referer('roe-validate'); // nonce security check

		update_site_option('roe_pressbooks_key', $_POST['roe_pressbooks_key']);
		update_site_option('roe_pressbooks_secret', $_POST['roe_pressbooks_secret']);

		wp_redirect(add_query_arg(array(
			'page' => 'roe-pressbooks',
			'updated' => true), network_admin_url('settings.php')
		));
		exit;
	}
}

