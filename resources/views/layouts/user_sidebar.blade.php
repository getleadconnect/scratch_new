 <!--start sidebar -->
        <aside class="sidebar-wrapper" data-simplebar="true">
          <div class="sidebar-header">
            <div>
             <img src="{{asset('assets/images/logos/gl-logo.svg')}}" class="logo-icon" alt="logo icon">
            </div>
            <div>
              <h4 class="logo-text"style="color:#403737;font-size:20px;">GETLEAD</h4>
            </div>
            <div class="toggle-icon ms-auto">
			<i class="bi bi-list"></i>
            </div>
          </div>
          <!--navigation-->
          <ul class="metismenu" id="menu">
		  		  
		  @if(Auth::user()->int_role_id==1 and Auth::user()->admin_status==1)
			<li>
              <a href="{{url('users/admin-dashboard')}}" title="Dashboard">
                <div class="parent-icon"><img src="{{asset('assets/images/icons/people-roof.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">Dashboard</div>
              </a>
            </li>
			@else
			<li>
              <a href="{{url('users/dashboard')}}" title="Dashboard">
                <div class="parent-icon"><img src="{{asset('assets/images/icons/people-roof.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">Dashboard</div>
              </a>
            </li>
		  @endif
				
			<li>
              <a href="{{url('users/campaigns')}}" title="Campaigns">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/megaphone.png')}}" style="width:20px;">
				
                </div>
                <div class="menu-title">Campaigns</div>
              </a>
            </li>

			<li>
              <a href="{{url('users/scratch-web-customers')}}" title="Campaigns">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/users-alt.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">Customers List</div>
              </a>
            </li>
			
			<li>
              <a href="{{url('users/redeem-scratch')}}" title="Campaigns">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/win.png')}}" style="width:18px;">
				
                </div>
                <div class="menu-title">Redeem</div>
              </a>
            </li>
			
			 @if(Auth::user()->int_role_id!=1)
				<li>
				  <a href="{{url('users/redeemed-customers')}}" title="Campaigns">
					<div class="parent-icon">
					<img src="{{asset('assets/images/icons/list.png')}}" style="width:18px;">
					
					</div>
					<div class="menu-title">Redeemed Customers</div>
				  </a>
				</li>
			@endif
			
			@if(Auth::user()->parent_user_id=="" and Auth::user()->int_role_id!=1)		
			<li>
              <a href="{{url('users/gl-links')}}" title="Campaigns">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/link.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">Web Scratch Links </div>
              </a>
            </li>
      @endif
			<li>
              <a href="{{url('users/gifts-list')}}" title="Campaigns">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/trophy-star.png')}}" style="width:18px;">
                </div>
                <div class="menu-title">Gifts List</div>
              </a>
            </li>
			

			<li>
              <a href="{{url('users/slide-images')}}" title="App Slide Images">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/gallery1.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">App Slide Images</div>
              </a>
            </li> 
						
			
			<li>
              <a href="{{url('users/scratch-ads-image')}}" title="Campaigns">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/gallery.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">Ads Images</div>
              </a>
            </li> 
			@if(Auth::user()->int_role_id==1 and Auth::user()->admin_status==1)

				
			<li>
              <a href="javascript:;" class="has-arrow" title="Options">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/reports.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">Reports</div>
              </a>
              <ul>
			   <!--<li> <a href="{{url('users/scratch-bills')}}"><i class="fa fa-caret-right"></i>Bills</a> </li>-->
				<li> <a href="{{url('users/reports')}}"><i class="fa fa-caret-right"></i>Analytics Report</a>
                </li>
                <li> <a href="{{url('users/branch-reports')}}"><i class="fa fa-caret-right"></i>Branch wise report</a>
                </li>
				
                
              </ul>
            </li>
	
			@endif
            <li>
              <a href="javascript:;" class="has-arrow" title="Options">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/admin-alt.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">Settings</div>
              </a>
              <ul>
			   <!--<li> <a href="{{url('users/scratch-bills')}}"><i class="fa fa-caret-right"></i>Bills</a> </li>-->
				<li> <a href="{{url('users/scratch-branches')}}"><i class="fa fa-caret-right"></i>Branches</a>
                </li>
                <li> <a href="{{url('users/user-profile')}}"><i class="fa fa-caret-right"></i>My Profile</a>
                </li>
				 <!-- <li> <a href="{{url('users/staff-users')}}"><i class="fa fa-caret-right"></i>Staff Users</a>
                </li> -->
				@if(Auth::user()->int_role_id==1 and Auth::user()->admin_status==1)
				<li> <a href="{{url('users/branch-users')}}"><i class="fa fa-caret-right"></i>Branch Users</a>
                </li>
				@endif
				<li> <a href="{{url('users/general-settings')}}"><i class="fa fa-caret-right"></i>General Settings</a>
                </li>
                
              </ul>
            </li>

          </ul>
          <!--end navigation-->
       </aside>
       <!--end sidebar -->
