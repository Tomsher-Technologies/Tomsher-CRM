<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CustomPermission;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('datas', 'deleted_at')) {
            Schema::table('datas', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        $manageData = CustomPermission::where('name', 'manage_data')->first();
        $parentId = $manageData ? $manageData->id : null;

        $permission = CustomPermission::firstOrCreate(
            ['name' => 'delete_data'],
            [
                'parent_id' => $parentId,
                'title' => 'Delete Data',
                'guard_name' => 'web',
                'is_active' => 1,
            ]
        );

        // Grant delete_data to Super Admin (role_id 1) if exists
        $superAdmin = DB::table('roles')->where('id', 1)->first();
        if ($superAdmin && $permission) {
            DB::table('role_has_permissions')->updateOrInsert([
                'permission_id' => $permission->id,
                'role_id' => $superAdmin->id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('datas', 'deleted_at')) {
            Schema::table('datas', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        $permission = CustomPermission::where('name', 'delete_data')->first();
        if ($permission) {
            DB::table('role_has_permissions')->where('permission_id', $permission->id)->delete();
            $permission->delete();
        }
    }
};
