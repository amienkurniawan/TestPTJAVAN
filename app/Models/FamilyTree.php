<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyTree extends Model
{
    use HasFactory;

    protected $table = 'family_tree';
    protected $primaryKey = 'family_id';

    public $nama;
    public $jenis_kelamin;
}
