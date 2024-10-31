<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap woocommerce">
    <h2><?php esc_html_e( 'Add Booking', 'recurring-bookings-for-woocommerce' ); ?></h2>

    <p>
	    <?php esc_html_e( 'You can create a new booking or recurring booking for a customer here.',
			    'recurring-bookings-for-woocommerce' ); ?>
    </p>

	<?php $this->show_errors(); ?>

    <form method="POST" data-nonce="<?php echo esc_attr( wp_create_nonce( 'find-booked-day-blocks' ) ); ?>">
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row">
                    <label for="customer_id"><?php esc_html_e( 'Customer', 'recurring-bookings-for-woocommerce' ); ?></label>
                </th>
                <td>
					<?php
					$query_users_ids_by_role = [
						'fields' => [ 'id' ],
						'role'   => 'customers'
					];

					$array_of_users = get_users( $query_users_ids_by_role );

					$array_of_users_ids = array_map( function ( $user ) {
						return $user->id;
					}, $array_of_users );

					$users_ids_list = implode( ',', $array_of_users_ids );

					$query_for_dropdown = [
						'show_option_all' => __( 'Guest' ),
						'name'            => 'customer_id',
						'class'           => 'wc-customer-search',
						'orderby'         => 'display_name',
						'order'           => 'ASC',
						'include'         => $users_ids_list
					];

					wp_dropdown_users( $query_for_dropdown );

					?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="bookable_product_id"><?php esc_html_e( 'Bookable Product',
							'recurring-bookings-for-woocommerce' ); ?></label>
                </th>
                <td>
                    <select id="bookable_product_id" name="bookable_product_id" class="chosen_select"
                            style="width: 300px">
                        <option value=""><?php esc_html_e( 'Select a bookable product...',
								'recurring-bookings-for-woocommerce' ); ?></option>
						<?php
						foreach ( WC_Bookings_Admin::get_booking_products() as $product ) { ?>
                                <option  value="<?php echo esc_attr( $product->get_id() ); ?>"><?php echo esc_html( sprintf( '%s (#%s)',
										$product->get_name(), $product->get_id() ) ); ?></option>
								<?php
						} ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="create_order"><?php esc_html_e( 'Create Order', 'recurring-bookings-for-woocommerce' ); ?></label>
                </th>
                <td>
                    <p>
                        <label>
                            <input type="radio" name="booking_order" value="new" class="checkbox"/>
							<?php esc_html_e( 'Create a new corresponding order for this new booking. Please note - the booking will not be active until the order is processed/completed.',
								'recurring-bookings-for-woocommerce' ); ?>
                        </label>
                    </p>
                    <p>
                        <label>
                            <input type="radio" name="booking_order" value="existing" class="checkbox"/>
							<?php esc_html_e( 'Assign this booking to an existing order with this ID:',
								'recurring-bookings-for-woocommerce' ); ?>
							<?php if ( class_exists( 'WC_Seq_Order_Number_Pro' ) ) : ?>
                                <input type="text" name="booking_order_id" value="" class="text" size="15"/>
							<?php else : ?>
                                <input type="number" name="booking_order_id" value="" class="text" size="10"/>
							<?php endif; ?>
                        </label>
                    </p>
                    <p>
                        <label>
                            <input type="radio" name="booking_order" value="" class="checkbox" checked="checked"/>
							<?php esc_html_e( 'Don\'t create an order for this booking.', 'recurring-bookings-for-woocommerce' ); ?>
                        </label>
                    </p>
                </td>
            </tr>
			<?php do_action( 'woocommerce_bookings_after_create_booking_page' ); ?>
            <tr valign="top">
                <th scope="row">&nbsp;</th>
                <td>
                    <input type="submit" name="create_booking" class="button-primary rbwc-submit"
                           value="<?php esc_attr_e( 'Next', 'recurring-bookings-for-woocommerce' ); ?>"/>
					<?php wp_nonce_field( 'create_booking_notification' ); ?>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
