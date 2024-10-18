<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    //
    public function getUserData(Request $request)
    {
        // return $user = Auth::user();

        $user = auth()->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,  // Assuming `role` is a field in your users table
        ]);
    }

}
