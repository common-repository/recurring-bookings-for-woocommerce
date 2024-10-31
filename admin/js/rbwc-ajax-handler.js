jQuery( document ).ready( function( $ ) {

  "use strict";
  /**
  * The file is enqueued from inc/admin/class-admin.php.
  */
  $( '#rbwc_recurring_booking_form' ).submit( function( event ) {

    event.preventDefault(); // Prevent the default form submit.

    // serialize the form data
    var ajax_form_data = $("#rbwc_recurring_booking_form").serialize();

    //add our own ajax check as X-Requested-With is not always reliable
    ajax_form_data = ajax_form_data+'&ajaxrequest=true&submit=Submit+Form';

    $.ajax({
      url:    params.ajaxurl, // domain/wp-admin/admin-ajax.php
      type:   'post',
      data:   ajax_form_data
    })

    .done( function( response ) { // response from the PHP action
      $(" #rbwc_form_feedback ").html( response );
    })

    // something went wrong
    .fail( function( response ) {
      $(" #rbwc_form_feedback ").html( "<h2>Something went wrong</h2>" );
    })

    // after all this time?
    .always( function() {
      event.target.reset();
    });

  });

  $('#rbwc_product_select').on('change', function () {

    if( $(this).find(':selected').data('booking-has-resources') == true ) {
      var data = $(this).val();
      console.log(data);

      $.ajax({
        url:    params.ajaxurl, // domain/wp-admin/admin-ajax.php
        type:   'post',
        data:   {
          'action': 'rbwc_resource_dropdown',
          'data': data
        }
      })

      .done( function( response ) { // response from the PHP action
        $('.rbwc_resource_container').css('display', 'grid');
        $('.rbwc_resource_container').html( response );
      })

      // something went wrong
      .fail( function() {
        alert("oops");
      })
    }

    if( ($(this).find(':selected').data('booking-has-resources') == false) || ($(this)[0].selectedIndex <= 0) ) {
      $('.rbwc_resource_container').css('display', 'none');
      $('.rbwc_resource_container').html("");
    }





  });

  $('#rbwc_product_select').on('change', function () {
    if( $(this).find(':selected').data('booking-has-persons') == true ) {
      var data = $(this).val();
console.log(data);
      $.ajax({
        url:    params.ajaxurl, // domain/wp-admin/admin-ajax.php
        type:   'post',
        data:   {
          'action': 'rbwc_persons_processor',
          'data': data
        }
      })

      .done( function( response ) { // response from the PHP action
        $('.rbwc_persons_container').css('display', 'grid');
        $('.rbwc_persons_container').html( response );
      })

      // something went wrong
      .fail( function() {
        alert("oops");
      })
    }

    if( ($(this).find(':selected').data('booking-has-persons') == false) || ($(this)[0].selectedIndex <= 0) ) {
      $('.rbwc_persons_container').css('display', 'none');
      $('.rbwc_persons_container').html("");
    }
  });
});
