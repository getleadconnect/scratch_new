    /* Event for login button submit */
    $('#kt_verify_signin_submit').on('click', function (e) {
        var validation =  validateForm();
        if (!validation.valid()) {
            return;
        }
        if (validation) {
            $('#kt_verify_signin_submit').css('pointer-events', 'none').html("Verifying.. &nbsp;<img src='/onboarding/setup/images/loader.gif' alt='getlead' width='25'>")
            form.submit();
        } else {
            swal.fire({
                text: "Sorry, looks like there are some errors detected, please try again.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn font-weight-bold btn-light-primary"
                }
            }).then(function() {
                KTUtil.scrollTop();
            });
        }
    });
    
    /* Function for validate form */
    function validateForm() { 
        form = $("#kt_verify_signin_form");
        form.validate({
            rules: {
                otp: {
                    required: true
                },
            },
            messages: {
                otp:{
                    required : "Enter Valid otp",
                },
            },
            errorPlacement: function(label, element) {
                label.addClass("arrow")
                label.insertAfter(element);
            },
            // submitHandler: function(form) {
            //     form.submit();
            // }
        });
        return form;
    }



        /* Event for login button submit */
        $('#kt_verify_forgot_signin_submit').on('click', function (e) {
            var validation =  validateForm2();
            if (!validation.valid()) {
                return;
            }
            if (validation) {
                    form.submit();
            } else {
                swal.fire({
                    text: "Sorry, looks like there are some errors detected, please try again.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn font-weight-bold btn-light-primary"
                    }
                }).then(function() {
                    KTUtil.scrollTop();
                });
            }
        });

        function validateForm2() { 
            form = $("#kt_verify_forgot_signin_form");
            form.validate({
                rules: {
                    otp: {
                        required: true
                    },
                },
                messages: {
                    otp:{
                        required : "Enter Valid OTP",
                    },
                },
                errorPlacement: function(label, element) {
                    label.addClass("arrow")
                    label.insertAfter(element);
                },
                // submitHandler: function(form) {
                //     form.submit();
                // }
            });
            return form;
        }

    $(document).on('click','.resend-otp',function(){
        $('.mob').val($('.phone_number').val())
        forms= $("#formResend");
        forms.submit();
    })
    