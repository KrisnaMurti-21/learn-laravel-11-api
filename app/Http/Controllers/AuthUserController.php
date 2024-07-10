<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthUserController extends Controller
{
    public function RegisterUser(Request $request)
    {
        $dataUser = new User();
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'proses validasi gagal',
                'data' => $validator->errors()
            ], 401);
        }

        $dataUser->name = $request->name;
        $dataUser->email = $request->email;
        $dataUser->password = Hash::make($request->password);
        $dataUser->save();
        return response()->json([
            'status' => true,
            'message' => 'data berhasil ditambahkan',
        ], 201);
    }

    public function loginUser(Request $request)
    {
        $dataUser = new User();
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'proses login gagal',
                'data' => $validator->errors()
            ], 401);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'email atau password salah'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $role = Role::join('user_role', 'user_role.role_id', '=', 'roles.id')
            ->join('users', 'users.id', '=', 'user_role.user_id')
            ->where('users.id', $user->id)
            ->pluck('roles.role_name')
            ->toArray();
        if (empty($role)) {
            $role = ["*"];
        }
        $token = $user->createToken('auth_token', $role)->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'anda berhasil login',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    public function filluserrole(Request $request)
    {
        $data = DB::table('user_role')->where('user_id', 1)->first();

        if ($data) {
            DB::table('user_role')
                ->where('user_id', 1)
                ->update([
                    'role_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'data diubah',
        ], 201);
    }
}
