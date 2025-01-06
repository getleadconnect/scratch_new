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
		  
			<li>
              <a href="{{url('users/dashboard')}}" title="Dashboard">
                <div class="parent-icon"><img src="{{asset('assets/images/icons/people-roof.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">Dashboard</div>
              </a>
            </li>
			
			
			<li>
              <a href="{{url('users/redeem-history')}}" title="Campaigns">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/users-alt.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">App Customers History</div>
              </a>
            </li> 
			
			
			<li>
              <a href="{{url('users/scratch-web-customers')}}" title="Campaigns">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/users-alt.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">Web Customers History</div>
              </a>
            </li>

          </ul>
          <!--end navigation-->
       </aside>
       <!--end sidebar -->
