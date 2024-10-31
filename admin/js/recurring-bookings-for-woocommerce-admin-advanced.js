(function ($) {
    'use strict';

    $(function () {

        if ($('#wc_bookings_field_recur_interval').length) {
            var $singular = $('input[name="wc_bookings_field_recur_period"]:checked').attr("data-singular");
            var $plural = $('input[name="wc_bookings_field_recur_period"]:checked').attr("data-plural");
            $('.wc_bookings_field_recur_interval > .select-after-singular').text($singular);
            $('.wc_bookings_field_recur_interval > .select-after-plural').text($plural);

            $('.wc_bookings_field_recur_interval > .select-after-singular').show();
            $('.wc_bookings_field_recur_interval > .select-after-plural').hide();

            $('#wc_bookings_field_recur_interval').on('change', function () {

                if ($(this).val() == 1) {
                    $('.wc_bookings_field_recur_interval > .select-after-singular').show();
                    $('.wc_bookings_field_recur_interval > .select-after-plural').hide();
                } else {
                    $('.wc_bookings_field_recur_interval > .select-after-singular').hide();
                    $('.wc_bookings_field_recur_interval > .select-after-plural').show();
                }
            });
        }

        if ($('.wc-bookings-booking-form > fieldset').length) {

            $('.rbwc-series-month-rules').hide();
            $("input[name='wc_bookings_field_recur_period']").click(function () {
                var radioValue = $("input[name='wc_bookings_field_recur_period']:checked").val();
                if (radioValue == 'month') {
                    $('.rbwc-series-month-rules').show();
                } else {
                    $('.rbwc-series-month-rules').hide();
                }

                $singular = $('input[name="wc_bookings_field_recur_period"]:checked').attr("data-singular");
                $plural = $('input[name="wc_bookings_field_recur_period"]:checked').attr("data-plural");
                $('.wc_bookings_field_recur_interval .select-after-singular').text($singular);
                $('.wc_bookings_field_recur_interval .select-after-plural').text($plural);

                if ($('#wc_bookings_field_recur_interval').val() == 1) {
                    $('.wc_bookings_field_recur_interval .select-after-singular').show();
                    $('.wc_bookings_field_recur_interval .select-after-plural').hide();
                } else {
                    $('.wc_bookings_field_recur_interval .select-after-singular').hide();
                    $('.wc_bookings_field_recur_interval .select-after-plural').show();
                }
            });

            function ordinal_suffix_of(i) {
                var j = i % 10,
                    k = i % 100;
                if (j == 1 && k != 11) {
                    return i + "st";
                }
                if (j == 2 && k != 12) {
                    return i + "nd";
                }
                if (j == 3 && k != 13) {
                    return i + "rd";
                }
                return i + "th";
            }

            $('.rbwc-series').hide();

            var current = new Date();
            $('.wc_bookings_field_recur_month_rule_0_result_same > span').text(ordinal_suffix_of(current.getDate()));
            $('.wc_bookings_field_recur_month_rule_0_result_same').show();
            $('.wc_bookings_field_recur_month_rule_0_result_different_the').hide();
            $('.wc_bookings_field_recur_month_rule_0_result_different_day').hide();
            $('.wc_bookings_field_recur_month_rule_1').hide();
            $('.wc_bookings_field_recur_month_rule_2').hide();
            $('.rbwc-series-header').hide();

            $(".wc-bookings-booking-form > fieldset").on('date-selected', function (event, fdate) {

                $('.rbwc-series').not('.wc_bookings_field_recur_month_rule_1, .wc_bookings_field_recur_month_rule_2').show();
                $('.rbwc-series-header').show();
                var date = new Date(Date.parse(fdate)), // The selected DATE Object
                    startDate = date.getDate(), // Day in numeric value from 1 to 31
                    ordinaled = ordinal_suffix_of(startDate); // Add our ordinal
                $('.rbwc-series-month-rules').attr("data-rbwcdate", startDate);
                $('.wc_bookings_field_recur_month_rule_0_result_same > span').text(ordinaled);
                $('#wc_booking_rbwc_recur_end_date').datepicker({
                    dateFormat: 'dd-mm-yy',
                    minDate: date,
                    defaultDate: +7
                });
            });

            $('.wc_bookings_field_recur_month_rule_1').hide();
            $('.wc_bookings_field_recur_month_rule_2').hide();

            $('#wc_bookings_field_recur_month_rule_0').on('change', function () {
                switch ($(this).val()) {
                    case 'same':
                        $('.wc_bookings_field_recur_month_rule_0_result_same').show();
                        $('.wc_bookings_field_recur_month_rule_0_result_different_the').hide();
                        $('.wc_bookings_field_recur_month_rule_0_result_different_day').hide();
                        $('.wc_bookings_field_recur_month_rule_1').hide();
                        $('.wc_bookings_field_recur_month_rule_2').hide();
                        break;
                    case 'different':
                        $('.wc_bookings_field_recur_month_rule_0_result_same').hide();
                        $('.wc_bookings_field_recur_month_rule_0_result_different_the').show();
                        $('.wc_bookings_field_recur_month_rule_1').show();
                        $('.wc_bookings_field_recur_month_rule_2').show();
                        $('.wc_bookings_field_recur_month_rule_2_result').show();
                        break;
                }
            });

            $('#wc_bookings_field_recur_month_rule_1').on('change', function () {
                switch ($(this).val()) {
                    case 'first':
                    case 'second':
                    case 'third':
                    case 'fourth':
                    case 'fifth':
                    case 'last':
                        $('.wc_bookings_field_recur_month_rule_0_result_different_the').show();
                        $('.wc_bookings_field_recur_month_rule_0_result_different_day').hide();
                        $('.wc_bookings_field_recur_month_rule_2').show();
                        $('.wc_bookings_field_recur_month_rule_2_result').show();
                        break;
                    default:
                        $('.wc_bookings_field_recur_month_rule_0_result_same').hide();
                        $('.wc_bookings_field_recur_month_rule_0_result_different_the').hide();
                        $('.wc_bookings_field_recur_month_rule_0_result_different_day').show();
                        $('.wc_bookings_field_recur_month_rule_2').hide();
                        $('.wc_bookings_field_recur_month_rule_2_result').show();
                        break;
                }
            });
        } else {
            $('.wc_bookings_field_recur_period').hide();
            $('.rbwc-series-month-rules').hide();
            $('#wc_booking_rbwc_recur_end_date').datepicker({
                dateFormat: 'dd-mm-yy',
                minDate: 0
            });

        }


    });

    $(function () {
        if ($('#rbwc_recurring_booking_form').length > 0) {
            $('#rbwc-toggle-legacy-form').on('click', function () {
                $('#rbwc_recurring_booking_form').toggle(400);
            });
        }

    });

    $(function () {
        if ($('.wc_booking_page_create_recurring_booking').length > 0) {
            $('.wc_booking_page_create_recurring_booking .wc-customer-search').select2({
                minimumInputLength: 1
            });
            $('.wc_booking_page_create_recurring_booking #bookable_product_id').select2({
                placeholder: "Select a bookable product...",
            });
        }

    });

    $(function () {

        var $public, $fixed_length, $fixed_period, $fixed_length_customer, $fixed_period_customer;

        var $rl = $("._wc_booking_rbwc_recur_length_field"),
            $rp = $("._wc_booking_rbwc_recur_period_field"),
            $rlct = $('._wc_booking_rbwc_recur_length_fixed_customer_field'),
            $rpct = $('._wc_booking_rbwc_recur_period_fixed_customer_field'),
            $rlc = $('._wc_booking_rbwc_recur_length_customer_field'),
            $rpc = $('._wc_booking_rbwc_recur_period_customer_field'),
            $rlm = $('._wc_booking_rbwc_recur_customer_defined_max_field');

        $('#_recurrable').is(":checked") ? $("#recurring-options, .rbwc_bookings_tab").show() : $("#recurring-options, .rbwc_bookings_tab").hide(), $("ul.wc-tabs li:visible").eq(0).find("a").click();
        $('#_recurrable').on('change', function () {
            $(this).is(":checked") ? $("#recurring-options, .rbwc_bookings_tab").show() : $("#recurring-options, .rbwc_bookings_tab").hide(), $("ul.wc-tabs li:visible").eq(0).find("a").click();
        });

        // Get our different cases

        $('#_wc_booking_rbwc_recur_public').is(":checked") ? $public = true : $public = false;
        $('#_wc_booking_rbwc_recur_public').on('change', function () {
            $(this).is(":checked") ? $public = true : $public = false;
        });

        $('#_wc_booking_rbwc_recur_length_fixed').is(":checked") ? $fixed_length = true : $fixed_length = false;
        $('#_wc_booking_rbwc_recur_length_fixed').on('change', function () {
            $(this).is(":checked") ? $fixed_length = true : $fixed_length = false;
        });

        $('#_wc_booking_rbwc_recur_period_fixed').is(":checked") ? $fixed_period = true : $fixed_period = false;
        $('#_wc_booking_rbwc_recur_period_fixed').on('change', function () {
            $(this).is(":checked") ? $fixed_period = true : $fixed_period = false;
        });

        $('#_wc_booking_rbwc_recur_length_fixed_customer').is(":checked") ? $fixed_length_customer = true : $fixed_length_customer = false;
        $('#_wc_booking_rbwc_recur_length_fixed_customer').on('change', function () {
            $(this).is(":checked") ? $fixed_length_customer = true : $fixed_length_customer = false;
        });

        $('#_wc_booking_rbwc_recur_period_fixed_customer').is(":checked") ? $fixed_period_customer = true : $fixed_period_customer = false;
        $('#_wc_booking_rbwc_recur_period_fixed_customer').on('change', function () {
            $(this).is(":checked") ? $fixed_period_customer = true : $fixed_period_customer = false;
        });

        if ($fixed_length == true) {
            $rl.show();
            $rlm.add($rlc).add($rlct).hide();
        } else {
            $rl.hide();
            $rlct.show();
        }
        if ($fixed_length == false && $public == true && $fixed_length_customer == true) {
            $rlc.show();
        } else {
            $rlc.hide();
        }
        if ($fixed_length == false && $public == true && $fixed_length_customer == false) {
            $rlm.show();
        } else {
            $rlm.hide();
        }
        if ($fixed_period == true) {
            $($rp).show();
            $rpc.add($rpct).hide();
        } else {
            $rp.hide();
            $rpct.show();
        }
        if ($fixed_period == false && $public == true && $fixed_period_customer == true) {
            $rpc.show();
        } else {
            $rpc.hide();
        }
        if ($public == false) {
            $rlm.add($rlc).add($rlct).add($rpc).add($rpct).hide();
        }

        $('#_wc_booking_rbwc_recur_public, #_wc_booking_rbwc_recur_length_fixed, #_wc_booking_rbwc_recur_period_fixed, #_wc_booking_rbwc_recur_length_fixed_customer, #_wc_booking_rbwc_recur_period_fixed_customer').on('change', function () {
            if ($fixed_length == true) {
                $($rl).show();
                $rlm.add($rlc).add($rlct).hide();
            } else {
                $rl.hide();
                $rlct.show();
            }
            if ($fixed_length == false && $public == true && $fixed_length_customer == true) {
                $rlc.show();
            } else {
                $rlc.hide();
            }
            if ($fixed_length == false && $public == true && $fixed_length_customer == false) {
                $rlm.show();
            } else {
                $rlm.hide();
            }
            if ($fixed_period == true) {
                $($rp).show();
                $rpc.add($rpct).hide();
            } else {
                $rp.hide();
                $rpct.show();
            }
            if ($fixed_period == false && $public == true && $fixed_period_customer == true) {
                $rpc.show();
            } else {
                $rpc.hide();
            }
            if ($public == false) {
                $rlm.add($rlc).add($rlct).add($rpc).add($rpct).hide();
            }
        });

        $('#_wc_booking_rbwc_subscriptions_toggle').is(":checked") ? $(".recurring-options-subscriptions-inner").show() : $(".recurring-options-subscriptions-inner").hide();
        $('#_wc_booking_rbwc_subscriptions_toggle').on('change', function () {
            $(this).is(":checked") ? $(".recurring-options-subscriptions-inner").show() : $(".recurring-options-subscriptions-inner").hide();
        });

        $('#_wc_booking_rbwc_subscriptions_behaviour').val() == 'discountable' ? $(".rbwc_subscription_discount_per_base_booking_field, .rbwc_subscription_discount_per_recurrence_field").show() : $(".rbwc_subscription_discount_per_base_booking_field, .rbwc_subscription_discount_per_recurrence_field").hide();
        $('#_wc_booking_rbwc_subscriptions_behaviour').on('change', function () {
            $(this).val() == 'discountable' ? $(".rbwc_subscription_discount_per_base_booking_field, .rbwc_subscription_discount_per_recurrence_field ").show() : $(".rbwc_subscription_discount_per_base_booking_field, .rbwc_subscription_discount_per_recurrence_field ").hide();
        });

    });

    $(function () {

        $('input.new-timepicker').timepicker({
            timeFormat: 'HH:mm',
            dropdown: false
        });

        var today = new Date();
        $('#mdp').multiDatesPicker({
            numberOfMonths: [4, 4],
            minDate: 0,
            maxPicks: 1,
            altField: '#mdpAltField',
            dateFormat: "dd-mm-yy",
            defaultDate: today
        });

        $('.rbwc_helpers').hide();

        $('.rbwc_mode').on('change', function () {
            switch ($(this).val()) {
                case 'freestyle':
                    $('.rbwc_helpers').show();
                    var today = new Date();
                    $('#mdp').multiDatesPicker({
                        numberOfMonths: [4, 4],
                        maxPicks: 365,
                        minDate: 0,
                        altField: '#mdpAltField',
                        dateFormat: "dd-mm-yy",
                        defaultDate: today
                    });
                    break;
                case 'fixed':
                    $('.rbwc_helpers').hide();
                    $('#mdp').multiDatesPicker('resetDates');
                    var today = new Date();
                    $('#mdp').multiDatesPicker({
                        numberOfMonths: [4, 4],
                        maxPicks: 1,
                        minDate: 0,
                        altField: '#mdpAltField',
                        dateFormat: "dd-mm-yy",
                        defaultDate: today
                    });
                    break;
            }
        });

        $('#rbwc_factor').on('change', function () {
            switch ($(this).val()) {
                case 'day':
                    $('#rbwc_factor_display').html('days');
                    break;
                case 'week':
                    $('#rbwc_factor_display').html('weeks');
                    break;
                case 'month':
                    $('#rbwc_factor_display').html('months');
                    break;
            }
        });

        $('#rbwc_product_select').on('change', function () {
            // Ignore default value
            if (!$(this)[0].selectedIndex <= 0) {

                var $booking_duration = $(this).find(':selected').data('booking-duration');
                var $booking_duration_type = $(this).find(':selected').data('booking-duration-type');
                var $booking_duration_unit = $(this).find(':selected').data('booking-duration-unit');
                var $booking_first_block_time = $(this).find(':selected').data('first-block-time');

                var $first_block_time_statement = "";
                if ($booking_first_block_time) {
                    var $first_block_time_statement = "The product has also been given a first available booking time of " + $booking_first_block_time + ". This means that you cannot make any bookings before that time.<br><br>"
                } else {
                    var $first_block_time_statement = "This product does not appear to have an earliest available time set.<br><br>"
                }

                var $final_reminder = "Please ensure the times you choose are consistent with how the product was set up, for instance, starting on the hour. A quick way of checking time compatibility is to look at the calendar displayed for that product on the front-end, as if you were making a single booking. If a time is available there, it can be used here. For more examples, see the plugin help page. If no bookings are created for you, time compatibility problems will be the reason in the vast majority of cases.";

                if ($booking_duration_type == "fixed") {
                    var $html = "The product you have selected uses " + $booking_duration + " " + $booking_duration_unit + " fixed blocks. This means that your booking has to be exactly " + $booking_duration + " " + $booking_duration_unit + "(s) long.<br><br>";
                } else {
                    var $html = "The product you have selected uses " + $booking_duration + " " + $booking_duration_unit + " customer defined blocks. Customer defined blocks allow you to make bookings as long as you want, however they must be a multiple of " + $booking_duration + " " + $booking_duration_unit + "(s).<br><br>";
                }

                $('#booking-duration-prompt p').html($html + $first_block_time_statement + $final_reminder);
                $('#booking-duration-prompt').show();

            } else {
                $('#booking-duration-prompt').hide();
            }
        });

        var $helper_range;
        var $helper_request;
        var dateArray = [];

        function getDateArray(days) {
            var currentDate = moment();
            var stopDate = moment(currentDate).add(days, 'days');
            while (currentDate <= stopDate) {
                var orig = moment(currentDate).toDate();
                dateArray.push(orig);
                currentDate = moment(currentDate).add(1, 'days');
            }
            return dateArray;
        }

        function getDateArrayWD(days) {
            var currentDate = moment();
            var stopDate = moment(currentDate).add(days, 'days');
            while (currentDate <= stopDate) {
                if (currentDate.isoWeekday() !== 6 && currentDate.isoWeekday() !== 7) {
                    var orig = moment(currentDate).toDate();
                    dateArray.push(orig);
                    currentDate = moment(currentDate).add(1, 'days');
                } else {
                    currentDate = moment(currentDate).add(1, 'days');
                }
            }
            return dateArray;
        }

        function getDateArrayWE(days) {
            var currentDate = moment();
            var stopDate = moment(currentDate).add(days, 'days');
            while (currentDate <= stopDate) {
                if (currentDate.isoWeekday() !== 6 && currentDate.isoWeekday() !== 7) {
                    currentDate = moment(currentDate).add(1, 'days');
                } else {
                    var orig = moment(currentDate).toDate();
                    dateArray.push(orig);
                    currentDate = moment(currentDate).add(1, 'days');
                }
            }
            return dateArray;
        }

        $('#rbwc_button_fill').on('click', function () {
            dateArray.length = 0;
            $('#mdp').multiDatesPicker('resetDates');
            $helper_range = $('input[name=rbwc_advanced_date_range]:checked').val();

            if ($('input[name=rbwc_advanced_date_request]:checked').val() == "weekdays") {
                getDateArrayWD($helper_range);
                $('#mdp').multiDatesPicker('addDates', dateArray);
            } else if ($('input[name=rbwc_advanced_date_request]:checked').val() == "weekends") {
                getDateArrayWE($helper_range);
                $('#mdp').multiDatesPicker('addDates', dateArray);
            } else if ($('input[name=rbwc_advanced_date_request]:checked').val() == "all") {
                getDateArray($helper_range);
                $('#mdp').multiDatesPicker('addDates', dateArray);
            }

        });

        $('#rbwc_button_clear').on('click', function () {
            dateArray.length = 0;
            $('#mdp').multiDatesPicker('resetDates');
        });

    });

})(jQuery);
