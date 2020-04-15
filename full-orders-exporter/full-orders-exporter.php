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

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	// WooCommerce is not available.
	exit;
}

add_action('admin_menu', 'register_full_order_exporters_menu');
function register_full_order_exporters_menu() {
	$hook_name = add_submenu_page('woocommerce', 'Full Orders Exporter', 'Full Export Orders', 'view_woocommerce_reports', 'full-orders-exporters', 'full_order_exporters_page');
	function full_order_exporters_page() {
		// check user capabilities
		if (!current_user_can('view_woocommerce_reports')) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?= esc_html(get_admin_page_title()); ?></h1>
			<form action="<?php menu_page_url('full-orders-exporters') ?>" method="post">
				<button type="submit" class="button">Export Now</button>
			</form>
		</div>
		<?php
	}

	add_action( 'load-' . $hook_name, 'full_order_exporters_page_submit' );
	function full_order_exporters_page_submit() {
		$logger = wc_get_logger();
		$logger->info('Export request received');
		$orders = wc_get_orders([]);
		foreach ($orders as $order) {
			$logger->debug('Order #' . $order->get_order_number() . ': ' . json_encode($order->get_data()));
		}
	}
}