<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Familytree
{
    public $nama;
    public $jenis_kelamin;
    private $array_group_family_tree = [];

    public function __construct($nama = NULL, $jenis_kelamin = NULL)
    {
        $this->nama = $nama;
        $this->jenis_kelamin = $jenis_kelamin;
    }

    public function insertDataFamily()
    {
        $result = DB::insert('insert into family_tree (nama, jenis_kelamin) values (?, ?)', [$this->nama, $this->jenis_kelamin]);
        if ($result > 0) {
            $inserted_data = DB::select("SELECT * FROM family_tree WHERE nama = '$this->nama' AND jenis_kelamin = '$this->jenis_kelamin'");
            return $inserted_data;
        }
        return [];
    }

    public function insertDataChild($parent_name)
    {
        $data_result = DB::select("SELECT family_id FROM family_tree WHERE nama LIKE '%$parent_name%'");
        if (isset($data_result)) {
            DB::insert('insert into family_tree (parent_id, nama, jenis_kelamin) values (?, ?, ?)', [$data_result[0]->family_id, $this->nama, $this->jenis_kelamin]);
            return TRUE;
        }
        return FALSE;
    }

    public function getDataInserted()
    {
        return [
            'nama' => $this->nama,
            'jenis_kelamin' => $this->jenis_kelamin
        ];
    }

    public function deleteFamilyTree($id)
    {
        $data_family = DB::select("SELECT family_id FROM family_tree WHERE family_id = $id");

        if (isset($data_family)) {
            array_push($this->array_group_family_tree, $data_family[0]->family_id);
            $this->checkTree($data_family[0]->family_id);
            $result = $this->deleteTree();
            if ($result) {
                return TRUE;
            }
            return FALSE;
        }
        return FALSE;
    }

    public function getAllFamily($id)
    {
        $this->checkTree($id);
        array_push($this->array_group_family_tree, $id);

        $data_implode = implode(",", $this->array_group_family_tree);
        $data_family = DB::select("SELECT * FROM family_tree WHERE family_id IN ($data_implode)");

        return $data_family;
    }

    public function updateDataKeluarga($id)
    {
        // check data apakah masih ada
        $data_is_exist = DB::select("SELECT family_id FROM family_tree WHERE family_id = $id");
        if (isset($data_is_exist[0]->family_id)) {

            // ubah datanya
            $affected = DB::update(
                "UPDATE family_tree SET nama = '$this->nama', jenis_kelamin = '$this->jenis_kelamin' WHERE family_id = ?",
                [$id]
            );
            // check apakah berhasil update
            if ($affected) {
                // kembalikan datanya
                $data_updated = DB::select("SELECT * FROM family_tree WHERE family_id = $id");
                return $data_updated;
            }
            return FALSE;
        } else {
            // data tidak ditemukan
            return FALSE;
        }
    }

    public function getCurrentData($id)
    {
        $current_data = DB::select("SELECT * FROM family_tree WHERE family_id = $id");
        if ($current_data) {
            return $current_data;
        }
        return [];
    }

    private function checkTree($id)
    {
        $data_families = DB::select("SELECT family_id FROM family_tree WHERE parent_id = $id");

        if ($data_families) {
            foreach ($data_families as $family) {
                if (isset($family)) {
                    array_push($this->array_group_family_tree, $family->family_id);
                    $this->checkTree($family->family_id);
                }
            }
        }
    }

    private function deleteTree()
    {
        $data_delete = implode(",", $this->array_group_family_tree);

        $result_delete = DB::delete("DELETE FROM family_tree WHERE family_id IN ($data_delete)");
        if ($result_delete > 0) {
            return TRUE;
        }
        return FALSE;
    }
}

class FamilyTreeController extends Controller
{
    public function getAllFamily($id)
    {
        $keluarga = new Familytree();
        $data_family = $keluarga->getAllFamily($id);

        if (count($data_family)) {
            return response()->json(['message' => 'Berhasil mendapatkan data keluarga', 'type' => 'Success', 'data' => $data_family]);
        }
        return response()->json(['message' => 'Gagal mendapatkan data anak', 'type' => 'error', 'data' => []]);
    }

    public function updateFamilyTree(Request $request, $id)
    {
        $nama = $request->input('nama');
        $jenis_kelamin = $request->input('jenis_kelamin');

        $keluarga = new Familytree($nama, $jenis_kelamin);
        $data_result = $keluarga->updateDataKeluarga($id);
        $data_updated = $keluarga->getCurrentData($id);

        if ($data_result) {
            return response()->json(['message' => 'Berhasil ubah data keluarga', 'type' => 'Success', 'data' => $data_updated]);
        }
        return response()->json(['message' => 'Gagal ubah data keluarga', 'type' => 'error', 'data' => []]);
    }

    public function deleteFamilyTree($id)
    {
        $keluarga = new Familytree();
        $result_delete = $keluarga->deleteFamilyTree($id);

        if ($result_delete) {
            return response()->json(['message' => 'Berhasil input data anak', 'type' => 'Success', 'data' => []]);
        }
        return response()->json(['message' => 'Gagal input data anak', 'type' => 'error', 'data' => []]);
    }

    public function insertDataParent(Request $request)
    {
        $nama = $request->input('nama');
        $jenis_kelamin = $request->input('jenis_kelamin');

        $keluarga = new Familytree($nama, $jenis_kelamin);
        $result = $keluarga->insertDataFamily();
        if ($result) {
            return response()->json(['message' => 'Berhasil input data parent', 'type' => 'Success', 'data' => $result]);
        }
        return response()->json(['message' => 'Gagal input data parent', 'type' => 'error', 'data' => $result]);
    }

    public function insertDataChild(Request $request)
    {
        $orangtua = $request->input('orangtua');
        $nama = $request->input('nama');
        $jenis_kelamin = $request->input('jenis_kelamin');

        $keluarga = new Familytree($nama, $jenis_kelamin);
        $result = $keluarga->insertDataChild($orangtua);
        $data_inserted = $keluarga->getDataInserted();

        if ($result) {
            return response()->json(['message' => 'Berhasil input data anak', 'type' => 'Success', 'data' => $data_inserted]);
        }
        return response()->json(['message' => 'Gagal input data anak', 'type' => 'error', 'data' => $data_inserted]);
    }

    /**
     * function to get all children 
     */
    public function get_children($id_family)
    {
        return $this->get_all_children($id_family);
    }

    /**
     * function to get all grandchildren
     */
    public function get_grandchildren($family_id)
    {
        return $this->get_all_grandchildren($family_id);
    }

    /**
     * function to get all female grandchildren
     */
    public function get_all_female_grandchildren($id_family)
    {
        return $this->get_female_grandchildren($id_family);
    }

    /**
     * function to get data auntie
     */
    public function get_all_autie($id_niece)
    {
        return $this->get_all_aunties($id_niece);
    }

    /**
     * function to get all male cousing 
     */
    public function get_all_male_cousing($id_family)
    {
        return $this->get_all_male_cousin($id_family);
    }

    /**
     * private function to get all data family 
     */
    private function get_all_children($id_family)
    {
        $firstBorn = DB::select("select table_2.* from family_tree as table_1
        inner Join family_tree as table_2 on table_1.family_id = table_2.parent_id 
        where table_1.parent_id is null AND table_1.family_id = $id_family");

        return $firstBorn;
    }

    /**
     * private function get all child
     */
    private function get_all_grandchildren($family_id)
    {
        $all_childrens = $this->get_all_children($family_id);

        $array_all_grandchildren = [];

        foreach ($all_childrens as $child) {
            $grand_child = DB::select('select * from family_tree where parent_id = ?', [$child->family_id]);
            if ($grand_child) {
                array_push($array_all_grandchildren, $grand_child);
            }
        }

        return $array_all_grandchildren;
    }

    /**
     * private function untuk mengambil semua cucu perempuan
     */
    private function get_female_grandchildren($id_family)
    {
        $all_childrens = $this->get_all_children($id_family);

        $array_all_female_grandchildren = [];

        foreach ($all_childrens as $child) {
            $grand_child = DB::select("select * from family_tree where jenis_kelamin like '%perempuan%' and parent_id = ?", [$child->family_id]);
            if ($grand_child) {
                array_push($array_all_female_grandchildren, $grand_child);
            }
        }

        return $array_all_female_grandchildren;
    }

    /**
     * private function untuk mengambil semua bibi
     */
    private function get_all_aunties($id_niece = Null)
    {
        $query = DB::select("select * from family_tree where parent_id IN 
        (
            select parent_id from family_tree where family_id IN (
                select parent_id from family_tree where family_id = $id_niece
            )
        ) and jenis_kelamin like '%perempuan%'");

        return $query;
    }

    /**
     * private function untuk mengambil semua sepupu laki-laki 
     */
    private function get_all_male_cousin($id_family)
    {
        $query = DB::select("select * from family_tree where parent_id IN (
            select family_id from family_tree where parent_id IN (
                select parent_id from family_tree where family_id IN (
                    select parent_id from family_tree where family_id = $id_family
                    )
                ) AND family_id NOT IN (
                    select parent_id from family_tree where family_id = $id_family
                    )
                ) AND jenis_kelamin LIKE '%Laki-laki%'");
        return $query;
    }
}
