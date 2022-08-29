<?php

namespace App\Http\Controllers;

use App\Models\EmployeeModel;
use App\Models\Parents;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid Credentials.'],
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Successfully Login',
            'token' => $user->createToken('myToken')->plainTextToken,
            'role' => $user->role,
            'profile_id' => $user->profile_id
        ]);

    }

}
