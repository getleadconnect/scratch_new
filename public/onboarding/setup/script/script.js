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
    // $('.upgrade-plans').css({'display':'none'})
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
});


// $(document).on('click','.todo-edit',function(){
//   $(this).prev().css({'background-color':'rgb(221, 219, 219)'})
//   $(this).prev().prop('contenteditable',true)
// })



/************************* Add Lead Fields *************************/

$(document).on('click', '.todo-add-btn', function() {
    switch ($(this).data('button')) {
        case "col-1":
            var code = '<li class="edit-label-list"><label id="edit-label" contenteditable="true"></label><button id="label-edit-btn" class="todo-edit"><img src="'+asset_url+'/images/delete-icon.svg" alt="getlead"></button></li>'
            $(".lead_purpose_1").append(code)
            break;

        case "col-2":
            var code = '<li class="edit-label-list"><label id="edit-label" contenteditable="true"></label><button id="label-edit-btn" class="todo-edit"><img src="'+asset_url+'/images/delete-icon.svg" alt="getlead"></button></li>'
            $(".lead_status").append(code)
            break;

        case "col-3":
            var code = '<li class="edit-label-list"><label id="edit-label" contenteditable="true"></label><button id="label-edit-btn" class="todo-edit"><img src="'+asset_url+'/images/delete-icon.svg" alt="getlead"></button></li>'
            $(".lead_source_2").append(code)
            break;

        case "col-4":
            var id= $(".add_fields li:last-child").data('id') + 1;
            var code= '<li class="edit-label-list" data-id="'+id+'">\
            <div class="filter-div" style="width: 100%;">\
                <div class="filter flt">\
                    <label>Shown in filter ? </label>\
                    <input type="checkbox" name="filter">\
                </div>\
                <div class="filter lst">\
                    <label>Shown in list ? </label>\
                    <input type="checkbox" name="list">\
                </div>\
                <div class="filter req">\
                    <label>Required ? </label>\
                    <input type="checkbox" name="required">\
                </div>\
            </div>\
            <div class="addi-field"><label class="label-'+id+'" id="edit-label" data-id="'+id+'" contenteditable="true">New</label><button id="label-edit-btn" class="todo-edit"><img src="'+asset_url+'/images/delete-icon.svg" alt="getlead"></button></div>\
            <div class="fields-type">\
            <select class="addi-field-sel" id="additional_field_type-'+id+'">\
                <option value="1">Textfield</option>\
                <option value="2">Dropdown</option>\
                <option value="3">Date</option>\
                <option value="4">Time</option>\
                <option value="5">Date Time</option>\
                <option value="6">Image</option>\
                <option value="7">Number</option>\
                <option value="8">Multi Select Dropdown</option>\
            </select></div></li>'
            $(".add_fields").append(code);
            
            $('.addi-field-sel').change(function() {
                var opval = $(this).val();
                if(opval=="2" || opval=="8"){
                    $('#select-modal').modal("show");
                }
            });

            // var field_type = $("#additional_field_type").val()
            //     switch (field_type) {
            //       case "dropdown":
            //         var code= '<li class="edit-label-list" contenteditable="true"><div class="addi-field"><label id="edit-label">New</label><button id="label-edit-btn" class="todo-edit"><img src="./images/delete-icon.svg" alt="getlead"></button></div><div class="fields-type"><select class="addi-field-sel" id="additional_field_type"><option value="text">Textfield</option><option value="dropdown-modal">Dropdown</option><option value="time">Time</option></select></div></li>'
            //           $(".add_fields").append(code);
                      
            //         break;

            //       case "text":
            //         var code= '<li class="edit-label-list" contenteditable="true"><div class="addi-field"><input type="text" id="edit-label" value="Text-field"><button id="label-edit-btn" class="todo-edit"><img src="./images/delete-icon.svg" alt="getlead"></button></div><div class="fields-type"><select class="addi-field-sel" id="additional_field_type"><option value="text">Textfield</option><option value="dropdown-modal">Dropdown</option><option value="time">Time</option></select></div></li>'
            //           $(".add_fields").append(code);
            //           break;
            //       case "time":
            //         var code= '<li class="edit-label-list" contenteditable="true"><div class="addi-field"><label id="edit-label">New</label><button id="label-edit-btn" class="todo-edit"><img src="./images/delete-icon.svg" alt="getlead"></button></div><div class="fields-type"><select class="addi-field-sel" id="additional_field_type"><option value="text">Textfield</option><option value="dropdown-modal">Dropdown</option><option value="time">Time</option></select></div></li>'
            //           $(".add_fields").append(code);
            //         break;

            //       default:
            //         break;
            //     }


            break;
        case "col-5":
            var code = '<li class="edit-label-list"><label id="edit-label" contenteditable="true"></label><button id="label-edit-btn" class="todo-edit"><img src="'+asset_url+'/images/delete-icon.svg" alt="getlead"></button></li>'
            $(".lead_dropdown").append(code)
        break;

        default:
            break;
    }
});

/****************   Add Dropdown modal   ************/

$(document).on('change','.addi-field-sel',function() {
    var opval = $(this).val();
    if(opval=="2" || opval=="8"){
        $('.drop_id').val($(this).parent('div').parent('li').data('id'))
        $('#select-modal').modal("show");
    }
});


// var editorBtn = document.getElementById('label-edit-btn');
// var element = document.getElementById('edit-label');

// // var size = document.getElementsByClassName('edit-label-list');

//   editorBtn.addEventListener('click', function(e) {
//     e.preventDefault();

//     // for(var i=0; i<size.length; i++){

//       if (element.isContentEditable) {
//         // Disable Editing
//         element.contentEditable = 'false';
//         element.style.backgroundColor = "transparent";  
//         // You could save any changes here.
//       } else {
//         element.contentEditable = 'true';
//       }

//     // }


//   });

$(document).on('change','.renew-single input:radio',function(){
    if(this.value == "upgrade") {
         $('.upgrade-plans').css({'display':'flex'})
    }
    else
    {    $('.upgrade-plans').css({'display':'none'})
        
    }
});

/****************   Delete list item   ************/
$(document).on('click','.todo-edit',function(){
   $(this).parents('li').remove()
});

/****************   submit form   ************/
var lead_purpose = [];
var lead_status = [];
var lead_source = [];
var additional_field = [];
var additional_dropdown = [];
var data_settings = [];
$(document).on('click','.next-button-step-two',function(){

    $('.lead_purpose_1 li label').each(function(i) {
        $(this).text() != ''? lead_purpose.push($(this).text()) : '';  
    });
    $('.lead_status li label').each(function(i) {
        $(this).text() != ''? lead_status.push($(this).text()) : ''; 
    });
    $('.lead_source_2 li label').each(function(i) {
        $(this).text() != ''? lead_source.push($(this).text()) : '';  
    });

    $('.add_fields li').each(function(i) {
        field_id = value = '';
        field_id = $(this).children('.fields-type').children('select').val();
        value = $(this).children('.addi-field').children('label').text();
        checkbox = $(this).children('.filter-div');
        flt = checkbox.children('.flt').children('input').prop('checked')
        lst = checkbox.children('.lst').children('input').prop('checked')
        req = checkbox.children('.req').children('input').prop('checked')
        additional_field.push({
            'id':field_id,
            'name':value,
            'data-id':$(this).data('id'),
            'required':req,
            'filter':flt,
            'list':lst
        });  
    });

    data_settings = [];
    data_settings.push({'lead_source' : lead_source});
    data_settings.push({'lead_status' : lead_status});
    data_settings.push({'lead_purpose' : lead_purpose});
    data_settings.push({'additional_field' : additional_field});
    data_settings.push({'additional_dropdown' : additional_dropdown});
    
    saveLeadSettigns(data_settings) 
    console.log(data_settings);
 });

 $(document).on('click','.setup-btn',function(){
    value = '';
    arrayItem = [];
    $('.lead_dropdown li').each(function(i) {
        arrayItem.push($(this).children('label').text());  
    });
    additional_dropdown.push({'id':$('.drop_id').val(),'item':arrayItem}); 

    $('.lead_dropdown li').remove();
    $('.drop_id').val('');
    $('#select-modal').modal("hide");
})

 function saveLeadSettigns(data_settings) {
    $(".next-button-step-two").html('Saving data &nbsp;<img src="/onboarding/setup/images/loader.gif" alt="getlead" width="25">').attr('disabled','disabled')
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: "POST",
        url: url,
        data: {
            _token: CSRF_TOKEN,
            data_settings
        },
        dataType: "json",
        success: function (response) {
            $(".next-button-step-two").html('Next');
            if(response.status){
                toastr.success('Lead settings save successfully!');
                location.href = url_step3;
            }
        }
    });
    
 }

 $(document).on('click','.send-sms-to-link',function(){
    value = '';
    arrayItem = [];
    $(".send-sms-to-link").html("Let's start &nbsp;<img src='/onboarding/setup/images/loader.gif' alt='getlead' width='25'>")
    location.href = redirect_url;
})


$(document).on('click','.become-paid',function(){
    location.href = becom_paid;
})

