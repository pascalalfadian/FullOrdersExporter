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
		$orders = wc_get_orders([]);
		$meta_keys = [];
		foreach ($orders as $order) {
			foreach ($order->get_items() as $item) {
				foreach ($item->get_meta_data() as $meta_data) {
					$meta_keys[$meta_data->key] = TRUE;
				}
			}
		}
		?>
		<div class="wrap">
			<h1><?= esc_html(get_admin_page_title()); ?></h1>
			<form action="<?php menu_page_url('full-orders-exporters') ?>" method="post">
				<button type="submit" class="button">Export Now</button>
			</form>
			<code>
				DEBUG: <?= htmlspecialchars(json_encode($meta_keys)) ?>
			</code>
		</div>
		<?php
	}

	add_action( 'load-' . $hook_name, 'full_order_exporters_page_submit' );
	function full_order_exporters_page_submit() {
		if ('POST' === $_SERVER['REQUEST_METHOD']) {
			$logger = wc_get_logger();
			$logger->info('Export request received');
			$orders = wc_get_orders([]);
			header('Content-type: text/csv');
			$fp = fopen('php://output', 'w');
			fputcsv($fp, [
				'Order ID',
				'Order Status',
				'Order Date',
				'Customer Note',
				'Teacher Name',
				'School Name',
				'Address',
				'City',
				'State Code',
				'Postal Code',
				'Country Code',
				'Teacher E-mail',
				'Teacher Phone Number',
				'Payment Method',
				'Nama Siswa (Anggota 1)',
				'Nama Siswa (Anggota 2)'
			]);
			foreach ($orders as $order) {
				$items = $order->get_items();
				foreach ($items as $item) {
					fputcsv($fp, [
						$order->get_id(),
						$order->get_status(),
						$order->get_date_created(),
						$order->get_customer_note(),
						$order->get_formatted_billing_full_name(),
						$order->get_billing_company(),
						$order->get_billing_address_1() . ' ' . $order->get_billing_address_2(),
						$order->get_billing_city(),
						$order->get_billing_state(),
						$order->get_billing_postcode(),
						$order->get_billing_country(),
						$order->get_billing_email(),
						$order->get_billing_phone(),
						$order->get_payment_method_title(),
						$item->get_meta('Nama Siswa (Anggota 1)'),
						$item->get_meta('Nama Siswa (Anggota 2)')
					]);
				}
			}
			fclose($fp);
			exit();
		}
	}
}