<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="rbwc_product_data" class="panel woocommerce_options_panel">
    <div id="recurring-options" class="options_group">
		<?php 
woocommerce_wp_checkbox( array(
    'id'          => '_wc_booking_rbwc_recur_length_fixed',
    'value'       => get_post_meta( get_the_ID(), '_wc_booking_rbwc_recur_length_fixed', true ),
    'label'       => __( 'Fixed Length?', 'recurring-bookings-for-woocommerce' ),
    'description' => __( 'Tick this box if this product has a fixed length, e,g, a 5-day plumbing course that cannot be shortened.', 'recurring-bookings-for-woocommerce' ),
    'desc_tip'    => true,
) );
woocommerce_wp_text_input( array(
    'id'          => '_wc_booking_rbwc_recur_length',
    'value'       => get_post_meta( get_the_ID(), '_wc_booking_rbwc_recur_length', true ),
    'label'       => __( 'Recurrence Length', 'recurring-bookings-for-woocommerce' ),
    'description' => __( 'Choose how many days, weeks or months the recurrence lasts for.', 'recurring-bookings-for-woocommerce' ),
    'type'        => 'number',
    'desc_tip'    => true,
) );
woocommerce_wp_checkbox( array(
    'id'          => '_wc_booking_rbwc_recur_period_fixed',
    'value'       => get_post_meta( get_the_ID(), '_wc_booking_rbwc_recur_period_fixed', true ),
    'label'       => __( 'Fixed Period?', 'recurring-bookings-for-woocommerce' ),
    'description' => __( 'Tick this box if this product has a fixed period, e,g, physiotherapy appointments must be a week apart, not days.', 'recurring-bookings-for-woocommerce' ),
    'desc_tip'    => true,
) );
woocommerce_wp_select( array(
    'id'          => '_wc_booking_rbwc_recur_period',
    'value'       => get_post_meta( get_the_ID(), '_wc_booking_rbwc_recur_period', true ),
    'options'     => array(
    ''      => __( 'Please select', 'recurring-bookings-for-woocommerce' ),
    'day'   => __( 'Days', 'recurring-bookings-for-woocommerce' ),
    'week'  => __( 'Weeks', 'recurring-bookings-for-woocommerce' ),
    'month' => __( 'Months', 'recurring-bookings-for-woocommerce' ),
),
    'label'       => __( 'Recurrence Period', 'recurring-bookings-for-woocommerce' ),
    'description' => __( 'Choose the period this product recurs over.', 'recurring-bookings-for-woocommerce' ),
    'desc_tip'    => true,
) );
// Allow other plugins or integrations to add further custom fields to the Recurring Tab
echo  '</div>' ;
// Closing tag required for styling
do_action( 'rbwc_add_custom_fields' );
?>
</div>