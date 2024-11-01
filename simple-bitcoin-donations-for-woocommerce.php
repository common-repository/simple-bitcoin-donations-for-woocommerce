<?php

/*
 Plugin Name: Simple Bitcoin donations for WooCommerce
 Plugin URI: https://profiles.wordpress.org/rynald0s
 Description: This plugin lets you add a Bitcoin donations option to your WooCommerce checkout page.
 Author: rynald0s
 Author URI: http:rynaldo.com
 Version: 1.0
 License: GPLv3 or later License
 URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

class WC_Btc_donations {

    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_btcdonations_tab', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_btcdonations_tab', __CLASS__ . '::update_settings' );

        add_action( 'woocommerce_admin_field_custom_type', __CLASS__ . '::btcdonations', 10, 1 );
    }
    
    public static function btcdonations($value){

        echo $value['desc'];
    }

    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['btcdonations_tab'] = __( 'Bitcoin donations', 'woocommerce' );
        return $settings_tabs;
    }

    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }

    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }

    public static function get_settings() {

        $settings = array(

            'section_title' => array(
                'name'     => __( 'Bitcoin donation settings', 'woocommerce' ),
                'type'     => 'title',
                'desc'     => 'Accept Bitcoin donations in your WooCommerce-powered site.',
                'id'       => 'wc_btc_donate_title'
            ),

            'title' => array(
                'name' => __( 'Enable', 'woocommerce' ),
                'type' => 'checkbox',
                'desc' => __( 'Enable this plugin.', 'woocommerce' ),
                'id'   => 'wc_btc_donate_enable'
            ),

            'section_message' => array(
        'name'     => __( 'Your custom message', 'woocommerce' ),
                'type' => 'text',
                'placeholder' => 'Please donate me some Bitcoin',
                'desc'     => __( 'Add your own custom donation message here.', 'woocommerce' ),
                'id'       => 'wc_btc_donate_message',
            ),

            'section_address' => array(
        'name'     => __( 'Your Bitcoin address', 'woocommerce' ),
                'type' => 'text',
                'placeholder' => '1BEsm8VMkYhSFJ92cvUYwxCtsfsB2rBfiG',
                'desc'     => __( 'Add your own valid BTC address here.', 'woocommerce' ),
                'id'       => 'wc_btc_donate_bitcoinAddress',
            ),

            'section_qr_code' => array(
        'name'     => __( 'Make the qr code clickable', 'woocommerce' ),
                'type' => 'checkbox',
                'placeholder' => 'Make the qr code clickable',
                'desc'     => __( 'This is good for those on mobile devices with wallets.', 'woocommerce' ),
                'id'       => 'wc_btc_donate_makeLink',
            ),

            'section_hooks' => array(
        'name'     => __( 'Where would you like to show the donation box', 'woocommerce' ),
                'type' => 'select',
                'placeholder' => 'Select one of the following hooks from the checkout page',
                'desc'     => __( '', 'woocommerce' ),
                'id'       => 'wc_btc_donate_makeHook',
                'options' => array ('woocommerce_before_checkout_form' => 'before checkout form', 'woocommerce_checkout_before_customer_details' => 'before customer details', 'woocommerce_before_checkout_billing_form' => 'before checkout billing form', 'woocommerce_after_checkout_billing_form' => 'after checkout billing form', 'woocommerce_before_order_notes' => 'before order notes', 'woocommerce_review_order_before_payment' => 'before payment', 'woocommerce_review_order_before_submit' => 'before order submit', 'woocommerce_review_order_after_submit' => 'after order submit', 'woocommerce_review_order_after_payment' => 'after payment form', 'woocommerce_after_checkout_form' => 'after checkout form'),
            ),

            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_btcdonations_section_end'
            )
        );

          echo "<span style='background-color:yellow; padding: 5px 15px;margin-top: 10px;display: inline-block;border: 1px solid yellowgreen; text-align: left; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; text-align: center '>Please consider sending me a Bitcoin donation if you find my plugin useful: <span style='color:#96588a'>1BEsm8VMkYhSFJ92cvUYwxCtsfsB2rBfiG</span>. It will help me keep things alive.</span>";

        return apply_filters( 'wc_btcdonations_settings', $settings ); 
    }

}

if( get_option('wc_btc_donate_enable', true ) == 'yes' ) {

add_action( get_option( 'wc_btc_donate_makeHook'), 'wc_btc_donate_run' );
  
function wc_btc_donate_run() {

  $title = get_option( 'wc_btc_donate_message');
  $bitcoinAddress = get_option( 'wc_btc_donate_bitcoinAddress');
  $makeLink = get_option( 'wc_btc_donate_makeLink');

      if ( !preg_match('/^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$/', $bitcoinAddress) ) {
        echo "This is an invalid bitcoin address!";
        return;
      }

      function wc_qr_for_bitcoin_donation($data, $width = 200, $height = 200, $charset = 'utf-8', $error = 'H') {

        $uri = 'https://chart.googleapis.com/chart?';
        $error = 'L|1';
        $query = array( 'cht' => 'qr', 'chs' => $width .'x'. $height, 'choe' => $charset, 'chld' => $error, 'chl' => $data );
        $uri = $uri .= http_build_query($query);

        return $uri;
      }

      echo "<p style='border: 1px solid #e0dadf; padding: 20px; margin: 2em 0 2em 0; text-align: left; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; text-align: center '>";

      if ( $title ) {
            echo $title; 
          }

      if ( $bitcoinAddress ) {

            $qrLinkShort = "bitcoin:".$bitcoinAddress;
            $image = wc_qr_for_bitcoin_donation( $qrLinkShort, 200, 200 );

      if ($makeLink == 'yes') {
              echo "<a href='$qrLinkShort' target='_blank' class='easy_bitcoin_donate_widget_qr_link'><img style='display: block; margin-left: auto; margin-right: auto' src='$image'/></a><br>";
              } else {
              echo "<img src='$image' style='display: block; margin-left: auto; margin-right: auto' />";
              echo "</p>";
      }
    }
  }
}

WC_Btc_donations::init();
