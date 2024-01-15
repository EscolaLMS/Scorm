<?php

namespace EscolaLms\Scorm\Tests;

use EscolaLms\Auth\Models\User;
use EscolaLms\Scorm\AuthServiceProvider;
use EscolaLms\Scorm\Database\Seeders\PermissionTableSeeder;
use EscolaLms\Scorm\EscolaLmsScormServiceProvider;
use EscolaLms\Scorm\Tests\Models\Client;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends \EscolaLms\Core\Tests\TestCase
{
    use DatabaseTransactions;

    public $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionTableSeeder::class);
        Passport::useClientModel(Client::class);
        $this->authenticateAsAdmin();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            EscolaLmsScormServiceProvider::class,
            PassportServiceProvider::class,
            PermissionServiceProvider::class,
            AuthServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set(
            'scorm',
            [
                'table_names' => [
                    'user_table' => 'users',
                    'scorm_table' => 'scorm',
                    'scorm_sco_table' => 'scorm_sco',
                    'scorm_sco_tracking_table' => 'scorm_sco_tracking',
                ],
                // Scorm directory. You may create a custom path in file system
                'disk' => 'local'
            ]
        );
        $app['config']->set('auth.providers.users.model', User::class);
    }

    protected function authenticateAsAdmin()
    {
        $this->user = config('auth.providers.users.model')::factory()->create();

        $this->user->guard_name = 'api';
        $this->user->assignRole('admin');
    }
}
