<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CpUser extends Model
{
    protected $table = 'cp_user';

    public function add($data){
		$id = DB::table($this->table)->insertGetId($data);
    	return $id;    	
    }
}
