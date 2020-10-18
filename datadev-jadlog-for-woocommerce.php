<?php
/*
Plugin Name:          Datadev - Jadlog for WooCommerce
Plugin URI:           https://github.com/datadev/datadev-jadlog-for-woocommerce
Description:          Adds Jadlog shipping methods to your WooCommerce store.
Author:               Datadev
Author URI:           https://www.datadev.com.br
Version:              1.1.0
License:              GPLv2 or later
Text Domain:          datadev-jadlog-for-woocommerce
Domain Path:          /languages
WC requires at least: 3.8.0
WC tested up to:      4.6.0

Datadev - Jadlog for WooCommerce is free software: you can
redistribute it and/or modify it under the terms of the
GNU General Public License as published by the Free Software Foundation,
either version 2 of the License, or any later version.

Datadev - Jadlog for WooCommerce is distributed in the hope that it
will be useful, but WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Datadev - Jadlog for WooCommerce. If not, see
<https://www.gnu.org/licenses/gpl-2.0.txt>.

@package Datadev_Jadlog
*/

defined( 'ABSPATH' ) || exit;

define( 'DATADEV_JADLOG_VERSION', '1.1.0' );
define( 'DATADEV_JADLOG_PLUGIN_FILE', __FILE__ );

if ( ! class_exists( 'Datadev_Jadlog' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-datadev-jadlog.php';

	add_action( 'plugins_loaded', array( 'Datadev_Jadlog', 'init' ) );
}
