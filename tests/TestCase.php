<?php

namespace EscolaLms\Pages\Tests;

use EscolaLms\Core\EscolaLmsServiceProvider;
use EscolaLms\Core\Models\User;
use EscolaLms\Pages\AuthServiceProvider;
use EscolaLms\Pages\Database\Seeders\PermissionTableSeeder;
use EscolaLms\Pages\EscolaLmsPagesServiceProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
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
    }

    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            EscolaLmsPagesServiceProvider::class,
            PassportServiceProvider::class,
            PermissionServiceProvider::class,
            AuthServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
    }

    protected function authenticateAsAdmin()
    {
        $this->user = config('auth.providers.users.model')::factory()->create();
        $this->user->guard_name = 'api';
        $this->user->givePermissionTo('create pages');
        $this->user->givePermissionTo('update pages');
        $this->user->givePermissionTo('delete pages');

        /** @var User $user */
//        $this->user = User::factory()->create();
//        $this->user = $this->user->assignRole('admin');
//        $this->user->guard_name = 'api';
//        Auth::guard()->setUser($this->user);
//        $user = config('auth.providers.users.model')::factory()->create();
    }
}
