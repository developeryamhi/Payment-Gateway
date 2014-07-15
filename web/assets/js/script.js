//  Use No Conflict jQuery
$.noConflict();


//  Payment Formatter
jQuery(function($) {

    //  Get the Rates
    /*$.ajax({
        url: 'http://openexchangerates.org/api/latest.json?app_id=temporary-1ba057ca38e2d94fe54',
        dataType: 'jsonp',
        success: function(data) {
            fx.rates = data.rates;
            fx.base = data.base;
        }
    });

    //  Listen Currency Change
    $("#payment-currency").change(function() {

        //  New Currency
        var newCur = $(this).val();

        //  Get Last Currency
        var lastCur = $(this).data('currency');

        //  Currency Amount
        var amount = $("#payment-amount").val();

        //  Try/Catch
        try {

            //  Convert
            $("#payment-amount").val(fx(1000).from(lastCur).to(newCur));
        } catch(e) {
            console.log(e);
        }

        //  Store
        $(this).data('currency', newCur);
    });*/

    //  Set Data
    $("#payment-currency").data('currency', $("#payment-currency").val());

    var validateDetails = function($holder) {

        // set variables for the expiry date validation, cvc validation and expiry date 'splitter'
        var expiry = $('.cc-exp', $holder).payment('cardExpiryVal');
        var validateExpiry = $.payment.validateCardExpiry(expiry["month"], expiry["year"]);
        var validateCVV = $.payment.validateCardCVC($('.cc-cvv', $holder).val());

        // if statement on whether the card’s expiry is valid or not
        if (validateExpiry) {

            // if the expiry is valid add the identified class
            $('.cc-exp', $holder).addClass('identified');
        } else {

            // remove again if the expiry becomes invalid
            $('.cc-exp', $holder).removeClass('identified');
        }

        // if statement on whether the card’s cvc is valid or not
        if (validateCVV) {

            // if the cvc is valid add the identified class
            $('.cc-cvc', $holder).addClass('identified');
        } else {

            // remove again if the cvc becomes invalid
            $('.cc-cvc', $holder).removeClass('identified');
        }

        //  Format
        $('.cc-exp', $holder).payment('formatCardExpiry');
        $('.cc-num', $holder).payment('formatCardNumber');
        $('.cc-cvc', $holder).payment('formatCardCVC');
    };

    // this runs the above function every time stuff is entered into the card inputs
    $('.paymentInput').bind('change paste keyup', function() {
        validateDetails($(this));
    });
    validateDetails($('.paymentInput'));

    //  Restrict
    $('[data-numeric]').payment('restrictNumeric');
});