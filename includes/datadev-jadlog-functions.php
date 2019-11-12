<?php

/**
 * Datadev Jadlog functions.
 *
 * @package Datadev_Jadlog/Functions
 * @since   1.0.0
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitize postcode.
 *
 * @param  string $postcode Postcode.
 *
 * @return string
 */
function wc_datadev_jadlog_sanitize_postcode($postcode) {
    return preg_replace('([^0-9])', '', sanitize_text_field($postcode));
}