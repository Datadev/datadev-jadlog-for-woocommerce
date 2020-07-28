<?php
/**
 * Jadlog Package shipping method.
 *
 * @package Datadev_Jadlog/Classes/Shipping
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Jadlog Package shipping method class.
 */
class Datadev_Jadlog_Shipping_Package extends Datadev_Jadlog_Shipping {

    /**
     * Jadlog Package code.
     * 0.
     *
     * @var string
     */
    protected $code = '3';

    /**
     * Service modality type.
     *
     * @var string
     */
    protected $modal = 'RODO';
        
    /**
     * Initialize Jadlog Package.
     *
     * @param int $instance_id Shipping zone instance.
     */
    public function __construct( $instance_id = 0 ) {
            $this->id           = 'jadlog-package';
            $this->method_title = __( 'Jadlog Package', 'datadev-jadlog-for-woocommerce' );
            $this->more_link    = 'https://www.jadlog.com.br/sitedpd/produtos-e-servicos/?lang=';

            parent::__construct( $instance_id );
    }

    /**
     * Get the declared value from the package.
     *
     * @param  array $package Cart package.
     * @return float
     */
    protected function get_declared_value( $package ) {
            return $package['contents_cost'];
    }
}
