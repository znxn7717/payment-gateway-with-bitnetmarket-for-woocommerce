<?php
/*
Plugin Name: Payment Gateway with BitnetMarket for WooCommerce
Plugin URI: https://github.com/znxn7717/payment-gateway-with-bitnetmarket-for-woocommerce
Description: The BitnetMarket payment gateway adds the ability to accept cryptocurrency payments to your store simply and securely. With this plugin, you can enable various cryptocurrencies as a payment method on your site and provide your customers with a modern, fast, and borderless shopping experience. درگاه پرداخت بیت‌نت‌مارکت امکان پذیرش پرداخت‌های رمزارزی را به سادگی و امنیت به فروشگاه شما اضافه می‌کند. با استفاده از این افزونه می‌توانید رمزارزهای مختلف را به عنوان روش پرداخت در سایت خود فعال کرده و تجربه خریدی مدرن، سریع و بدون مرز برای مشتریان‌تان فراهم کنید
Version: 1.0.0
Author: znxn7717
Requires Plugins: woocommerce
Tested up to: 6.8
WC tested up to: 10.2
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
*/

if (!defined('ABSPATH')) {
    exit;
}

define('BMWOO_BITNETMARKET_DIR', plugin_dir_path(__FILE__));
define('BMWOO_BITNETMARKET_URL', plugin_dir_url(__FILE__));

function load_bitnetmarket_woo_gateway() {
    add_filter('woocommerce_payment_gateways', 'woocommerce_add_bitnetmarket_gateway');
    function woocommerce_add_bitnetmarket_gateway($methods) {
        $methods[] = 'WC_Gateway_Bitnetmarket';
        return $methods;
    }
    require_once(BMWOO_BITNETMARKET_DIR . 'class-wc-gateway-bitnetmarket.php');
}
add_action('plugins_loaded', 'load_bitnetmarket_woo_gateway', 0);

function declare_bitnetmarket_cart_checkout_blocks_compatibility() {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
    }
}
add_action('before_woocommerce_init', 'declare_bitnetmarket_cart_checkout_blocks_compatibility');

add_action('woocommerce_blocks_loaded', 'bitnetmarket_register_payment_method_type');
function bitnetmarket_register_payment_method_type() {
    if (!class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
        return;
    }
    require_once BMWOO_BITNETMARKET_DIR . 'class-block.php';
    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function (Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
            $payment_method_registry->register(new Bitnetmarket_Gateway_Blocks());
        }
    );
}

// نمایش پیام عدم نصب WooCommerce
add_action('admin_notices', 'bitnetmarket_woocommerce_missing_notice');
function bitnetmarket_woocommerce_missing_notice() {
    if (!class_exists('WC_Payment_Gateway')) {
        echo '<div class="error notice"><p>' . esc_html(__('افزونه "درگاه پرداخت بیت‌نت‌مارکت برای ووکامرس" نیاز به نصب و فعال بودن ووکامرس دارد', 'bitnetmarket-payment-gateway-for-woocommerce')) . '</p></div>';
    }
}