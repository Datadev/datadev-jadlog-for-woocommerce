<?php

/**
 * Datadev Jadlog Com shipping method.
 *
 * @package Datadev_Jadlog/Classes/Shipping
 * @since   1.0.0
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Jadlog Com shipping method class.
 */
class Datadev_Jadlog_Shipping_Com extends Datadev_Jadlog_Shipping {

    /**
     * Jadlog Com code.
     * 0.
     *
     * @var string
     */
    protected $code = '9';

    /**
     * Initialize Jadlog Com.
     *
     * @param int $instance_id Shipping zone instance.
     */
    public function __construct($instance_id = 0) {
        $this->id = 'jadlog-com';
        $this->method_title = __('Jadlog .com', 'datadev-jadlog-for-woocommerce');
        $this->more_link = 'https://www.jadlog.com.br/sitedpd/produtos-e-servicos/?lang=';

        parent::__construct($instance_id);
    }

    /**
     * Get the declared value from the package.
     *
     * @param  array $package Cart package.
     * @return float
     */
    protected function get_declared_value($package) {
        return $package['contents_cost'];
    }

}
