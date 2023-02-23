<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('school_id');
            $table->longText('can_cu_1')->nullable();
            $table->longText('dac_diem_ktvhxh_21')->nullable();  
            $table->longText('dac_diem_hocsinh_221')->nullable();  
            $table->longText('tinh_hinh_nhan_vien_222')->nullable();  
            $table->longText('co_so_vat_chat_23')->nullable();
            $table->longText('mtnh_chung_31')->nullable();
            $table->longText('mtnh_cu_the_32')->nullable();
            $table->longText('phan_phoi_thoi_luong_41')->nullable();
            $table->longText('hd_tap_the_421')->nullable();
            $table->longText('hd_ngoai_gio_422')->nullable();
            $table->longText('to_chuc_thuc_hien_diem_truong_43')->nullable();
            $table->longText('khung_thoi_gian_44')->nullable();
            $table->longText('giai_phap_thuc_hien_5')->nullable();
            $table->longText('to_chuc_thuc_hien_6')->nullable();
            $table->longText('content')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });

        Schema::create('school_plan_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('school_plan_id');
            $table->longText('notes');
            $table->tinyInteger('status');
            $table->timestamps();
        });


        //IV.1 Phân phối thời lượng các môn học và hoạt động giáo dục
        Schema::create('school_plan_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('school_plan_id');
            $table->integer('grade');
            $table->longText('thoi_gian_to_chuc_theo_tuan')->nullable();
            $table->longText('ke_hoach_cac_mon')->nullable();
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
        Schema::dropIfExists('school_plan');
        Schema::dropIfExists('school_plan_history');
        Schema::dropIfExists('school_plan_detail');
    }
}
