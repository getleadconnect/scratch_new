<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Stevebauman\Location\Facades\Location;

use guard;
use Illuminate\Http\Request;

use App\Common\Variables;
use App\Common\Common;

use Session;
use App\Models\User;
use Hash;
use Auth;
use Lang;
use Artisan;
use Flash;
use Carbon\Carbon;

use Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        $ip = Common::getIp();
        //$ip="144.25.10.2";
        if($ip){
            $data = \Location::get($ip);
            if ($data && $data->countryCode) {
                $countryCode = $data->countryCode;
            } else {
                $countryCode = "IN";
            } 
        }else{
        $countryCode = "IN";
      }      
    return view('onboarding.login', compact('countryCode'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function userLogin(Request $request)
    {

		$validate = Validator::make(request()->all(),[
			'email' => 'required', 
            'password' => 'required',
        ]);
		
		if($validate->fails())
		{
			Session::flash('error','Invalid credentials. Try again');
			return back()->withErrors(['msg'=>"Invalid credenstials"]);
		}
		else
		{
		
			$credentials = $request->only('email', 'password');
			$country_code = $request->only('email_phoneCode');
			
			$user = User::where('int_status', Variables::ACTIVE)
				->where(function ($query) use ($credentials, $country_code) {
					$query->where('vchr_user_name', $credentials['email'])
						->orWhere('vchr_user_mobile', $country_code['email_phoneCode'] . $credentials['email'])
						->orWhere('email', $credentials['email']);
				})
				->first();

				
			if ($user && Hash::check($credentials['password'], $user->password)) 
			{
				Auth::login($user);

				$user->datetime_last_login = Carbon::now();
				$rs=$user->save();

				/*login history*/
				/*$ip = Common::getIp();
				$ip="192.168.1.9";
				$data = Location::get($ip);
		
				dd($data);

				$d = json_encode($data);
				$loghistory = new LoginHistory();
				$loghistory->user_id = $user->id;
				$loghistory->ip_address = $ip;
				$loghistory->timestamp_login_time = Carbon::now();
				if ($data) {
					$loghistory->address = $data->cityName;
					$loghistory->regionname = $data->regionName;
					$loghistory->cityname = $data->cityName;
					$loghistory->countryname = $data->countryName;
					$loghistory->countrycode = $data->countryCode;
				}
				$loghistory->ip_history_json = $d;
				$loghistory->status = 1;
				$loghistory->save();
				*/
				
				if ($user->int_status == Variables::ACTIVE)
				{
					
					if ($user->int_role_id == Variables::ROLE_ADMIN) 
					{
						return redirect('admin/dashboard');
					}
					else if ($user->int_role_id == Variables::USER) 
					{
						 return redirect('users/dashboard');
						
					}
				}
				else
				{
					Session::flash('error','Your account has been deactivated,Please contact your administrator');
				}
				
			}
			else
			{
				Session::flash('error','Invalid credentials. Try again');
				return back();
			}			
		
		}
	
	}
	
	
    /**
     * Get the failed login message.
     *
     * @return string
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
            ? Lang::get('auth.failed')
            : 'These credentials do not match our records.';
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect('/');
    }
}
