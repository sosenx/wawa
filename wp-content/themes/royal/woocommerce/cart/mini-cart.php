<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>

<?php etheme_cart_items( $item = etheme_get_option( 'top_cart_item' ) ? etheme_get_option( 'top_cart_item' ) : 3 ); ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>