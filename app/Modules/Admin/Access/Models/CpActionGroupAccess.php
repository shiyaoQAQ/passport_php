<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CpActionGroupAccess extends Model
{
    protected $table = 'cp_action_group_access';

    public function getActionByGroupId($gid){
		$ret = self::where(array('gid'=>$gid))->get()->toArray();
		return empty($ret) ? array() : $ret;    		
    }

	public function addActions($datas, $gid){
		if(empty($datas)){
			return false;
		}
		foreach ($datas as $one) {
			$action = array(
					'con_action' => $one['controller'] . '-' . $one['action'],
					'gid'        => $gid,
					'controller' => $one['controller'],
					'action'     => $one['action'],
					'inherit'    => $one['inherit'],
					'data_limit' => $one['limit'],
				);	
			$one = self::where(array('con_action'=>$action['con_action'], 'gid'=> $action['gid']))->first();
			if(empty($one)){
				DB::table($this->table)->insert($action);
			}			
		}
		return true;

	}

	public function removeActions($datas, $gid){
		if(empty($datas) || empty($gid)){
			return false;
		}
		$keyArr = array();
		foreach ($datas as $one) {
			$keyArr[] = $one['controller'] . '-' . $one['action'];
		}	
		$keyArr = array_chunk($keyArr, 100);
		foreach ($keyArr as $chunkKey) {
			DB::table($this->table)->where(array('gid'=>$gid))->whereIn('con_action', $chunkKey)->delete();
		}
		return true;
	}    

	public function del($gid){
		if(empty($gid)){
			return false;
		}
		$con = array(
				'gid' => $gid,
			);
		$ret = self::where($con)->delete();
		return $ret ? true : false;
	}	

	public function getActionsByGids($gids){
		if(empty($gids)){
			return array();
		}
		$ret = self::whereIn('gid', $gids)->get();
		return empty($ret) ? array() : $ret->toArray();
	}	


}
