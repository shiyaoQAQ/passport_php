<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CpDepartmentAction extends Model
{
	const TYPE_GROUP  = 1;
	const TYPE_ACTION = 2;

    protected $table = 'cp_department_action';

	public function getByDids($dids){
		$ret = self::whereIn('department_id', $dids)->get();
		return empty($ret) ? array() : $ret->toArray();
	}    

    public function getGroupByDid($did){
		$ret = self::where(array('department_id'=>$did,'action_type'=>self::TYPE_GROUP))->get();
		return empty($ret) ? array() : $ret->toArray();    	
    }


    public function getDepartByGroupId($gid){
		$ret = self::where(array('group_id'=>$gid,'action_type'=>self::TYPE_GROUP))->get();
		return empty($ret) ? array() : $ret->toArray();
	}    

    public function getDeaprtActionList($did){
		$ret = self::where(array('department_id'=>$did,'action_type'=>self::TYPE_ACTION))->get()->toArray();
		return empty($ret) ? array() : $ret;
    }

	public function addActions($datas, $did){
		if(empty($datas)){
			return false;
		}
		foreach ($datas as $one) {
			$action = array(
					'con_action'    => $one['controller'] . '-' . $one['action'],
					'department_id' => $did,
					'controller'    => $one['controller'],
					'action'        => $one['action'],
					'data_limit'    => $one['limit'],
					'action_type'   => self::TYPE_ACTION,
				);	
			$one = self::where(array('con_action'=>$action['con_action'], 'department_id'=> $action['department_id']))->first();
			if(empty($one)){
				DB::table($this->table)->insert($action);
			}
		}
		return true;
	}

	public function removeActions($datas, $did){
		if(empty($datas)){
			return false;
		}
		$keyArr = array();
		foreach ($datas as $one) {
			$keyArr[] = $one['controller'] . '-' . $one['action'];
		}	
		$keyArr = array_chunk($keyArr, 100);
		foreach ($keyArr as $chunkKey) {
			DB::table($this->table)->where(array('department_id'=>$did))->whereIn('con_action', $chunkKey)->delete();
		}
		return true;
	}  

	public function addGroups($dids, $gid){
		if(empty($dids)){
			return false;
		}
		foreach ($dids as $one) {
			$action = array(
					'group_id'      => $gid,
					'department_id' => $one,
					'action_type'   => self::TYPE_GROUP,
				);	
			$one = self::where(array('group_id'=>$action['group_id'], 'department_id'=> $action['department_id']))->first();
			if(empty($one)){
				DB::table($this->table)->insert($action);
			}
		}
		return true;
	}

	public function removeGroups($dids, $gid){
		if(empty($dids)){
			return false;
		}
		$keyArr = array_chunk($dids, 100);
		foreach ($keyArr as $chunkKey) {
			DB::table($this->table)->where(array('group_id'=>$gid,'action_type'=>self::TYPE_GROUP))->whereIn('department_id', $chunkKey)->delete();
		}
		return true;
	}

}
