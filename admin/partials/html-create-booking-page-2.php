<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap woocommerce">
	<h2><?php esc_html_e( 'Add Booking', 'recurring-bookings-for-woocommerce' ); ?></h2>

	<?php $this->show_errors(); ?>

	<form method="POST" data-nonce="<?php echo esc_attr( wp_create_nonce( 'find-booked-day-blocks' ) ); ?>" id="wc-bookings-booking-form">
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					<label><?php esc_html_e( 'Booking Data', 'recurring-bookings-for-woocommerce' ); ?></label>
				</th>
				<td>
					<div class="wc-bookings-booking-form">
                        <div class="wc-booking-form-infobox">
                            <?php

                            do_action( 'rbwc_booking_form_infobox_start', $customer_id, $bookable_product_id );
                            $this->display_recurrence_info( $bookable_product_id );
                            do_action( 'rbwc_booking_form_infobox_end', $customer_id, $bookable_product_id );

                            ?>

                        </div>

						<?php $booking_form->output(); ?>

						<div class="wc-bookings-booking-cost" style="display:none"></div>
					</div>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">&nbsp;</th>
				<td>
					<input type="submit" name="create_booking_2" class="button-primary rbwc-submit" value="<?php esc_attr_e( 'Add Bookings', 'recurring-bookings-for-woocommerce' ); ?>" />
					<input type="hidden" name="customer_id" value="<?php echo esc_attr( $customer_id ); ?>" />
					<input type="hidden" name="bookable_product_id" value="<?php echo esc_attr( $bookable_product_id ); ?>" />
					<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $bookable_product_id ); ?>" />
					<input type="hidden" name="booking_order" value="<?php echo esc_attr( $booking_order ); ?>" />
					<?php wp_nonce_field( 'create_booking_notification' ); ?>
				</td>
			</tr>
			</tbody>
		</table>
	</form>
</div>
