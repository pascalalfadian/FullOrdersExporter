<?php
/**
 * Plugin Name: Full Orders Exporter
 * Description: WooCommerce Plugin to export orders, complete with their order meta and item meta.
 * Version: 1.0
 * Author: Pascal Alfadian Nugroho
 * Author URI: https://informatika.unpar.ac.id/dosen/pascal
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: full-orders-exporter
 * WC requires at least: 4.0.0
 * WC tested up to: 4.0.1
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('admin_menu', 'register_full_order_exporters_menu');

function register_full_order_exporters_menu() {
	if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
		add_submenu_page('woocommerce', 'Full Orders Exporter', 'Full Export Orders', 'manage_options', 'full-orders-exporters', 'full_order_exporters_page'); // TODO capability should be something like "export orders or see orders"
	}

	function full_order_exporters_page() {
		// check user capabilities
		if (!current_user_can('manage_options')) { // TODO find correct capabilities
			return;
		}
		?>
		<div class="wrap">
			<h1><?= esc_html(get_admin_page_title()); ?></h1>
			<p>The quick brown fox jumps over the lazy dog.</p>
		</div>
		<?php
	}
}