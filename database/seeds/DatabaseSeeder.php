<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(AdminRoleSeeder::class);
         $this->call(AdminPermissionSeeder::class);
         $this->call(AdminMenuSeeder::class);
         $this->call(CheckListSeeder::class);
         $this->call(TaskStatusSeeder::class);
    }
}
