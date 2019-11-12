<?php
/**
 * Datadev Jadlog Rodoviário shipping method.
 *
 * @package Datadev_Jadlog/Classes/Shipping
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Jadlog Rodoviário shipping method class.
 */
class Datadev_Jadlog_Shipping_Rodoviario extends Datadev_Jadlog_Shipping {

	/**
	 * Jadlog Rodoviário code.
	 * 0.
	 *
	 * @var string
	 */
	protected $code = '4';

	/**
	 * Initialize Jadlog Rodoviário.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'jadlog-rodoviario';
		$this->method_title = __( 'Jadlog Rodoviário', 'datadev-jadlog-for-woocommerce' );
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
