@if ( request()->segment(2) == "pricing" ||  request()->segment(2) == "renewal-subscription")
    <div class="team-sidebar">
        <div class="single-col">
            <a href="{{url('/')}}" class="home-nav">
                <img src="{{url('onboarding/setup/images/home-icon.svg')}}" alt="getlead">
            </a>
        </div>
        <div class="setup-col ">
            <div class="setup-logo">
                <a href="{{url('/')}}">
                    <img class="web-logo" src="{{url('onboarding/setup/images/logo-new.svg')}}" alt="Getlead">
                    <img class="mob-logo" src="{{url('onboarding/setup/images/Getlead-2.svg')}}" alt="getlead">
                </a>
                {!!$sideBar_label!!}
            </div>
            <div class="setup-steps @if (request()->segment(2) == 'pricing' ||  request()->segment(2) == "renewal-subscription") billing-col @endif">
                <div class="setup-single">
                    <p class="s-number">01</p>
                    <div class="bill-texts">
                        <h4>Billing</h4>
                        <p>Choose your plan and manage billing details</p>
                    </div>
                </div>
            </div>
            <div class="bottom-download setup-steps">
                <p>Download GetLead App</p>
                <ul class="playstore-links">
                    <li>
                        <a href="https://play.google.com/store/apps/details?id=com.getlead.app">
                        <img src="{{url('onboarding/setup/images/playstore.svg')}}" alt="getlead">
                        </a>
                    </li>
                    <li>
                        <a href="https://apps.apple.com/in/app/getlead/id1557445421">
                        <img src="{{url('onboarding/setup/images/appstore.svg')}}" alt="getlead">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@else
    <div class="team-sidebar">
        <div class="single-col">
            <a href="{{url('/')}}" class="home-nav">
            <img src="{{url('onboarding/setup/images/home-icon.svg')}}" alt="getlead">
            </a>
        </div>
        <div class="setup-col">
            <div class="setup-logo">
                <a href="{{url('/')}}">
                    <img class="web-logo" src="{{url('onboarding/setup/images/logo-new.svg')}}" alt="Getlead">
                    <img class="mob-logo" src="{{url('onboarding/setup/images/Getlead-2.svg')}}" alt="getlead">
                </a>
                {!!$sideBar_label!!}
            </div>
            <div class="setup-steps billing-col">
                <div class="setup-single">
                <p class="s-number">01</p>
                <div class="bill-texts">
                    <h4>Setup your team</h4>
                    <p>Let’s build your workflow in few steps</p>
                </div>
                </div>
            </div>
            <div class="setup-steps @if (request()->segment(2) == 'step-two-add-lead' || request()->segment(2) == 'step-three-get-mobile-app')
                billing-col
            @endif">
                <div class="setup-single">
                <p class="s-number">02</p>
                <div class="bill-texts">
                    <h4>Lead Settings</h4>
                    <p>Adding leads is not 
                        difficult at all
                    </p>
                    </div>
                </div>
            </div>
            <div class="setup-steps @if (request()->segment(2) == 'step-three-get-mobile-app')
                billing-col
            @endif">
                <div class="setup-single">
                <p class="s-number">02</p>
                <div class="bill-texts">
                    <h4>Finish setup</h4>
                    <p>Finish building your workflow in just a few steps</p>
                </div>
                </div>
            </div>
            <div class="bottom-download setup-steps">
                <p>Download GetLead App</p>
                <ul class="playstore-links">
                <li>
                    <a href="https://play.google.com/store/apps/details?id=com.getlead.app">
                    <img src="{{url('onboarding/setup/images/playstore.svg')}}" alt="getlead">
                    </a>
                </li>
                <li>
                    <a href="https://apps.apple.com/in/app/getlead/id1557445421">
                    <img src="{{url('onboarding/setup/images/appstore.svg')}}" alt="getlead">
                    </a>
                </li>
                </ul>
            </div>
        </div>
    </div>
@endif