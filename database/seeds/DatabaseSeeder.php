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
        $this->call([
            CountrySeeder::class,
            UserGroupSeeder::class,
            RoleGroupSeeder::class,
            RoleSeeder::class,
            ApiSeeder::class,
            RoleApiSeeder::class,
            UserSeeder::class,
            UserRoleSeeder::class,
            UserGroupRoleSeeder::class,
            UserApiSeeeder::class,
            FundCompanySeeder::class,
            FundProductTypeSeeder::class,
            AccountSystemSeeder::class,
            // TestTableSeeder::class,
            SupervisingBankSeeder::class,
            MailTemplateSeeder::class,
            MailTemplateLocaleSeeder::class,
            IdTypeSeeder::class,
            BankSeeder::class,            
            TestSeeder::class,
        ]);
    }
}
