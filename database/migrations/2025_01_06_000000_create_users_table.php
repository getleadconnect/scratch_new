<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_users', function (Blueprint $table) {
            $table->increments('pk_int_user_id');
			$table->string('customer_id')->nullable();
			$table->string('vchr_user_name');
			$table->string('email');
			$table->string('country_code')->default('91');
            $table->string('mobile')->nullable()->unique();
			$table->string('vchr_user_mobile')->nullable();
            $table->string('vchr_user_imei')->nullable();
            $table->string('password');
			$table->string('otp',10)->nullable();
			$table->dateTime('password_validity')->nullable();
            $table->string('vchr_logo')->nullable();
			$table->dateTime('datetime_last_login')->nullable()->default(null);
			$table->integer('int_role_id')->nullable();
            $table->integer('permission_role_id')->nullable();
            $table->tinyInteger('is_co_admin')->nullable();
            $table->integer('reward')->nullable();
            $table->integer('rank')->nullable();
			$table->integer('telegram_id')->nullable();
			$table->integer('parent_user_id')->nullable();
			$table->tinyInteger('sticky_agent')->default(0);
            $table->string('designation_id')->nullable();
            $table->string('company_name')->nullable();
			$table->integer('branch_id')->nullable();
			$table->string('address')->nullable();
			$table->string('location')->nullable();
			$table->string('latitude')->nullable();;
			$table->string('longitude')->nullable();
			$table->string('battery')->nullable();
			$table->string('device_model')->nullable();
			$table->string('os_version')->nullable();
			$table->string('speed')->nullable();
			$table->string('angle')->nullable();
			$table->string('visa_id')->nullable();
			$table->string('gst_number')->nullable();
			$table->integer('verify_email')->nullable();
			$table->integer('int_module')->nullable();
			$table->integer('int_registration_form')->default(1);
			$table->integer('int_status')->nullable();
			$table->text('fcm_token')->nullable();
			$table->dateTime('last_app_used')->nullable();
			$table->string('last_app_version')->nullable();
			$table->tinyInteger('calling_method')->nullable();
			$table->text('enquiry_display_fields')->nullable();
			$table->tinyInteger('call_forwarded')->nullable();
			$table->string('extension')->nullable();
			$table->string('email_password')->nullable();
			$table->tinyInteger('web_sound')->nullable();
			$table->string('time_zone')->nullable();
			$table->tinyInteger('web_notification')->nullable();
			$table->string('employee_code')->nullable();
			$table->date('subscription_start_date')->nullable();
			$table->date('subscription_end_date')->nullable();
			$table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('users');
    }
}
