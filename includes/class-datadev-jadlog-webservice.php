<?php

/**
 * Datadev Jadlog Webservice.
 *
 * @package Datadev_Jadlog/Classes/Webservice
 * @since   1.0.0
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Datadev Jadlog Webservice integration class.
 */
class Datadev_Jadlog_Webservice {

    /**
     * Webservice URL.
     *
     * @var string
     */
    private $_webservice = 'http://www.jadlog.com.br/embarcador/api/frete/valor';

    /**
     * Shipping method ID.
     *
     * @var string
     */
    protected $id = '';

    /**
     * Shipping zone instance ID.
     *
     * @var int
     */
    protected $instance_id = 0;

    /**
     * ID from Jadlog modality.
     *
     * @var string|array
     */
    protected $modality = '';
    
    /**
     * Jadlog modal type
     *
     * @var string
     */
    protected $modal_type = '';

    /**
     * WooCommerce package containing the products.
     *
     * @var array
     */
    protected $package = null;

    /**
     * Origin postcode.
     *
     * @var string
     */
    protected $origin_postcode = '';

    /**
     * Destination postcode.
     *
     * @var string
     */
    protected $destination_postcode = '';

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
     * Package height.
     *
     * @var float
     */
    protected $height = 0;

    /**
     * Package width.
     *
     * @var float
     */
    protected $width = 0;

    /**
     * Package length.
     *
     * @var float
     */
    protected $length = 0;

    /**
     * Package weight.
     *
     * @var float
     */
    protected $weight = 0;

    /**
     * Minimum height.
     *
     * @var float
     */
    protected $minimum_height = 2;

    /**
     * Minimum width.
     *
     * @var float
     */
    protected $minimum_width = 11;

    /**
     * Minimum length.
     *
     * @var float
     */
    protected $minimum_length = 16;

    /**
     * Extra weight.
     *
     * @var float
     */
    protected $extra_weight = 0;

    /**
     * Declared value.
     *
     * @var string
     */
    protected $declared_value = '0';

    /**
     * Contract.
     *
     * @var string
     */
    protected $contract = '';

    /**
     * Authorization token
     *
     * @var string
     */
    protected $token = '';

    /**
     * Debug mode.
     *
     * @var string
     */
    protected $debug = 'no';

    /**
     * Logger.
     *
     * @var WC_Logger
     */
    protected $log = null;

    /**
     * Initialize webservice.
     *
     * @param string $id Method ID.
     * @param int    $instance_id Instance ID.
     */
    public function __construct($id = 'jadlog', $instance_id = 0) {
        $this->id = $id;
        $this->instance_id = $instance_id;
        $this->log = new WC_Logger();
    }

    /**
     * Set the modality
     *
     * @param string|array $modality Service.
     */
    public function set_modality($modality = '') {
        if (is_array($modality)) {
            $this->modality = implode(',', $modality);
        } else {
            $this->modality = $modality;
        }
    }
    
    /**
     * Set the modal type
     *
     * @param string $modal_type modal type.
     */
    public function set_modal_type($modal_type = '') {
        $this->modal_type = $modal_type;
    }

    /**
     * Set shipping package.
     *
     * @param array $package Shipping package.
     */
    public function set_package($package = array()) {
        $this->package = $package;
        $jadlog_package = new Datadev_Jadlog_Package($package);

        if (!is_null($jadlog_package)) {
            $data = $jadlog_package->get_data();

            $this->set_height($data['height']);
            $this->set_width($data['width']);
            $this->set_length($data['length']);
            $this->set_weight($this->get_cubic_weight($this->get_height(), $this->get_width(), $this->get_length()));
        }

        if ('yes' === $this->debug) {
            if (!empty($data)) {
                $data = array(
                    'weight' => $this->get_weight(),
                    'height' => $this->get_height(),
                    'width' => $this->get_width(),
                    'length' => $this->get_length(),
                );
            }

            $this->log->add($this->id, 'Weight and cubage of the order: ' . print_r($data, true));
        }
    }
    
    /**
     * Calculates cubic weight according to modal type
     *
     * @param float $pheight Package height.
     * @param float $pwidth Package width.
     * @param float $plength Package length.
     */
    public function get_cubic_weight($pheight = 0, $pwidth = 0, $plength = 0) {
        $pheight = str_replace(',', '.', $pheight);
        $pwidth = str_replace(',', '.', $pwidth);
        $plength = str_replace(',', '.', $plength);
        $modal_divider = 1;
        if ($this->modal_type === 'AEREO') {
            $modal_divider = 6000;
        } elseif ($this->modal_type === 'RODO') {
            $modal_divider = 3333;
        }
                
        if ('yes' === $this->debug) {
            $this->log->add($this->id, 'height: ' .$pheight . ' width: ' . $pwidth . ' length: ' . $plength );
            $this->log->add($this->id, 'Modal type: ' . $this->modal_type . ' (Modal divider: ' . $modal_divider . ')');
            $this->log->add($this->id, 'Calculation: ' . "$pheight * $pwidth * $plength) / $modal_divider");
            $this->log->add($this->id, 'Calculation: ' . ($pheight * $pwidth * $plength) . ' / ' . $modal_divider);
            $this->log->add($this->id, 'Calculation: ' . (($pheight * $pwidth * $plength)/$modal_divider));
        }
        
        if ($modal_divider === 1) {
            return;
        } else {
            return ($pheight * $pwidth * $plength) / $modal_divider;
        }
    }

    /**
     * Set origin postcode.
     *
     * @param string $postcode Origin postcode.
     */
    public function set_origin_postcode($postcode = '') {
        $this->origin_postcode = $postcode;
    }

    /**
     * Set destination postcode.
     *
     * @param string $postcode Destination postcode.
     */
    public function set_destination_postcode($postcode = '') {
        $this->destination_postcode = $postcode;
    }

    /**
     * Set CNPJ.
     *
     * @param string $cnpj CNPJ.
     */
    public function set_cnpj($cnpj = '') {
        $this->cnpj = $cnpj;
    }

    /**
     * Set account.
     *
     * @param string $account account.
     */
    public function set_account($account = '') {
        $this->account = $account;
    }

    /**
     * Set shipping package height.
     *
     * @param float $height Package height.
     */
    public function set_height($height = 0) {
        $this->height = (float) $height;
    }

    /**
     * Set shipping package width.
     *
     * @param float $width Package width.
     */
    public function set_width($width = 0) {
        $this->width = (float) $width;
    }

    /**
     * Set shipping package length.
     *
     * @param float $length Package length.
     */
    public function set_length($length = 0) {
        $this->length = (float) $length;
    }

    /**
     * Set shipping package weight.
     *
     * @param float $weight Package weight.
     */
    public function set_weight($weight = 0) {
        $this->weight = (float) $weight;
    }

    /**
     * Set minimum height.
     *
     * @param float $minimum_height Package minimum height.
     */
    public function set_minimum_height($minimum_height = 0) {
        $this->minimum_height = (float) $minimum_height;
    }

    /**
     * Set minimum width.
     *
     * @param float $minimum_width Package minimum width.
     */
    public function set_minimum_width($minimum_width = 0) {
        $this->minimum_width = (float) $minimum_width;
    }

    /**
     * Set minimum length.
     *
     * @param float $minimum_length Package minimum length.
     */
    public function set_minimum_length($minimum_length = 0) {
        $this->minimum_length = (float) $minimum_length;
    }

    /**
     * Set extra weight.
     *
     * @param float $extra_weight Package extra weight.
     */
    public function set_extra_weight($extra_weight = 0) {
        $this->extra_weight = (float) wc_format_decimal($extra_weight);
    }

    /**
     * Set declared value.
     *
     * @param string $declared_value Declared value.
     */
    public function set_declared_value($declared_value = '0') {
        $this->declared_value = $declared_value;
    }

    /**
     * Set contract.
     *
     * @param string $contract contract.
     */
    public function set_contract($contract = '') {
        $this->contract = $contract;
    }

    /**
     * Set Authorization token.
     *
     * @param string $token Authorization token
     */
    public function set_token($token = '') {
        $this->token = $token;
    }

    /**
     * Set the debug mode.
     *
     * @param string $debug Yes or no.
     */
    public function set_debug($debug = 'no') {
        $this->debug = $debug;
    }

    /**
     * Get webservice URL.
     *
     * @return string
     */
    public function get_webservice_url() {
        return apply_filters('datadev_jadlog_webservice_url', $this->_webservice, $this->id, $this->instance_id, $this->package);
    }

    /**
     * Get origin postcode.
     *
     * @return string
     */
    public function get_origin_postcode() {
        return apply_filters('datadev_jadlog_origin_postcode', $this->origin_postcode, $this->id, $this->instance_id, $this->package);
    }

    /**
     * Get CNPJ.
     *
     * @return string
     */
    public function get_cnpj() {
        return apply_filters('datadev_jadlog_login', $this->cnpj, $this->id, $this->instance_id, $this->package);
    }

    /**
     * Get account.
     *
     * @return string
     */
    public function get_account() {
        return apply_filters('datadev_jadlog_password', $this->account, $this->id, $this->instance_id, $this->package);
    }

    /**
     * Get height.
     *
     * @return float
     */
    public function get_height() {
        return $this->float_to_string($this->minimum_height <= $this->height ? $this->height : $this->minimum_height);
    }

    /**
     * Get width.
     *
     * @return float
     */
    public function get_width() {
        return $this->float_to_string($this->minimum_width <= $this->width ? $this->width : $this->minimum_width);
    }

    /**
     * Get length.
     *
     * @return float
     */
    public function get_length() {
        return $this->float_to_string($this->minimum_length <= $this->length ? $this->length : $this->minimum_length);
    }

    /**
     * Get weight.
     *
     * @return float
     */
    public function get_weight() {
        return $this->float_to_string($this->weight + $this->extra_weight);
    }

    /**
     * Get contract.
     *
     * @return string
     */
    public function get_contract() {
        return apply_filters('datadev_jadlog_contract', $this->contract, $this->id, $this->instance_id, $this->package);
    }

    /**
     * Get Authorization token.
     *
     * @return string
     */
    public function get_token() {
        return apply_filters('datadev_jadlog_token', $this->token, $this->id, $this->instance_id, $this->package);
    }

    /**
     * Fix number format for XML.
     *
     * @param  float $value  Value with dot.
     *
     * @return string        Value with comma.
     */
    protected function float_to_string($value) {
        $value = str_replace('.', ',', $value);

        return $value;
    }

    /**
     * Convert number to float.
     *
     * @param  string $value  Value with dot.
     *
     * @return float        Value with comma.
     */
    protected function string_to_float($value) {
        $value = str_replace(',', '.', $value);

        return floatval($value);
    }

    /**
     * Check if is available.
     *
     * @return bool
     */
    protected function is_available() {
        $origin_postcode = $this->get_origin_postcode();

        return !empty($this->modality) || !empty($this->destination_postcode) || !empty($origin_postcode) || 0 === $this->get_height();
    }

    /**
     * Get shipping prices.
     *
     * @return SimpleXMLElement|array
     */
    public function get_shipping() {
        $shipping = null;

        // Checks if service and postcode are empty.
        if (!$this->is_available()) {
            return $shipping;
        }

        $frete = apply_filters('datadev_jadlog_shipping_args', array(
            'cepori' => wc_datadev_jadlog_sanitize_postcode($this->get_origin_postcode()),
            'cepdes' => wc_datadev_jadlog_sanitize_postcode($this->destination_postcode),
            'frap' => 'N',
            'peso' => number_format($this->string_to_float($this->get_weight()), 2, '.', ''),
            'cnpj' => $this->get_cnpj(),
            'conta' => $this->get_account(),
            'contrato' => $this->get_contract(),
            'modalidade' => $this->modality,
            'tpentrega' => 'D',
            'tpseguro' => 'N',
            'vldeclarado' => round(number_format($this->declared_value, 2, '.', '')),
            'vlcoleta' => null,
                ), $this->id, $this->instance_id, $this->package);

        $payload = array(
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->get_token(),
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(
                    array(
                        'frete' => array($frete),
                    )
            )
        );

        $url = $this->get_webservice_url();

        if ('yes' === $this->debug) {
            $this->log->add($this->id, 'Datadev - Jadlog for WooCommerce: ' . DATADEV_JADLOG_VERSION);
            $this->log->add($this->id, 'Requesting Jadlog WebServices: ' . $url);
            $this->log->add($this->id, 'Payload: ' . print_r($payload, true));
        }

        // Gets the WebServices response.
        $response = wp_safe_remote_post(esc_url_raw($url), $payload);

        if (is_wp_error($response)) {
            if ('yes' === $this->debug) {
                $this->log->add($this->id, 'WP_Error: ' . $response->get_error_message());
            }
        } elseif ($response['response']['code'] >= 200 && $response['response']['code'] < 300) {
            try {
                if ('yes' === $this->debug) {
                    $this->log->add($this->id, 'Result: ' . print_r($response['body'], true));
                }
                $result = json_decode($response['body']);
            } catch (Exception $e) {
                if ('yes' === $this->debug) {
                    $this->log->add($this->id, 'Jadlog WebServices invalid XML: ' . $e->getMessage());
                }
            }

            if (isset($result->frete[0]->vltotal)) {
                $shipping = $result->frete[0];
            }
        } else {
            if ('yes' === $this->debug) {
                $this->log->add($this->id, 'Error accessing the Jadlog WebServices: ' . print_r($response, true));
            }
        }

        return $shipping;
    }

}
