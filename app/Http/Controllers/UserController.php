<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|max:255',
        ]);

        $data = $request->only(['name', 'email']);
        $data['password'] = Hash::make($request->password);

        DB::beginTransaction();
        try {
            User::create($data);
            DB::commit();
            return $this->response(true, "User Created Successfully!");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response(false, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $data = $request->only(['name', 'email']);

        DB::beginTransaction();
        try {
            User::where('id', $id)->update($data);

            DB::commit();
            return $this->response(true, "User Updated Successfully!");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response(false, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::where('id', $id)->delete();

        return $this->response(true, "User Deleted Successfully!");
    }

    function response($status, $message)
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
}
