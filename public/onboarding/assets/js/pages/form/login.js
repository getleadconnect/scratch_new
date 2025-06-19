    /* Event for login button submit */
    $('#kt_login_signin_submit').on('click', function (e) {
        var validation =  validateForm();
        if (!validation.valid()) {
            return;
        }
        if (validation) {
                // swal.fire({
                //     text: "Thanks for login",
                //     icon: "success",
                //     buttonsStyling: false,
                //     confirmButtonText: "Ok",
                //     customClass: {
                //         confirmButton: "btn font-weight-bold btn-light-primary"
                //     }
                // }).then(function() {
                //     form.submit();
                // });
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
        // $.validator.addMethod("pwcheck",
        //     function(value, element) {
        //         return /^[A-Za-z0-9\d=!\-@._*]+$/.test(value);
        // });
        form = $("#kt_login_signin_form");
        form.validate({
            rules: {
                email: {
                    required: true,
                    minlength: 6,
                    maxlength: 12,
                    number:true
                },
                password: {
                    required: true,
                    // minlength:5,
                    // pwcheck:true,
                },
            },
            messages: {
                email:{
                    required : "Enter mobile number",
                    number:"Please enter mobile number"
                },
                password: {
                    required:"Please enter your password",
                }
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

    $(function () {
        $("#toggle_pwd").click(function () {
            $(this).toggleClass("fa-eye fa-eye-slash");
        var type = $(this).hasClass("fa-eye") ? "text" : "password";
            $("#password").attr("type", type);
        });
    });

    $(document).on('keydown','#kt_login_signin_form',function (e) {
        if (e.keyCode == 13) {
            $('#kt_login_signin_submit').click();
        }
    });

    