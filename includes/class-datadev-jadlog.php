<?php

/**
 * Datadev Jadlog
 *
 * @package Datadev_Jadlog/Classes
 * @since   1.0.0
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugins main class.
 */
class Datadev_Jadlog {

    /**
     * Initialize the plugin public actions.
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'load_plugin_textdomain'), -1);

        // Checks with WooCommerce is installed.
        if (class_exists('WC_Integration')) {
            self::includes();

            add_filter('woocommerce_integrations', array(__CLASS__, 'include_integrations'));
            add_filter('woocommerce_shipping_methods', array(__CLASS__, 'include_methods'));
        } else {
            add_action('admin_notices', array(__CLASS__, 'woocommerce_missing_notice'));
        }
    }

    /**
     * Load the plugin text domain for translation.
     */
    public static function load_plugin_textdomain() {
        load_plugin_textdomain('datadev-jadlog-for-woocommerce', false, dirname(plugin_basename(DATADEV_JADLOG_PLUGIN_FILE)) . '/languages/');
    }

    /**
     * Includes.
     */
    private static function includes() {
        include_once dirname(__FILE__) . '/datadev-jadlog-functions.php';
        include_once dirname(__FILE__) . '/class-datadev-jadlog-package.php';
        include_once dirname(__FILE__) . '/class-datadev-jadlog-webservice.php';
        include_once dirname(__FILE__) . '/class-datadev-jadlog-orders.php';
        include_once dirname(__FILE__) . '/integrations/class-datadev-jadlog-integration.php';
        include_once dirname(__FILE__) . '/abstracts/class-datadev-jadlog-shipping.php';

        foreach (glob(plugin_dir_path(__FILE__) . '/shipping/class-datadev-jadlog-*.php') as $filename) {
            include_once $filename;
        }
    }

    /**
     * Include Correios integration to WooCommerce.
     *
     * @param  array $integrations Default integrations.
     *
     * @return array
     */
    public static function include_integrations($integrations) {
        $integrations[] = 'Datadev_Jadlog_Integration';

        return $integrations;
    }

    /**
     * Include Correios shipping methods to WooCommerce.
     *
     * @param  array $methods Default shipping methods.
     *
     * @return array
     */
    public static function include_methods($methods) {
        $methods['jadlog-cargo'] = 'Datadev_Jadlog_Shipping_Cargo';
        $methods['jadlog-com'] = 'Datadev_Jadlog_Shipping_Com';
        $methods['jadlog-corporate'] = 'Datadev_Jadlog_Shipping_Corporate';
        $methods['jadlog-economico'] = 'Datadev_Jadlog_Shipping_Economico';
        $methods['jadlog-expresso'] = 'Datadev_Jadlog_Shipping_Expresso';
        $methods['jadlog-package'] = 'Datadev_Jadlog_Shipping_Package';
        $methods['jadlog-rodoviario'] = 'Datadev_Jadlog_Shipping_Rodoviario';

        return $methods;
    }

    /**
     * WooCommerce fallback notice.
     */
    public static function woocommerce_missing_notice() {
        include_once dirname(__FILE__) . '/admin/views/html-admin-missing-dependencies.php';
    }

    /**
     * Get main file.
     *
     * @return string
     */
    public static function get_main_file() {
        return DATADEV_JADLOG_PLUGIN_FILE;
    }

    /**
     * Get plugin path.
     *
     * @return string
     */
    public static function get_plugin_path() {
        return plugin_dir_path(DATADEV_JADLOG_PLUGIN_FILE);
    }

    /**
     * Get templates path.
     *
     * @return string
     */
    public static function get_templates_path() {
        return self::get_plugin_path() . 'templates/';
    }

}
