<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CpDepartmentResource extends Model
{
    protected $table = 'cp_department_resource';

	const TYPE_GROUP    = 1;
	const TYPE_RESOURCE = 2;

	public function getByDids($dids){
		$ret = self::whereIn('department_id', $dids)->get();
		return empty($ret) ? array() : $ret->toArray();
	}    

	public function getResourceByDids($dids){
		$ret = self::whereIn('department_id', $dids)->where('resource_type', self::TYPE_RESOURCE)->get();
		return empty($ret) ? array() : $ret->toArray();			
	}

    public function getGroupByDid($did){
		$ret = self::where(array('department_id'=>$did,'resource_type'=>self::TYPE_GROUP))->get();
		return empty($ret) ? array() : $ret->toArray();    	
    }

    public function getResourceGroupsByDids($dids){
		if(empty($dids)){
            return array();
        }
        $ret = self::where(array('resource_type'=>self::TYPE_GROUP))->whereIn('department_id', $dids)->get();
        return empty($ret) ? array() : $ret->toArray();    		
    }

	public function getByIds($gids){
        $ret = self::table(self::TABLE)->whereIn('id', $gids)->get();
        return empty($ret) ? array() : $ret->toArray();
    }    

    public function getByResource($controller, $resource){
		
		if(is_array($resource)) {

			foreach ($resource as $v) {
				$conResource[] = $controller . '-' . $v;
			}
		} else {
			$conResource = [$controller . '-' . $resource];
		}
		$ret = self::whereIn('con_resource', $conResource)->get();
		return empty($ret) ? array() : $ret->toArray();	
	}   	 

    public function getDepartByGroupId($gid){
		$ret = self::where(array('group_id'=>$gid,'resource_type'=>self::TYPE_GROUP))->get();
		return empty($ret) ? array() : $ret->toArray();
	}    

	public function getDepartByGroupIdIn($gids){
		$ret = self::whereIn('group_id', $gids)->where('resource_type', self::TYPE_GROUP)->get();
		return empty($ret) ? array() : $ret->toArray();		
	}

    public function getDeaprtResourceList($did){
		$ret = self::where(array('department_id'=>$did,'resource_type'=>self::TYPE_RESOURCE))->get()->toArray();
		return empty($ret) ? array() : $ret;
    }

	public function addResources($datas, $did){
		if(empty($datas)){
			return false;
		}
		foreach ($datas as $one) {
			$resource = array(
					'con_resource'  => $one['controller'] . '-' . $one['resource'],
					'department_id' => $did,
					'controller'    => $one['controller'],
					'resource'      => $one['resource'],
					'resource_type' => self::TYPE_RESOURCE,
				);	
			$one = self::where(array('con_resource'=>$resource['con_resource'], 'department_id'=> $resource['department_id']))->first();
			if(empty($one)){
				DB::table($this->table)->insert($resource);
			}
		}
		return true;
	}

	public function removeResources($datas, $did){
		if(empty($datas)){
			return false;
		}
		$keyArr = array();
		foreach ($datas as $one) {
			$keyArr[] = $one['controller'] . '-' . $one['resource'];
		}	
		$keyArr = array_chunk($keyArr, 100);
		foreach ($keyArr as $chunkKey) {
			DB::table($this->table)->where(array('department_id'=>$did))->whereIn('con_resource', $chunkKey)->delete();
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
					'resource_type'   => self::TYPE_GROUP,
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
			DB::table($this->table)->where(array('group_id'=>$gid,'resource_type'=>self::TYPE_GROUP))->whereIn('department_id', $chunkKey)->delete();
		}
		return true;
	}    
}
