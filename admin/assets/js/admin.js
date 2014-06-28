(function ( $ ) {
	"use strict";

	$(function () {

        /**
         * Show expanded wptm fields when admin choose to use category as tutorial
         */
        if($('#wp_tutorial_maker').val() == 1) {
            $('.wp_tutorial_maker_more_options').show();
        }
		$('#wp_tutorial_maker').on('change', function() {
           if($(this).val() == 1) {
               $('.wp_tutorial_maker_more_options').show();
           } else {
               $('.wp_tutorial_maker_more_options').hide();
           }
        });

	});

}(jQuery));