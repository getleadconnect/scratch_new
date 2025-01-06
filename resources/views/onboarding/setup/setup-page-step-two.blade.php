@extends('onboarding.setup.layouts.master')
@push('css')

@endpush
@section('content')

<div class="team-dash-sec">
    @include('onboarding.setup.layouts.header')
     
    <div class="add-y-team">
        <div class="y-team-header">
            <h3>Add leads Settings</h3>
            <p>To add leads Settings in your account.</p>
            <div class="lead-options">
                <p><span>Lead purpose -</span> The reason why this potential customer has connected with you.</p>
                <p><span>Lead status -</span> The present status of the lead in your sales pipeline.</p>
                <p><span>Lead source -</span> The source from where you received this lead.</p>
                {{-- <p><span>Additional fields -</span> Any other additional field of your choice can be added here. </p> --}}
            </div>
        </div>
        <div class="add-leads">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="sel-types">
                        <h5>Lead purpose</h5>
                         <ul class="lead-listing lead_purpose_1">
                           @if($purpose)
                              @foreach ($purpose as $item)
                                 <li class="edit-label-list">
                                    <label id="edit-label" contenteditable="true">{{$item->vchr_purpose}}</label>
                                    <button id="label-edit-btn" class="todo-edit"><img src="{{url('onboarding/setup/images/delete-icon.svg')}}" alt="getlead"></button>
                                 </li>
                              @endforeach
                           @else
                              <li class="edit-label-list">
                                 <label id="edit-label" contenteditable="true">CRM</label>
                                 <button id="label-edit-btn" class="todo-edit">{{-- <img src="{{url('onboarding/setup/images/delete-icon.svg')}}" alt="getlead"> --}}</button>
                              </li>
                           @endif
                         </ul>
                         <ul class="lead-listing-2">
                            <li>
                               <button class="todo-add-btn" data-button="col-1">Add purpose <img src="{{url('onboarding/setup/images/Add-icon.svg')}}" alt="getlead"></button>
                            </li>
                         </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                   <div class="sel-types">
                       <h5>Lead status</h5>
                        <ul class="lead-listing lead_status">
                           @if($feedback)
                              @foreach ($feedback as $item)
                                 <li class="edit-label-list">
                                    <label id="edit-label" contenteditable="true">{{$item->vchr_status}}</label>
                                    <button id="label-edit-btn" class="todo-edit"><img src="{{url('onboarding/setup/images/delete-icon.svg')}}" alt="getlead"></button>
                                 </li>
                              @endforeach
                           @else
                              <li class="edit-label-list">
                                 <label id="edit-label" contenteditable="true">New</label>
                                 <button id="label-edit-btn" class="todo-edit">{{-- <img src="{{url('onboarding/setup/images/delete-icon.svg')}}" alt="getlead"> --}}</button>
                              </li>
                           @endif
                        </ul>
                        <ul class="lead-listing-2">
                           <li>
                              <button class="todo-add-btn" data-button="col-2">Add status <img src="{{url('onboarding/setup/images/Add-icon.svg')}}" alt="getlead"></button>
                           </li>
                        </ul>
                   </div>
               </div>
               <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="sel-types">
                    <h5>Lead Source</h5>
                     <ul class="lead-listing lead_source_2">
                        @if($newEnquiryType)
                           @foreach ($newEnquiryType as $item)
                              <li class="edit-label-list">
                                 <label id="edit-label" contenteditable="true">{{$item->vchr_enquiry_type}}</label>
                                 <button id="label-edit-btn" class="todo-edit"><img src="{{url('onboarding/setup/images/delete-icon.svg')}}" alt="getlead"></button>
                              </li>
                           @endforeach
                        @else
                           <li class="edit-label-list">
                              <label id="edit-label" contenteditable="true">Facebook</label>
                              <button id="label-edit-btn" class="todo-edit">{{-- <img src="{{url('onboarding/setup/images/delete-icon.svg')}}" alt="getlead"> --}}</button>
                           </li>
                        @endif
                     </ul>
                     <ul class="lead-listing-2">
                        <li>
                           <button class="todo-add-btn" data-button="col-3">Add status <img src="{{url('onboarding/setup/images/Add-icon.svg')}}" alt="getlead"></button>
                        </li>
                     </ul>
                </div>
            </div>
            {{-- <div class="col-lg-3 col-md-6 col-sm-12">
               <div class="sel-types">
                  <h5>Additional fields</h5>
                     <ul class="lead-listing lead-listing-v2 add_fields">
                        @if($enqfields)
                              @foreach ($enqfields as $key =>  $item)
                                 <li class="edit-label-list" data-id="{{$key+1}}">
                                    <div class="filter-div" style="width: 100%;">
                                       <div class="filter flt">
                                          <label>Shown in filter ?</label>
                                          <input type="checkbox" name="filter" @if($item->show_in_filter) checked @endif>
                                       </div>
                                       <div class="filter lst">
                                          <label>Shown in list ? </label>
                                          <input type="checkbox" name="list" @if($item->show_in_list) checked @endif>
                                       </div>
                                       <div class="filter req">
                                          <label>Required ? </label>
                                          <input type="checkbox" name="required" @if($item->is_required) checked @endif>
                                       </div>
                                    </div>
                                 <div class="addi-field">
                                    <label id="edit-label" class="label-1" contenteditable="true" data-id="{{$key+1}}">{{$item->field_name}}</label>
                                    <button id="label-edit-btn" class="todo-edit"><img src="{{url('onboarding/setup/images/delete-icon.svg')}}" alt="getlead"></button>
                                 </div>
                                 <div class="fields-type">
                                    <select class="addi-field-sel" id="additional_field_type-1">
                                       <option value="1" @if($item->input_type == "1") selected @endif>Textfield</option>
                                       <option value="2" @if($item->input_type == "2") selected @endif>Dropdown</option>
                                       <option value="3" @if($item->input_type == "3") selected @endif>Date</option>
                                       <option value="4" @if($item->input_type == "4") selected @endif>Time</option>
                                       <option value="5" @if($item->input_type == "5") selected @endif>Date Time</option>
                                       <option value="6" @if($item->input_type == "6") selected @endif>Image</option>
                                       <option value="7" @if($item->input_type == "7") selected @endif>Number</option>
                                       <option value="8" @if($item->input_type == "8") selected @endif>Multi Select Dropdown</option>
                                    </select>
                                 </div>
                                 </li>
                              @endforeach
                        @else
                           <li class="edit-label-list" data-id="1">
                              <div class="filter-div" style="width: 100%;">
                                 <div class="filter flt">
                                    <label>Shown in filter ?</label>
                                    <input type="checkbox" name="filter">
                                 </div>
                                 <div class="filter lst">
                                    <label>Shown in list ? </label>
                                    <input type="checkbox" name="list">
                                 </div>
                                 <div class="filter req">
                                    <label>Required ? </label>
                                    <input type="checkbox" name="required">
                                 </div>
                              </div>
                           <div class="addi-field">
                              <label id="edit-label" class="label-1" contenteditable="true" data-id="1">New</label>
                              <button id="label-edit-btn" class="todo-edit"><img src="{{url('onboarding/setup/images/delete-icon.svg')}}" alt="getlead"></button>
                           </div>
                           <div class="fields-type">
                              <select class="addi-field-sel" id="additional_field_type-1">
                                 <option value="1">Textfield</option>
                                 <option value="2">Dropdown</option>
                                 <option value="3">Date</option>
                                 <option value="4">Time</option>
                                 <option value="5">Date Time</option>
                                 <option value="6">Image</option>
                                 <option value="7">Number</option>
                                 <option value="8">Multi Select Dropdown</option>
                              </select>
                           </div>
                           </li>
                        @endif
                        
                     </ul>
                     <ul class="lead-listing-2">
                     <div class="addi-field">
                        <li>
                           <button class="todo-add-btn add-list-btn-v2" data-button="col-4">Add field <img src="{{url('onboarding/setup/images/Add-icon.svg')}}" alt="getlead"></button>
                        </li>
                     </div>
                     </ul>
               </div>
            </div> --}}
            </div>
        </div>
        
    </div>
    
    <div class="bottom-btns bottom-btns-v2">
      <div class="back-btn">
         <a href="{{ route('setup-add-member.list')}}">Back</a>
     </div>
      <div class="bottom-btns bottom-btns-v2" style="padding-top:0;">
       <div class="back-btn">
         <a href="{{ route('setup-get-mobile-app.list')}}">Skip</a>
       </div>
       <div class="next-btn">
          <a href="javascript:void(0)" class="next-button-step-two">
             Next
          </a>
       </div>
    </div>
    </div>

    </div>
    {{-- <div class="modal invite-modal fade show" id="select-modal" tabindex="-1"  data-toggle="modal" role="dialog" aria-labelledby="myModalLabel-task" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-body">
               <div class="add-leads invitation-col">
                  <h5>Add new dropdown list</h5>
                  <p>To get your dropdown additional field in<br> 
                     Getlead CRM add them</p>
                     <input type="hidden" name="drop_id" class="drop_id">
                     <div class="sel-types">
                        <ul class="lead-listing lead_dropdown">
                           <li class="edit-label-list">
                              <label id="edit-label" contenteditable="true">List</label>
                              <button id="label-edit-btn" class="todo-edit"><img src="{{url('onboarding/setup/images/delete-icon.svg')}}" alt="getlead"></button>
                           </li>
                        </ul>
                        <ul class="lead-listing-2">
                           <li>
                              <button class="todo-add-btn" data-button="col-5">Add Dropdown<img src="{{url('onboarding/setup/images/Add-icon.svg')}}" alt="getlead"></button>
                           </li>
                           <li>
                              <button class="setup-btn">Create</button>
                           </li>
                        </ul>
                     </div>
               </div>
            </div>
       </div>
      </div>
   </div> --}}

@push('script')
<script>
   var url = @JSON(route('add.lead.settings'));
   var url_step3 = @JSON(route('setup-get-mobile-app.list'));
</script>
    
    <script src="{{url('onboarding/setup/script/script.js')}}"></script>
@endpush
@endsection