<div class="team-header">
    <div class="welcome-text">
        @if(request()->segment(2) != 'renewal-subscription') 
            <h3>Welcome {{auth()->user()->vchr_user_name}}</h3>
            <p>Let’s build your workflow in few steps</p>
        @endif
    </div>
    
    <div class="plan-col">
        {!!$sideBar_label!!}
        <div class="premium-btn">
            @if(request()->segment(2) == 'step-one-add-member')
                <a href="{{route('renewal-subscription','agent-field')}}">Renew subscription</a>
            @elseif(request()->segment(2) != 'renewal-subscription')
                <a href="{{route('renewal-subscription')}}">Renew subscription</a>
            @else
                @if($count >= 0) <a href="{{url('/')}}">Home</a> @endif 
            @endif
        </div>
        <ul class="header-icons">
            <li>
                {{-- <a href="#">
                    <img src="{{url('onboarding/setup/images/qns-icon.svg')}}" alt="getlead">
                </a> --}}
            </li>
            {{-- <li>
                <a href="#">
                    <img style="width: 40px; height:40px;" src="{{url('onboarding/setup/images/profile-icon-2.svg')}}" alt="getlead">
                </a>
            </li> --}}
            <li>
                <div class="dropdown show">
                   <a class=" dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                       <img style="width: 40px; height:40px;" src="{{url('onboarding/setup/images/profile-icon-2.svg')}}" alt="getlead">
                   </a>
                   <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                     {{-- <a class="dropdown-item" href="{{ url('user/profile') }}">View Profile</a> --}}
                     {{-- <a class="dropdown-item" href="#">Change Password</a> --}}
                     <a class="dropdown-item" href="{{ route('auth.logout') }}">Logout</a>
                   </div>
                 </div>
             </li>
        </ul>
    </div>
</div>