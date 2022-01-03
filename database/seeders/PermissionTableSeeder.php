<?php

namespace EscolaLms\Scorm\Database\Seeders;

use EscolaLms\Scorm\Enums\ScormPermissionsEnum;
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

        $permissions = [
            ScormPermissionsEnum::SCORM_LIST,
            ScormPermissionsEnum::SCORM_READ,
            ScormPermissionsEnum::SCORM_CREATE,
            ScormPermissionsEnum::SCORM_UPDATE,
            ScormPermissionsEnum::SCORM_DELETE,
            ScormPermissionsEnum::SCORM_SET_TRACK,
            ScormPermissionsEnum::SCORM_GET_TRACK,
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'api');
        }

        $admin->givePermissionTo($permissions);
        $tutor->givePermissionTo($permissions);
    }
}
