<?php

namespace EscolaLms\Scorm\Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    public function run()
    {
        // create permissions
        $admin = Role::findOrCreate('admin', 'api');
        $tutor = Role::findOrCreate('tutor', 'api');

        Permission::findOrCreate('update Scorm', 'api');
        Permission::findOrCreate('delete Scorm', 'api');
        Permission::findOrCreate('create Scorm', 'api');

        Permission::findOrCreate('set track Scorm', 'api');
        Permission::findOrCreate('get track Scorm', 'api');

        $admin->givePermissionTo(['update Scorm', 'delete Scorm', 'create Scorm', 'set track Scorm', 'get track Scorm']);
        $tutor->givePermissionTo(['update Scorm', 'delete Scorm', 'create Scorm', 'set track Scorm', 'get track Scorm']);
    }
}
