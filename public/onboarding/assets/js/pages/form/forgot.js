    /* Event for login button submit */
    $('#kt_forgot_signin_submit').on('click', function (e) {
        var validation =  validateForm();
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
    
    /* Function for validate form */
    function validateForm() { 
        form = $("#kt_forgot_signin_form");
        form.validate({
            rules: {
                number: {
                    required: true
                },
            },
            messages: {
                otp:{
                    required : "Enter Valid Number",
                },
            },
            errorPlacement: function(label, element) {
                label.addClass("arrow")
                label.insertAfter(element);
            },
        });
        return form;
    }


    /* CHANGE PASSWORD FORM SUBMISSION */

     /* Event for login button submit */
     $('#kt_password_change_submit').on('click', function (e) {
        var validations =  validateForm2();
        if (!validations.valid()) {
            return;
        }
        if (validations) {
                forms.submit();
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
    function validateForm2() { 
        forms = $("#kt_password_change_form");
        forms.validate({
            rules: {
                password:{
                    minlength: 8,
                    maxlength: 20,
                    required: true,
                },
                password_confirmation:{
                    equalTo: "#password_reg",
                }
            },
            messages: {
                password:{
                    required : "Enter valid password",
                },
            },
            errorPlacement: function(label, element) {
                label.addClass("arrow")
                label.insertAfter(element);
            },
        });
        return forms;
    }