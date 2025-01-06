@extends('onboarding.setup.layouts.master')
@push('css')
<style>
    .phone-field{
        padding-left: 100px !important;    
    }
    .error{
        color: red;
        font-size: 13px;
    }
</style>
@endpush
@section('content')
    <div class="team-dash-sec">
    
        @include('onboarding.setup.layouts.header')
        
        <div class="add-y-team">
            <div class="y-team-header">
                <h3>Add your team</h3>
                <p>To add new members in your team, sent them an invite first.</p>
            </div>
            <div class="agent-boxes ">
                @foreach ($staffs as $item)
                    <div class="agent-single">
                        <div class="agent-img">
                            <img src="{{url("onboarding/setup/images/profile-icon.svg")}}" alt="getlead">
                        </div>
                        <div class="agent-details">
                        <p>{{$item->vchr_user_name}}</p>
                        <a class="agent-email" href="mailto:{{$item->email}}">{{$item->email}}</a>
                        <div class="agent-btn-col">
                            {{-- <a class="agent-btn" href="#">Agent</a> --}}
                        </div>
                        <p>
                            {{$item->vchr_user_mobile}}
                        </p>
                        </div>
                    </div>
                @endforeach
                <div class="agent-single agent-invite">
                    <p>Add<br> new team member</p>
                    <div class="invite-btn">
                        <a class="setup-btn popup-open-member" href="#" >
                        Add
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom-btns">
            <div class="back-btn">
                <a href="{{ route('setup-add-lead.list')}}">Skip</a>
            </div>
            <div class="next-btn">
                <a href="{{ route('setup-add-lead.list')}}">
                    Next
                </a>
            </div>
        </div>
    </div>

    <!----------------------------------- Invite Modal ------------------------------------------->

    <div class="modal invite-modal fade modal-md" id="invite-modal" tabindex="-1"  data-toggle="modal" role="dialog" aria-labelledby="myModalLabel-task" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-body" style="width: 500px">
               <div class="invitation-col">
                  <h5>Invite your team</h5>
                  <p>To get your team members in<br> 
                     Getlead CRM add them</p>
                     <form class="invite-form">
                        <div class="form-feild-row text-left position-relative">
                            <input type="text" name="name" placeholder="Name">
                            <span id="name-error" class="error arrow name" style="display: inline;"></span>
                        </div>
                        <div class="form-feild-row text-left position-relative">
                            <input type="email" name="email" placeholder="Email">
                            <span id="email-error" class="error arrow email" style="display: inline;"></span>
                        </div>
                        <div class="form-group form-feild-row  country-code-row text-left position-relative">
                            <input class="form-control form-control-solid h-auto phone-field text-input" type="text" placeholder="Mobile number" name="mobile" id="phoneField1" autocomplete="off" value="{{ old('email')}}"/>
                            <input type="hidden" id="country_code" value="{{ $countryCode }}">
                            <span id="mobile-error" class="error arrow mobile" style="display: inline;"></span>
                        </div>
                        <div class="form-feild-row text-left position-relative">
                            <input type="password" name="password" placeholder="Password" autocomplete="new-password">
                            <span id="password-error" class="error arrow password" style="display: inline;"></span>
                            <span id="limit-error" class="error arrow limit" style="display: inline;"></span>
                        </div>

                        <button class="setup-btn" id="add_member">Add member</button>
                     </form>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="modal invite-modal fade modal-md" id="alert-modal" tabindex="-1"  data-toggle="modal" role="dialog" aria-labelledby="myModalLabel-task" aria-hidden="true">
    <div class="modal-dialog" role="document">
       <div class="modal-content">
          <div class="modal-body" style="width: 500px">
             <div class="invitation-col">
                <h5>Agent Limit exceeded</h5>
                <span>Your maximum agent limit exceeded<br> 
                   You want to add more members please renew your subscription</span>
             </div>
          </div>
       </div>
    </div>
 </div>
@push('script')
<script>
    var member = @json($data);

    $(document).on('click','.popup-open-member',function (e) { 
        if(member.allowed_staff_count == 0 || member.allowed_staff_count >= member.staff_count)
            $('#invite-modal').modal('toggle');
        else  
            $('#alert-modal').modal('toggle') 
    });
/****************   Event for submit form   ************/
    $(document).on('click','#add_member',function (e) { 
        e.preventDefault();
        form = $('.invite-form');
        var validation =  validateForm(form);

        if (!validation.valid()) {
            return;
        }
        if (validation) {
            $("#add_member").html('Add agent...!!&nbsp;<img src="/onboarding/setup/images/loader.gif" alt="getlead" width="25">').attr('disabled','disabled')
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: "POST",
                url: '{{ route("setup-member.insert") }}',
                data: {
                    _token: CSRF_TOKEN,
                    data:form.serialize()
                },
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    $("#add_member").html('Add member')
                    $("#add_member").removeAttr("disabled");
                    if(response.status == false){
                        if(response.response == 'Your maximum agent limit exceeded!'){
                            $('#alert-modal').modal('toggle') 
                            return false;    
                        }
                        $.each(response.response, function (indexInArray, valueOfElement) { 
                            switch (indexInArray) {
                                case 'mobile':
                                    $('.mobile').html(valueOfElement[0]).css('display', 'block')
                                    break;
                                case 'name':
                                    $('.name').html(valueOfElement[0]).css('display', 'block')
                                    break;
                                case 'email':
                                    $('.email').html(valueOfElement[0]).css('display', 'block')
                                    break;
                                case 'limit':
                                    $('.limit').html(valueOfElement[0]).css('display', 'block')
                                    break;
                                default:
                                    toastr.warning(response.response);
                                    break;
                            }   
                        });
                        
                    }else{
                        var obj = '<div class="agent-single">\
                            <div class="agent-img">\
                                <img src="{{url("onboarding/setup/images/profile-icon.svg")}}" alt="getlead">\
                            </div>\
                            <div class="agent-details">\
                            <p>'+response.result.vchr_user_name+'</p>\
                            <a class="agent-email" href="mailto:'+response.result.email+'">'+response.result.email+'</a>\
                            <div class="agent-btn-col">\
                            </div>\
                            <p>'+response.result.vchr_user_mobile+'</p>\
                            </div>\
                        </div>';
                        $(obj).insertBefore(".agent-invite");
                        $('#invite-modal').modal('toggle')
                        toastr.success('New member added !');
                    }
                }
            });
        }
    });

/****************   Function for validate form   ************/
    function validateForm(form) { 
        form.validate({
            errorElement: 'span',
            rules: {
                name: {
                    required: true
                },
                mobile: {
                    required: true,
                    minlength:7,
                },
            },
            messages: {
                mobile:{
                    required : "Enter mobile number",
                },
                name: {
                    required:"Please enter your name",
                }
            },
            errorPlacement: function(label, element) {
                label.addClass("arrow")
                label.insertAfter(element);
            }
        });
        return form;
    }
    
</script>
@endpush
@endsection