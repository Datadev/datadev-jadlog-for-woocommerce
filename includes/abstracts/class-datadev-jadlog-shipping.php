<?php

/**
 * Abstract Jadlog shipping method.
 *
 * @package Datadev_Jadlog/Abstracts
 * @since   1.0.0
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Default Jadlog shipping method abstract class.
 *
 * This is a abstract method with default options for all methods.
 */
abstract class Datadev_Jadlog_Shipping extends WC_Shipping_Method {

    /**
     * Service modality code.
     *
     * @var string
     */
    protected $code = '';

    /**
     * CNPJ.
     *
     * @var string
     */
    protected $cnpj = '';

    /**
     * Account.
     *
     * @var string
     */
    protected $account = '';

    /**
     * Contract.
     *
     * @var string
     */
    protected $contract = '';

    /**
     * Authorization token.
     *
     * @var string
     */
    protected $token = '';

    /**
     * Initialize the Datadev Jadlog shipping method.
     *
     * @param int $instance_id Shipping zone instance ID.
     */
    public function __construct($instance_id = 0) {
        $this->instance_id = absint($instance_id);
        /* translators: %s: method title */
        $this->method_description = sprintf(__('%s is a shipping method from Jadlog.', 'datadev-jadlog-for-woocommerce'), $this->method_title);
        $this->supports = array(
            'shipping-zones',
            'instance-settings',
        );

        // Load the form fields.
        $this->init_form_fields();

        // Define user set variables.
        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('title');
        $this->origin_postcode = $this->get_option('origin_postcode');
        $this->shipping_class_id = (int) $this->get_option('shipping_class_id', '-1');
        $this->show_delivery_time = $this->get_option('show_delivery_time');
        $this->additional_time = $this->get_option('additional_time');
        $this->fee = $this->get_option('fee');
        $this->declare_value = $this->get_option('declare_value');
        $this->custom_code = $this->get_option('custom_code');
        $this->cnpj = $this->get_option('cnpj');
        $this->account = $this->get_option('account');
        $this->contract = $this->get_option('contract');
        $this->token = $this->get_option('token');
        $this->minimum_height = $this->get_option('minimum_height');
        $this->minimum_width = $this->get_option('minimum_width');
        $this->minimum_length = $this->get_option('minimum_length');
        $this->extra_weight = $this->get_option('extra_weight', '0');
        $this->debug = $this->get_option('debug');

        // Save admin options.
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

    /**
     * Get log.
     *
     * @return string
     */
    protected function get_log_link() {
        return ' <a href="' . esc_url(admin_url('admin.php?page=wc-status&tab=logs&log_file=' . esc_attr($this->id) . '-' . sanitize_file_name(wp_hash($this->id)) . '.log')) . '">' . __('View logs.', 'datadev-jadlog-for-woocommerce') . '</a>';
    }

    /**
     * Get base postcode.
     *
     * @since  1.0.0
     * @return string
     */
    protected function get_base_postcode() {
        // WooCommerce 3.1.1+.
        if (method_exists(WC()->countries, 'get_base_postcode')) {
            return WC()->countries->get_base_postcode();
        }

        return '';
    }

    /**
     * Get shipping classes options.
     *
     * @return array
     */
    protected function get_shipping_classes_options() {
        $shipping_classes = WC()->shipping->get_shipping_classes();
        $options = array(
            '-1' => __('Any Shipping Class', 'datadev-jadlog-for-woocommerce'),
            '0' => __('No Shipping Class', 'datadev-jadlog-for-woocommerce'),
        );

        if (!empty($shipping_classes)) {
            $options += wp_list_pluck($shipping_classes, 'name', 'term_id');
        }

        return $options;
    }

    /**
     * Admin options fields.
     */
    public function init_form_fields() {
        $this->instance_form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'datadev-jadlog-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable this shipping method', 'datadev-jadlog-for-woocommerce'),
                'default' => 'yes',
            ),
            'title' => array(
                'title' => __('Title', 'datadev-jadlog-for-woocommerce'),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'default' => $this->method_title,
            ),
            'behavior_options' => array(
                'title' => __('Behavior Options', 'datadev-jadlog-for-woocommerce'),
                'type' => 'title',
                'default' => '',
            ),
            'origin_postcode' => array(
                'title' => __('Origin Postcode', 'datadev-jadlog-for-woocommerce'),
                'type' => 'text',
                'description' => __('The postcode of the location your packages are delivered from.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'placeholder' => '00000-000',
                'default' => $this->get_base_postcode(),
            ),
            'shipping_class_id' => array(
                'title' => __('Shipping Class', 'datadev-jadlog-for-woocommerce'),
                'type' => 'select',
                'description' => __('If necessary, select a shipping class to apply this method.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'default' => '',
                'class' => 'wc-enhanced-select',
                'options' => $this->get_shipping_classes_options(),
            ),
            'show_delivery_time' => array(
                'title' => __('Delivery Time', 'datadev-jadlog-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Show estimated delivery time', 'woocommerce-datadev-jadlog'),
                'description' => __('Display the estimated delivery time in working days.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'default' => 'no',
            ),
            'additional_time' => array(
                'title' => __('Additional Days', 'datadev-jadlog-for-woocommerce'),
                'type' => 'text',
                'description' => __('Additional working days to the estimated delivery.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'default' => '0',
                'placeholder' => '0',
            ),
            'fee' => array(
                'title' => __('Handling Fee', 'woocommerce-datadev-jadlog'),
                'type' => 'price',
                'description' => __('Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'placeholder' => '0.00',
                'default' => '',
            ),
            'optional_services' => array(
                'title' => __('Optional Services', 'datadev-jadlog-for-woocommerce'),
                'type' => 'title',
                'description' => __('Use these options to add the value of each service provided by the Jadlog.', 'datadev-jadlog-for-woocommerce'),
                'default' => '',
            ),
            'declare_value' => array(
                'title' => __('Declare Value for Insurance', 'datadev-jadlog-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable declared value', 'datadev-jadlog-for-woocommerce'),
                'description' => __('This controls if the price of the package must be declared for insurance purposes.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'default' => 'yes',
            ),
            'service_options' => array(
                'title' => __('Service Options', 'datadev-jadlog-for-woocommerce'),
                'type' => 'title',
                'default' => '',
            ),
            'custom_code' => array(
                'title' => __('Service Code', 'datadev-jadlog-for-woocommerce'),
                'type' => 'text',
                'description' => __('Service code, use this for custom codes.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'placeholder' => $this->code,
                'default' => '',
            ),
            'cnpj' => array(
                'title' => __('CNPJ', 'datadev-jadlog-for-woocommerce'),
                'type' => 'text',
                'description' => __('CNPJ', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'placeholder' => $this->cnpj,
                'default' => '',
            ),
            'account' => array(
                'title' => __('Account', 'datadev-jadlog-for-woocommerce'),
                'type' => 'text',
                'description' => __('Account.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'placeholder' => $this->account,
                'default' => '',
            ),
            'contract' => array(
                'title' => __('Contract', 'datadev-jadlog-for-woocommerce'),
                'type' => 'text',
                'description' => __('Contract.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'placeholder' => $this->contract,
                'default' => '',
            ),
            'token' => array(
                'title' => __('Authorization token', 'datadev-jadlog-for-woocommerce'),
                'type' => 'text',
                'description' => __('Authorization token.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'placeholder' => $this->token,
                'default' => '',
            ),
            'package_standard' => array(
                'title' => __('Package Standard', 'datadev-jadlog-for-woocommerce'),
                'type' => 'title',
                'description' => __('Minimum measure for your shipping packages.', 'datadev-jadlog-for-woocommerce'),
                'default' => '',
            ),
            'minimum_height' => array(
                'title' => __('Minimum Height (cm)', 'datadev-jadlog-for-woocommerce'),
                'type' => 'text',
                'description' => __('Minimum height of your shipping packages.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'default' => '0',
            ),
            'minimum_width' => array(
                'title' => __('Minimum Width (cm)', 'datadev-jadlog-for-woocommerce'),
                'type' => 'text',
                'description' => __('Minimum width of your shipping packages.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'default' => '0',
            ),
            'minimum_length' => array(
                'title' => __('Minimum Length (cm)', 'datadev-jadlog-for-woocommerce'),
                'type' => 'text',
                'description' => __('Minimum length of your shipping packages.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'default' => '0',
            ),
            'extra_weight' => array(
                'title' => __('Extra Weight (kg)', 'datadev-jadlog-for-woocommerce'),
                'type' => 'text',
                'description' => __('Extra weight in kilograms to add to the package total when quoting shipping costs.', 'datadev-jadlog-for-woocommerce'),
                'desc_tip' => true,
                'default' => '0',
            ),
            'testing' => array(
                'title' => __('Testing', 'datadev-jadlog-for-woocommerce'),
                'type' => 'title',
                'default' => '',
            ),
            'debug' => array(
                'title' => __('Debug Log', 'datadev-jadlog-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable logging', 'datadev-jadlog-for-woocommerce'),
                'default' => 'no',
                /* translators: %s: method title */
                'description' => sprintf(__('Log %s events, such as WebServices requests.', 'datadev-jadlog-for-woocommerce'), $this->method_title) . $this->get_log_link(),
            ),
        );
    }

    /**
     * Datadev Jadlog options page.
     */
    public function admin_options() {
        include Datadev_Jadlog::get_plugin_path() . 'includes/admin/views/html-admin-shipping-method-settings.php';
    }

    /**
     * Validate price field.
     *
     * Make sure the data is escaped correctly, etc.
     * Includes "%" back.
     *
     * @param  string $key   Field key.
     * @param  string $value Posted value.
     * @return string
     */
    public function validate_price_field($key, $value) {
        $value = is_null($value) ? '' : $value;
        $new_value = '' === $value ? '' : wc_format_decimal(trim(stripslashes($value)));

        if ('%' === substr($value, -1)) {
            $new_value .= '%';
        }

        return $new_value;
    }

    /**
     * Get Jadlog modality code.
     *
     * @return string
     */
    public function get_code() {
        if (!empty($this->custom_code)) {
            $code = $this->custom_code;
        } else {
            $code = $this->code;
        }

        return apply_filters('datadev_jadlog_shipping_method_code', $code, $this->id, $this->instance_id);
    }

    /**
     * Get CNPJ.
     *
     * @return string
     */
    public function get_cnpj() {
        if (!empty($this->cnpj)) {
            $cnpj = $this->cnpj;
        } else {
            $cnpj = $this->cnpj;
        }

        return apply_filters('datadev_jadlog_shipping_cnpj', $cnpj, $this->id, $this->instance_id);
    }

    /**
     * Get contract.
     *
     * @return string
     */
    public function get_contract() {
        if (!empty($this->contract)) {
            $contract = $this->contract;
        } else {
            $contract = $this->contract;
        }

        return apply_filters('datadev_jadlog_shipping_contract', $contract, $this->id, $this->instance_id);
    }

    /**
     * Get account.
     *
     * @return string
     */
    public function get_account() {
        if (!empty($this->account)) {
            $account = $this->account;
        } else {
            $account = $this->account;
        }

        return apply_filters('datadev_jadlog_shipping_account', $account, $this->id, $this->instance_id);
    }

    /**
     * Get the Authorization token.
     *
     * @return string
     */
    public function get_token() {
        if (!empty($this->token)) {
            $token = $this->token;
        } else {
            $token = $this->token;
        }

        return apply_filters('datadev_jadlog_shipping_token', $token, $this->id, $this->instance_id);
    }

    /**
     * Get the declared value from the package.
     *
     * @param  array $package Cart package.
     *
     * @return float
     */
    protected function get_declared_value($package) {
        return $package['contents_cost'];
    }

    /**
     * Get shipping rate.
     *
     * @param  array $package Cart package.
     *
     * @return SimpleXMLElement|null
     */
    protected function get_rate($package) {
        $api = new Datadev_Jadlog_Webservice($this->id, $this->instance_id);
        $api->set_debug($this->debug);
        $api->set_modality($this->get_code());
        $api->set_package($package);
        $api->set_origin_postcode($this->origin_postcode);
        $api->set_destination_postcode($package['destination']['postcode']);

        if ('yes' === $this->declare_value) {
            $api->set_declared_value($this->get_declared_value($package));
        }

        $api->set_cnpj($this->get_cnpj());
        $api->set_contract($this->get_contract());
        $api->set_account($this->get_account());
        $api->set_token($this->get_token());

        $api->set_minimum_height($this->minimum_height);
        $api->set_minimum_width($this->minimum_width);
        $api->set_minimum_length($this->minimum_length);
        $api->set_extra_weight($this->extra_weight);

        $shipping = $api->get_shipping();

        return $shipping;
    }

    /**
     * Get additional time.
     *
     * @param  array $package Package data.
     *
     * @return array
     */
    protected function get_additional_time($package = array()) {
        return apply_filters('datadev_jadlog_shipping_additional_time', $this->additional_time, $package);
    }

    /**
     * Check if package uses only the selected shipping class.
     *
     * @param  array $package Cart package.
     * @return bool
     */
    protected function has_only_selected_shipping_class($package) {
        $only_selected = true;

        if (-1 === $this->shipping_class_id) {
            return $only_selected;
        }

        foreach ($package['contents'] as $item_id => $values) {
            $product = $values['data'];
            $qty = $values['quantity'];

            if ($qty > 0 && $product->needs_shipping()) {
                if ($this->shipping_class_id !== $product->get_shipping_class_id()) {
                    $only_selected = false;
                    break;
                }
            }
        }

        return $only_selected;
    }

    /**
     * Calculates the shipping rate.
     *
     * @param array $package Order package.
     */
    public function calculate_shipping($package = array()) {
        // Check if valid to be calculeted.
        if ('' === $package['destination']['postcode'] || 'BR' !== $package['destination']['country']) {
            return;
        }

        // Check for shipping classes.
        if (!$this->has_only_selected_shipping_class($package)) {
            return;
        }

        $shipping = $this->get_rate($package);

        if (isset($shipping->error)) {
            $error_number = (string) $shipping->error->id; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
            // Display Jadlog errors.
            $error_message = $error_number . ' - ' . $shipping->error->descricao;
            if ('' !== $error_message && is_cart()) {
                $notice_type = 'error';
                $notice = '<strong>' . $this->title . ':</strong> ' . esc_html($error_message);
                wc_add_notice($notice, $notice_type);
            }
        }

        if (!isset($shipping->vltotal)) {
            return;
        }

        // Set the shipping rates.
        $label = $this->title;
        $cost = esc_attr((string) $shipping->vltotal); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
        // Exit if don't have price.
        if (0 === intval($cost)) {
            return;
        }

        // Apply fees.
        $fee = $this->get_fee($this->fee, $cost);

        // Display delivery.
        $meta_delivery = array();
        if ('yes' === $this->show_delivery_time) {
            $meta_delivery = array(
                '_delivery_forecast' => intval($shipping->prazo) + intval($this->get_additional_time($package)), // phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
            );
        }

        // Create the rate and apply filters.
        $rate = apply_filters(
                'woocommerce_datadev_jadlog_' . $this->id . '_rate', array(
            'id' => $this->id . $this->instance_id,
            'label' => $label,
            'cost' => (float) $cost + (float) $fee,
            'meta_data' => $meta_delivery,
                ), $this->instance_id, $package
        );

        $rates = apply_filters('datadev_jadlog_shipping_methods', array($rate), $package);

        // Add rate to WooCommerce.
        $this->add_rate($rates[0]);
    }

}
