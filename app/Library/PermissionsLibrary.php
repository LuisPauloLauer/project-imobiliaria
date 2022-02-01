<?php

namespace App\Library;

use Illuminate\Support\Facades\Auth;

class PermissionsLibrary
{
    /**
     * @return array
     */
    public static function verifyPermissionMaster(): array
    {
        $currentUser = Auth::user();
        if ($currentUser->isMaster()
            && $currentUser->hasPermission(\App\Laravue\Acl::PERMISSION_USER_MANAGE)
        ){
            $returnArray = [
                'enable'    => true,
                'response'  => response()->json(['success' => 'Permissão liberada']),
            ];
        } else {
            $returnArray = [
                'enable'    => false,
                'response'  => response()->json(['error' => 'Permissão negada'], 403),
            ];
        }

        return  $returnArray;
    }
}
