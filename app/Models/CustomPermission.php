<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class CustomPermission extends SpatiePermission // Change the class name to avoid conflict
{
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->where('is_active',1);
    }

}

