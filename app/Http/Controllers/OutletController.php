<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $outlets = Outlet::all();
        return response()->json([
            'status' => true,
            'data' => $outlets
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
            'phone' => 'required|unique:outlets,phone',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255'
        ]);

        $data = $request->only(['name', 'phone', 'latitude', 'longitude']);

        DB::beginTransaction();
        try {
            //Store image
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $extension;
                $file->move('uploads/', $fileName);
                $data['image'] = 'uploads/' . $fileName;
            }

            Outlet::create($data);

            DB::commit();
            return $this->response(true, "Outlet Created Successfully!");
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
        $outlet = Outlet::find($id);
        return response()->json([
            'status' => true,
            'data' => $outlet
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
            'phone' => 'required|unique:outlets,phone,' . $id,
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255'
        ]);

        $data = $request->only(['name', 'phone', 'latitude', 'longitude']);

        DB::beginTransaction();
        try {
            //Store image
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $extension;
                $file->move('uploads/', $fileName);
                $data['image'] = 'uploads/' . $fileName;
            }

            Outlet::where('id', $id)->update($data);

            DB::commit();
            return $this->response(true, "Outlet Updated Successfully!");
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
        Outlet::where('id', $id)->delete();

        return $this->response(true, "Outlet Deleted Successfully!");
    }

    function response($status, $message)
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    function getOutletLocation($id)
    {
        $outlet = Outlet::find($id);
        $result = $this->getLocationDetails($outlet->latitude, $outlet->longitude);
        // return $result;
        return response()->json([
            'status' => $result->status,
            'data' => $result
        ]);
    }

    private function getLocationDetails($latitude, $longitude)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($latitude) . ',' . trim($longitude) . '&sensor=false';
        $json = @file_get_contents($url);
        $data = json_decode($json);

        return $data;
    }
}
