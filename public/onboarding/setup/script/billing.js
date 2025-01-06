/*************************  Add class to Subscription plan  *************************/
$('.team-dash-sec .renew-single input:radio:checked').parent().addClass("active");
$('.team-dash-sec .renew-single  input:radio').click(function() {
    $('.team-dash-sec .renew-single  input:not(:checked)').parent().removeClass("active");
    $('.team-dash-sec .renew-single  input:checked').parent().addClass("active");
});

/*************************   Buttons in mobile  *************************/

$('.team-dash-sec .renew-select .next-btn').click(function() {
    $('#billing-box').show(500);
    $('#plan-boxes').hide(0);
});
$('.team-dash-sec .back-btn').click(function() {
    $('#plan-boxes').show(500);
    $('#billing-box').hide(0);
});

/********************     Input Counter  *************************/

$(document).ready(function() {
    if(total != 0 && (page == 'pricing' || page == 'renewal')){
        amountPerMonth = checkPrice($('.change_month').val())
        calculateAmount($('.change_month').val(),$('.change_users').val(),amountPerMonth,'default')
        fetchPromoCodeDetails($('.change_month').val(),$('.change_users').val())
        $('.user_price').html(amountPerMonth);
    }
    
    /* ----------- Users change --------------- */
    $('.team-dash-sec .upgrade-plans .minus').click(function() {
        var $input = $(this).parent().find('input');
        var count = parseInt($input.val()) - 1;
        count = count < 1 ? 1 : count;
        $input.val(count);
        $input.change();
        return false;
    });
    $('.team-dash-sec .upgrade-plans .plus').click(function() {
        var $input = $(this).parent().find('input');
        $input.val(parseInt($input.val()) + 1);
        $input.change();
        return false;
    });
    $('.team-dash-sec .upgrade-plans .change_users').change(function() {
        var $input = $(this).parent().find('input');
        amountPerMonth = checkPrice($('.change_month').val())
        if(page == 'renewal'){
            if($input.val() == min)
                $('.minus').addClass('disabled_minus')
            else
                $('.minus').removeClass('disabled_minus')

            if($input.val() < min){
                $('.user_price').html(amountPerMonth);
                $('.user_count').html($input.val());
                calculateAmount($('.change_month').val(),$input.val(),amountPerMonth,'default');
                fetchPromoCodeDetails($('.change_month').val(),$input.val())
                return false;
            }else{
                $('.user_price').html(amountPerMonth);
                $('.user_count').html($input.val());
                calculateAmount($('.change_month').val(),$input.val(),amountPerMonth,'default');
                fetchPromoCodeDetails($('.change_month').val(),$input.val())
                return false;
            }
        }else{
            $('.user_price').html(amountPerMonth);
            $('.user_count').html($input.val());
            calculateAmount($('.change_month').val(),$input.val(),amountPerMonth,'default');
            fetchPromoCodeDetails($('.change_month').val(),$input.val())

            return false;
        }
    });

    /* ----------- Month change --------------- */

    $('.change_month').change(function() {
        var $input = $(this).val();
        amountPerMonth = checkPrice($input)
        calculateAmount($input,$('.change_users').val(),amountPerMonth,'default');
        fetchPromoCodeDetails($input,$('.change_users').val())
        $('.user_price').html(amountPerMonth);
        $('.month_count').html($input);
        return false;
    });

    $('.see-all-coupen').click(function() {
        $('#coupons-modal').modal('toggle')
        return false;
    });
});


$(document).on('change','.renew-single input:radio',function(){
    if(this.value == "upgrade") {
        $('.upgrade-plans').css({'display':'flex'})
        amountPerMonth = checkPrice($('.change_month').val())
        calculateAmount($('.change_month').val(),$('.change_users').val(),amountPerMonth,'default');
        fetchPromoCodeDetails($('.change_month').val(),$('.change_users').val())
        $('.user_price').html(amountPerMonth);
        $('.pay-box').html('<button id="rzp-button1" class="razorpay-payment-button">Proceed to payment</button>');
        $('.user_count').html($('.change_users').val());
    }else{   
        $('.upgrade-plans').css({'display':'none'})
        loadCurrentPlan();
        if($('.change_users').val() == 0)
        $('.pay-box').html('<button id="rzp-button" class="razorpay-payment-button btn_upgdr">Please choose customize plan</button>');
    }
});

// Calculate payable amount
    function calculateAmount(month,users,amountPerMonth,flag = null){
        var total_amount = parseInt(month) * parseInt(users) * amountPerMonth;
        $('#price_crm').html('₹'+total_amount)

        if(!flag){
            var voucher = $('#promo_discount').attr('data-value');
            if(voucher)
                total_amount = total_amount - voucher;
        }else{
            $('#promo_discount').attr({
                "data-value": 0,
            });
            $('.you_save').html('');
            $('#promo_discount').html('₹00.00');
            $('#coupon_code').val('');
        }
        
        var gst = ( total_amount * 18 ) / 100;
        var final_amount = Math.ceil(gst + total_amount);

        $('#crm_gst').html('₹'+gst)
        $('#final_amount').html('₹'+final_amount)
        $('.payable_amount').html('₹'+final_amount)
        $(".payable_amount").attr({
            "data-value": final_amount,
        });
        return total_amount;
    }

// Calcumate promocode
    function calculatePromoCode(mode,value){
        amountPerMonth = checkPrice($('.change_month').val())
        var total_amount = calculateAmount($('.change_month').val(),$('.change_users').val(),amountPerMonth);
        var amount = 0
        switch (mode) {
            case 1:
                amount = Math.ceil(parseInt(total_amount) * (value/100));
                break;
            case 2:
                amount = Math.ceil(parseInt(total_amount) - value);
                break;
            default:
                break;
        }
        return amount;
    }

// Fetch all promo code from billing
    function fetchPromoCodeDetails(month,no_of_users){
        $.ajax({
            type: "POST",
            url: 'https://billing.getleadcrm.com/api/list-promo-codes',
            data: {
                months:month,
                min_users:no_of_users,
            },
            dataType: "json",
            success: function (response) {
                var html = '';
                $('.coupens_lists').html(html)
                $.each(response.data, function (indexInArray, value) {
                    promo = calculatePromoCode(value.discount_mode,value.discount_value); 
                    if(value.discount_mode == 1)
                        promotext = value.discount_value+'% off on this purchase';
                    else
                        promotext = 'Save ₹'+promo+' on this purchase';

                    not_applicable = (value.applicable == 0)? 'not-applicable' : '';
                    html += '<div class="coupon-box active '+not_applicable+'">\
                                <div class="title-btn">\
                                    <p>'+value.promocode+'</p>\
                                    <a href="#" onclick=applyPromoCode('+promo+',1,"'+value.promocode+'","'+value.applicable+'"); data-coupen='+value.promocode+' data-value='+promo+'>APPLY </a>\
                                </div>\
                                <p class="coupon-title">'+promotext+'</p>\
                                <p class="coupon-cont">This promo code is valid for a limited time only, so don"t miss out! To redeem your discount, simply use this promo code at checkout</p>\
                            </div>';
                });
                $('.coupens_lists').html(html)
            }
        });
    }

// Get promocode details from promode
    function getPromoCodeDetails(code){
        if(code)
        $.ajax({
            type: "POST",
            url: 'https://billing.getleadcrm.com/api/get-promo-details',
            data: {
                promo_code:code
            },
            dataType: "json",
            success: function (response) {
                if(response.data){
                    if((response.data.min_months <= $('.change_month').val()) && (response.data.min_users <= $('.change_users').val())){

                        if(parseInt($('#promo_discount').attr('data-value')) == 0){
                            promo = calculatePromoCode(response.data.discount_mode,response.data.discount_value);
                            $('.you_save').html('You saved ₹'+promo);
                            $('#promo_discount').html('₹'+promo);
                            $('#promo_discount').attr('data-value',promo);
                            $('#coupon_code').val(code);
                            toastr.success('Voucher code is applied!');
                        }
                    }else{
                        toastr.warning('Voucher is valid only a perticular subscription!');
                        return false;
                    }
                    
                }else{
                    $('.you_save').html('Invalid code!!');
                    $('#promo_discount').html('₹00');
                    $('#promo_discount').attr('data-value',0);
                    amountPerMonth = checkPrice($('.change_month').val())
                    total = calculateAmount($('.change_month').val(),$('.change_users').val(),amountPerMonth);
                    $('.payable_amount').html('₹'+(total));
                    $('#coupon_code').val(code);
                    toastr.warning('Voucher code is Invalid!');
                }
            
            }
        });
    }

// Apply promo code function
    function applyPromoCode(promo,flag,promocode,applicable){
        if(applicable == 0){
            toastr.warning('Voucher is valid only a perticular subscription!');
            return false;
        }
        if(flag == 1){
            $('.you_save').html('You saved ₹'+promo);
            $('#promo_discount').html('₹'+promo);
            $('#promo_discount').attr('data-value',promo);
            amountPerMonth = checkPrice($('.change_month').val())
            total = calculateAmount($('.change_month').val(),$('.change_users').val(),amountPerMonth);
            // $('.payable_amount').html('₹'+(total));
            $('#coupon_code').val(promocode);
            $('#coupons-modal').modal('toggle')
            toastr.success('Voucher code is applied!');
        }else{
            getPromoCodeDetails(promocode);
        }
    }

// Payment successfull 
    $(document).on('click','#rzp-button1' , function(e){
        if($(this).data('id') == '0') // For free plan zero amount
        {
            return false;
        }

        var amount =$('.payable_amount').attr('data-value')
        var options = {
        "key": key, // Enter the Key ID generated from the Dashboard
        "amount": amount * 100, // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
        "currency": "INR",
        "name": "getlead.co.uk", //your business name
        "description": 'Getlead CRM plan subscription payment',
        "image": "https://app.getlead.co.uk/backend/images/favicon/android-icon-96x96.png",
        "callback_url": ret_url,
        "prefill": {
            "name": name, //your customer's name
            "email": email,
            "contact": mobile
        },
        "notes": {
            "address": "Govt. CyberPark, Unit-2, Upper Basement,Sahya Building, Kozhikode",
            "no_users": $('.user_count').html(),
            "no_of_months": $('.month_count').html(),
            "code_used":  $('#coupon_code').val(),
            "discount" :$('#promo_discount').attr('data-value'),
            "cust_id" : cid,
            "page" : page,
            "page_redirect" : page_redirect
        },
        "theme": {
            "color": "#F37254"
        }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
        e.preventDefault();
    });

// Apply promocode through manual text
    $(document).on('click','.apply-promo',function(e){
        month = $('.change_month').val();
        promo_code = $('#coupon_code').val();
        flag = 2;

        applyPromoCode(null,flag,promo_code,2);
    })

// Apply free plan
    $(document).on('click','.free-plan',function(e){
        $.ajax({
            type: "POST",
            url: base_url+'/setup/apply-free-plan',
            dataType: "json",
            success: function (response) {
                if(response.status == 1){
                    location.href = base_url + '/setup/step-one-add-member';
                    toastr.success('Welcome to getlead crm!');
                }
            }
        });
    });

    function loadCurrentPlan(){
        amountPerMonth = checkPrice($('.month_count').html())
        total = calculateAmount($('.user_count').html(),$('.month_count').html(),amountPerMonth);
        $('#price_crm').html('₹'+total)
        var gst = ( total * 18 ) / 100;
        $('#crm_gst').html('₹'+gst)
        var final_amount = Math.ceil(gst + total);
        $('#final_amount').html('₹'+final_amount)
        $('.payable_amount').html('₹'+final_amount)
        $(".payable_amount").attr({
            "data-value": final_amount,
        });
    }
    function checkPrice(duration){
        if(duration < 3){
            currency_value = 899;
        }else if(duration >= 3 && duration < 6){
            currency_value = 799;
        }else if(duration >= 6 && duration < 11){
            currency_value = 699;
        }else{
            currency_value = 599;
        }
        return currency_value;
      }