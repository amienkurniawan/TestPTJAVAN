<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Validator;

class IndonesiaController extends Controller
{

    /**
     * function untuk mengambil semua data desa
     */
    public function getAllDesa()
    {

        $datas = \Indonesia::allVillages();
        return response()->json(['message' => 'Berhasil mendapatkan data setiap desa', 'type' => 'success', 'data' => $datas]);
    }

    /**
     * function untuk mengambil data desa
     */
    public function getDesa($villageId)
    {
        $data = \Indonesia::findVillage($villageId, $with = null);
        if ($data) {
            return response()->json(['message' => 'Berhasil mendapatkan data setiap desa', 'type' => 'success', 'data' => $data]);
            //if not exist send error message
        } else {
            return response()->json(['message' => 'Gagal mendapatkan data dari desa', 'type' => 'failed', 'data' => []]);
        }
    }

    /**
     * function untuk menghapus data desa
     */
    public function deleteDesa($villageId)
    {
        // find village 
        $data = \Indonesia::findVillage($villageId, $with = null);
        // if exist then delete
        if ($data) {
            $deleted = DB::table('indonesia_villages')->where('id', '=', $villageId)->delete();
            if ($deleted) {
                return response()->json(['message' => 'Berhasil menghapus data dari desa', 'type' => 'success', 'data' => []]);
            }
            //if not exist send error message
        } else {
            return response()->json(['message' => 'Gagal menghapus data dari desa', 'type' => 'failed', 'data' => []]);
        }
    }

    /**
     * function untuk membuat data desa
     */
    public function createDesa(Request $request)
    {

        // get data last desa by distric code then increment 1
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'distric_code' => 'required|exists:indonesia_districts,code',
            'meta' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages(), 'type' => 'failed', 'data' => []]);
        } else {

            $villageName = $request->input('nama');
            $villageDistrictCode = $request->input('distric_code');
            $villageMetaData = $request->input('meta');

            // check code 
            $data_last_villages = DB::table('indonesia_villages')->select(DB::raw("max(`code`)as max_code"))->where('district_code', '=', $villageDistrictCode)->first();

            if ($data_last_villages) {
                $villageCode = intval($data_last_villages->max_code) + 1;
                $mytime = \Carbon\Carbon::now();

                $insertDataId = DB::table('indonesia_villages')->insertGetId([
                    'name' => $villageName,
                    'code' => $villageCode,
                    'district_code' => $villageDistrictCode,
                    'meta' => json_encode($villageMetaData),
                    'created_at' => $mytime
                ]);

                if ($insertDataId > 0) {
                    // find data last insert
                    $data = \Indonesia::findVillage($insertDataId, $with = null);
                    if ($data) {
                        return response()->json(['message' => 'Berhasil menambahkan data desa', 'type' => 'success', 'data' => $data]);
                    }
                } else {
                    return response()->json(['message' => 'Gagal menambahkan data desa', 'type' => 'failed', 'data' => []]);
                }
            } else {
                return response()->json(['message' => 'Gagal menambahkan data desa, distric code tidak valid', 'type' => 'failed', 'data' => []]);
            }
        }
    }


    /**
     * function untuk mengubah data desa
     */
    public function updateDesa(Request $request, $villageId)
    {
        // get data last desa by distric code then increment 1
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'distric_code' => 'required|exists:indonesia_districts,code',
            'meta' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages(), 'type' => 'failed', 'data' => []]);
        } else {

            $villageName = $request->input('nama');
            $villageDistrictCode = $request->input('distric_code');
            $villageMetaData = $request->input('meta');

            $findLastData = DB::table('indonesia_villages')->select('code', 'district_code', 'name')->where('district_code', '=', $villageDistrictCode)->first();

            $mytime = \Carbon\Carbon::now();

            if ($findLastData) {
                if ($villageDistrictCode != $findLastData->district_code) {
                    // check code 
                    $data_last_villages = DB::table('indonesia_villages')->select(DB::raw("max(`code`)as max_code"))->where('district_code', '=', $villageDistrictCode)->first();

                    if ($data_last_villages) {
                        $villageCode = intval($data_last_villages->max_code) + 1;


                        $insertDataId = DB::table('indonesia_villages')->insertGetId([
                            'name' => $villageName,
                            'code' => $villageCode,
                            'district_code' => $villageDistrictCode,
                            'meta' => json_encode($villageMetaData),
                            'created_at' => $mytime
                        ]);

                        if ($insertDataId > 0) {
                            // find data last insert
                            $data = \Indonesia::findVillage($insertDataId, $with = null);
                            if ($data) {
                                return response()->json(['message' => 'Berhasil mengubah data desa', 'type' => 'success', 'data' => $data]);
                            }
                        } else {
                            return response()->json(['message' => 'Gagal menambahkan data desa', 'type' => 'failed', 'data' => []]);
                        }
                    } else {
                        return response()->json(['message' => 'Gagal menambahkan data desa, distric code tidak valid', 'type' => 'failed', 'data' => []]);
                    }
                } else {
                    $updateDataId = DB::table('indonesia_villages')->where('id', $villageId)->update([
                        'name' => $villageName,
                        'meta' => json_encode($villageMetaData),
                        'updated_at' => $mytime
                    ]);

                    if ($updateDataId > 0) {
                        $data = \Indonesia::findVillage($villageId, $with = null);
                        return response()->json(['message' => 'Berhasil mengubah data desa', 'type' => 'success', 'data' => $data]);
                    }
                }
            } else {
                return response()->json(['message' => 'Gagal menambahkan data desa, ivalid id update', 'type' => 'failed', 'data' => []]);
            }
        }
    }
}
