<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
//use Laracasts\TestDummy\Factory as TestDummy;

//use App\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $superAdmin = new Role();
        $superAdmin->name = 'super-admin';
        $superAdmin->display_name = 'Super Admin';
        $superAdmin->save();

        $admin = new Role();
        $admin->name = 'admin';
        $admin->display_name = 'Admin';
        $admin->save();

        $businessUser = new Role();
        $businessUser->name = 'business-user';
        $businessUser->display_name = 'Business User';
        $businessUser->save();

        $guide = new Role();
        $guide->name = 'guide';
        $guide->display_name = 'Guide';
        $guide->save();

        $traveller = new Role();
        $traveller->name = 'traveller';
        $traveller->display_name = 'Traveller';
        $traveller->save();

        $sponsor = new Role();
        $sponsor->name = 'sponsor';
        $sponsor->display_name = 'Sponsor';
        $sponsor->save();
    }
}
