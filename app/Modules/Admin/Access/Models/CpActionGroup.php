<?php

namespace App\Modules\Admin\Access\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CpActionGroup extends Model
{
    protected $table = 'cp_action_group';

    public function getByIds($gids){
		$ret = self::whereIn('id', $gids)->get();
		return empty($ret) ? array() : $ret->toArray();    	
    }

    public function del($id){
		if(empty($id)){
			return false;
		}
		$con = array(
				'id' => $id,
			);
		$ret = self::where($con)->delete();
		return $ret ? true : false;
	}


    public function getList(){
		$ret = self::all();
		return empty($ret) ? array() : $ret->toArray();
	}    

	public function updateGroup($id, $name, $desc){
		$data = array(
				'name' => $name,
				'desc' => $desc,
			);
		$ret = DB::table($this->table)->where(array('id'=>$id))->update($data);
		return $ret ? true : false;
	}

	public function add($name, $desc){
		$data = array(
				'name' => $name,
				'desc' => $desc,
			);
		$id = DB::table($this->table)->insert($data);
		return true;
	}	

}
