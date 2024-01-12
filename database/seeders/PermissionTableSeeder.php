<?php

namespace EscolaLms\Scorm\Database\Seeders;

use EscolaLms\Scorm\Enums\ScormPermissionsEnum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::findOrCreate('admin', 'api');
        $tutor = Role::findOrCreate('tutor', 'api');

        foreach (ScormPermissionsEnum::getValues() as $permission) {
            Permission::findOrCreate($permission, 'api');
        }

        $admin->givePermissionTo(ScormPermissionsEnum::adminPermissions());
        $tutor->givePermissionTo(ScormPermissionsEnum::tutorPermissions());
    }
}
