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
              <a href="{{url('admin/dashboard')}}" title="Dashboard">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/people-roof.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">Dashboard</div>
              </a>
            </li>
			
			<li>
              <a href="{{url('admin/users-list')}}" title="Campaigns">
                <div class="parent-icon">
				<img src="{{asset('assets/images/icons/users-alt.png')}}" style="width:20px;">
                </div>
                <div class="menu-title">Users</div>
              </a>
            </li>
			
			<li>
              <a href="{{url('admin/scratch-gifts-list')}}" title="Campaigns">
                <div class="parent-icon"><i class="bi bi-house-fill"></i>
                </div>
                <div class="menu-title">Campaign Gift Lists</div>
              </a>
            </li>
			
			<!--<li>
              <a href="{{url('admin/deleted-gifts-list')}}" title="Campaigns">
                <div class="parent-icon"><i class="bi bi-house-fill"></i>
                </div>
                <div class="menu-title">Deleted Gift Lists</div>
              </a>
            </li> -->
			
			<li>
              <a href="#" title="Campaigns">
                <div class="parent-icon"><i class="bi bi-house-fill"></i>
                </div>
                <div class="menu-title">Option</div>
              </a>
            </li>


          </ul>
          <!--end navigation-->
       </aside>
       <!--end sidebar -->
