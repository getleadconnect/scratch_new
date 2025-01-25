<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $user = DB::table('tbl_users')->where('int_role_id',\App\Common\Variables::ROLE_ADMIN)->first();
        if(!$user){
			$user = new \App\Models\User();
			$user->vchr_user_name = "admin";
			$user->email = "admin@gmail.com";
			$user->mobile=123456789;
			$user->country_code=91;
			$user->vchr_user_mobile=91123456789;
			$user->vchr_user_imei=123456789;
			$user->password = bcrypt('gls@321');
			$user->int_role_id = \App\Common\Variables::ROLE_ADMIN;
			$user->int_status = \App\Common\Variables::ACTIVE;
			$user->save();  
        }
        
    }
}
