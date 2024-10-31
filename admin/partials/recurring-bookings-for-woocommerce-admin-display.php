<?php

/**
 * Provide an admin area view for the advanced version of the plugin
 *
 **/
?>

<?php 
/**
 * The form to be loaded on the plugin's admin page
 */

if ( current_user_can( 'edit_users' ) ) {
    // Populate the dropdown list with exising users.
    $dropdown_html_users = '<select required id="rbwc_user_select" name="rbwc[user_select]">
  <option value="">' . __( 'Select a User', $this->recurring_bookings_for_woocommerce ) . '</option>';
    $wp_users = get_users( array(
        'fields' => array( 'user_login', 'display_name' ),
    ) );
    foreach ( $wp_users as $user ) {
        $user_login = esc_html( $user->user_login );
        $user_display_name = esc_html( $user->display_name );
        $dropdown_html_users .= '<option value="' . $user_login . '">' . $user_login . ' (' . $user_display_name . ') ' . '</option>' . "\n";
    }
    $dropdown_html_users .= '</select>';
    $args = array(
        'type'  => 'booking',
        'limit' => -1,
    );
    $bookable_products = wc_get_products( $args );
    $dropdown_html_products = '<select required id="rbwc_product_select" name="rbwc[product_select]">
  <option value="">' . __( 'Select a Product', $this->recurring_bookings_for_woocommerce ) . '</option>';
    foreach ( $bookable_products as $bookable ) {
        $booking_ID = $bookable->get_id();
        $booking_name = $bookable->get_name();
        $booking_duration = $bookable->get_duration();
        $booking_duration_type = $bookable->get_duration_type();
        $booking_duration_unit = $bookable->get_duration_unit();
        $booking_first_block_time = $bookable->get_first_block_time();
        
        if ( !$bookable->has_resources() || !$bookable->is_resource_assignment_type( 'customer' ) ) {
            $booking_has_resources = 0;
        } else {
            $booking_has_resources = 1;
        }
        
        $booking_has_persons = ( $bookable->has_persons() ? 1 : 0 );
        $dropdown_html_products .= '<option data-first-block-time="' . $booking_first_block_time . '" data-booking-duration="' . $booking_duration . '" data-booking-duration-type="' . $booking_duration_type . '" data-booking-duration-unit="' . $booking_duration_unit . '" data-booking-has-resources="' . $booking_has_resources . '" data-booking-has-persons="' . $booking_has_persons . '" value="' . $booking_ID . '">' . $booking_name . '</option>' . "\n";
    }
    $dropdown_html_products .= '</select>';
    // Generate a custom nonce value.
    $rbwc_recurring_booking_nonce = wp_create_nonce( 'rbwc_recurring_booking_form_nonce' );
    // Build the Form
    ?>
    <div class="rbwc_recurring_booking_form">
		<?php 
    
    if ( rbwc_fs()->is_not_paying() ) {
        ?>
            <div class="notice rbwc-upgrade-panel">
                <h3><?php 
        _e( 'Why Upgrade?', 'recurring-bookings-for-woocommerce' );
        ?></h3>
                <p style="font-weight: bold"><?php 
        printf( __( 'Take your recurring bookings to the next level with the <a href="%s">Professional Edition</a>', 'recurring-bookings-for-woocommerce' ), esc_url( rbwc_fs()->get_upgrade_url() ) );
        ?></p>
                <ul>
                    <li><?php 
        _e( 'Let customers make recurring bookings on the frontend', 'recurring-bookings-for-woocommerce' );
        ?></li>
                    <li><?php 
        _e( 'Improved recurrence date management', 'recurring-bookings-for-woocommerce' );
        ?></li>
                    <li><?php 
        _e( 'Advanced error and clash handling', 'recurring-bookings-for-woocommerce' );
        ?></li>
                    <li><?php 
        _e( 'WooCommerce Subscriptions integration', 'recurring-bookings-for-woocommerce' );
        ?></li>
                </ul>
                <p style="font-weight: bold"><?php 
        printf( __( 'If the free version has halved the time it takes to make repeated bookings, imagine how much time customers making their own bookings could save. <a href="%s">Upgrade now!</a>', 'recurring-bookings-for-woocommerce' ), esc_url( rbwc_fs()->get_upgrade_url() ) );
        ?></p>
                <p style="font-weight: bold"><?php 
        printf( __( 'To see all Professional Edition features, head to our <a href="%s">website</a>', 'recurring-bookings-for-woocommerce' ), esc_url( 'https://www.recurringbookings.com' ) );
        ?></p>
            </div>
		<?php 
    }
    
    ?>
        <h3><?php 
    _e( 'Getting Started', 'recurring-bookings-for-woocommerce' );
    ?></h3>
        <ol>
            <li><?php 
    _e( 'Set your products to recurrable, by ticking the box at the top of the product data panel.', 'recurring-bookings-for-woocommerce' );
    ?></li>
            <li><?php 
    _e( 'Click the \'Recurring\' panel to access the settings for this product.', 'recurring-bookings-for-woocommerce' );
    ?></li>
            <li><?php 
    _e( 'Choose whether the product will have a fixed length and period (and if so, complete the boxes). If you decide the length and period of the booking is flexible, you will enter them at the point of making a booking.', 'recurring-bookings-for-woocommerce' );
    ?></li>
        </ol>
        <img src="<?php 
    echo  plugin_dir_url( __DIR__ ) . '//assets/product.png' ;
    ?>">
        <h3><?php 
    _e( 'Make Your Booking', 'recurring-bookings-for-woocommerce' );
    ?></h3>
        <p><?php 
    _e( 'With your product set, you are now ready to make your recurring bookings. Click the \'Add Booking\' link in the menu. The booking form is functionally similar to the native booking form, with the addition of the logic that allows you to repeat your bookings.', 'recurring-bookings-for-woocommerce' );
    ?></p>
        <p><?php 
    _e( 'If the booking length and period are fixed, just select a booking. Otherwise, enter the length and period in the boxes below the calendar. The plugin automatically works out the details of the repeat bookings, checks their availability, calculates the total cost, and shows them to you. If you are happy with the selection, go ahead and make your bookings. We then suggest you confirm they have all been created using the calendar of the bookings list.', 'recurring-bookings-for-woocommerce' );
    ?></p>
        <p><?php 
    _e( 'Prior to version 2 of the plugin, you could create bookings on this page. In this version, we have changed the way the plugin works with WooCommerce Bookings to check availability and process the repeat bookings. Using the new \'Add Booking\' form will provide better results and is more user-friendly. However, if you would prefer to make your bookings using the legacy method, click the button below to open the form.', 'recurring-bookings-for-woocommerce' );
    ?></p>
        <a id="rbwc-toggle-legacy-form" class="button button-primary">Open Legacy Booking Form</a>
        <form action="<?php 
    echo  esc_url( admin_url( 'admin-post.php' ) ) ;
    ?>" method="post"
              id="rbwc_recurring_booking_form" style="display: none">

            <h3><?php 
    _e( 'Select user', $this->recurring_bookings_for_woocommerce );
    ?></h3>
			<?php 
    echo  $dropdown_html_users ;
    ?>

            <h3><?php 
    _e( 'Select product', $this->recurring_bookings_for_woocommerce );
    ?></h3>
			<?php 
    echo  $dropdown_html_products ;
    ?>


            <div class="rbwc_timecontainer">
                <div>
                    <h3><?php 
    _e( 'Start time', $this->recurring_bookings_for_woocommerce );
    ?></h3>
                    <input class="new-timepicker" required id="rbwc_advanced_start" name="rbwc[advanced_start]">
                </div>
                <div class="time_arrow">
                    <p>&#8594;</p>
                </div>
                <div>
                    <h3><?php 
    _e( 'Finish time', $this->recurring_bookings_for_woocommerce );
    ?></h3>
                    <input class="new-timepicker" required id="rbwc_advanced_finish" name="rbwc[advanced_finish]">
                </div>
            </div>

            <div id="booking-duration-prompt" hidden><p></p></div>
            <div class="rbwc_resource_container" hidden></div>
            <div class="rbwc_persons_container" hidden></div>

			<?php 
    echo  '<img src="' . plugin_dir_url( __DIR__ ) . 'assets/order-creation.png" alt="">' ;
    ?>

            <div class="rbwc_datecontainer">
                <h3><?php 
    _e( 'Date selection', $this->recurring_bookings_for_woocommerce );
    ?></h3>
                <div id="rbwc_mode_select_container">
                    <p><?php 
    _e( 'You can select dates using two different modes. Freestyle mode is useful where a client or customer gives you a range of different and unrelated dates and you would like to get them all booked in one go. A use case might be a commitee meeting that aims to be within the first week of every month. Meanwhile, fixed mode may be useful when a client or customer requires the same time every week, for example, a weekly yoga class.', $this->recurring_bookings_for_woocommerce );
    ?></p>
                    <p>
                        <label>
                            <input type="radio" name="rbwc[mode_select]" value="fixed" checked="checked"
                                   class="rbwc_mode"/>
							<?php 
    _e( 'Fixed mode - the same time every ', $this->recurring_bookings_for_woocommerce );
    ?>
                        </label>
                        <label>
                            <select name="rbwc[factor]" id="rbwc_factor">
                                <option class="rbwc_factor_option" value="day">Day</option>
                                <option class="rbwc_factor_option" value="week">Week</option>
                                <option class="rbwc_factor_option" value="month">Month</option>
                            </select>
							<?php 
    _e( 'repeating for a total of ', $this->recurring_bookings_for_woocommerce );
    ?>
                        </label>
                        <input type="number" name="rbwc[multiplier]" id="rbwc_multiplier" value="1"
                               placeholder="enter a number" min="1" class="text" size="15"/>
                        <span id="rbwc_factor_display">days</span>.
                    </p>
                    <p>
                        <label>
                            <input type="radio" name="rbwc[mode_select]" value="freestyle" class="rbwc_mode"/>
							<?php 
    _e( 'Freestyle mode - choose multiple dates below', $this->recurring_bookings_for_woocommerce );
    ?>
                        </label>
                    </p>
                </div>

                <div class="rbwc_helpers">
                    <h4><?php 
    _e( "As well as picking dates directly, you may also use the helpers below to automatically select certain date patterns. Give them a try to see what they do. To change your mind, just click that date again to de-select it, or use the 'clear calendar' button.", $this->recurring_bookings_for_woocommerce );
    ?></h4>
                    <div class="rbwc_helper_range">
                        <h4><?php 
    _e( 'Choose a date range:', $this->recurring_bookings_for_woocommerce );
    ?></h4>
                        <label><input id="advanced_date_range_1" type="radio" name="rbwc_advanced_date_range" value="30"
                                      checked>30 Days</label><br>
                        <label><input id="advanced_date_range_2" type="radio" name="rbwc_advanced_date_range"
                                      value="60">60 Days</label><br>
                        <label><input id="advanced_date_range_3" type="radio" name="rbwc_advanced_date_range"
                                      value="90">90 Days</label><br>
                        <label><input id="advanced_date_range_4" type="radio" name="rbwc_advanced_date_range"
                                      value="180">180 Days</label><br>
                        <label><input id="advanced_date_range_5" type="radio" name="rbwc_advanced_date_range"
                                      value="365">365 Days</label><br>
                    </div>
                    <div class="rbwc_helper_request">
                        <h4><?php 
    _e( 'Choose the dates you wish to pre-select:', $this->recurring_bookings_for_woocommerce );
    ?></h4>
                        <label><input id="advanced_date_request_1" type="radio" name="rbwc_advanced_date_request"
                                      value="weekdays">Every weekday</label><br>
                        <label><input id="advanced_date_request_2" type="radio" name="rbwc_advanced_date_request"
                                      value="weekends">Every weekend (Saturday and Sunday)</label><br>
                        <label><input id="advanced_daterequeste_3" type="radio" name="rbwc_advanced_date_request"
                                      value="all">Every day</label><br>
                    </div>
                    <br>
                    <input type="button" name="rbwc_button_fill" id="rbwc_button_fill" class="button button-primary"
                           value="Fill with selected">
                    <input type="button" name="rbwc_button_clear" id="rbwc_button_clear" class="button button-primary"
                           value="Clear calendar">
                </div>
                <br>
                <div id="mdp"></div>
            </div>


            <input type="hidden" id="mdpAltField" name="rbwc[advanced_dates]">

			<?php 
    echo  '<img src="' . plugin_dir_url( __DIR__ ) . 'assets/error-handling.png" alt="">' ;
    ?>

            <input type="hidden" name="action" value="rbwc_form_response">
            <input type="hidden" name="rbwc_recurring_booking_nonce"
                   value="<?php 
    echo  $rbwc_recurring_booking_nonce ;
    ?>"/>

            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                     value="Create Bookings"></p>
        </form>
        <div id="rbwc_form_feedback"></div>
    </div>

	<?php 
} else {
    ?>
    <p> <?php 
    __( "You are not authorized to perform this operation.", $this->recurring_bookings_for_woocommerce );
    ?> </p>
	<?php 
}
