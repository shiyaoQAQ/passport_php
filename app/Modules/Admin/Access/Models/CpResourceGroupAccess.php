<?php

namespace App\Modules\Admin\Access\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CpResourceGroupAccess extends Model
{
    protected $table = 'cp_resource_group_access';

    public function getResourceByGroupId($gid){
		$ret = self::where(array('gid'=>$gid))->get()->toArray();
		return empty($ret) ? array() : $ret;    		
    }

	public function addResources($datas, $gid){
		if(empty($datas)){
			return false;
		}
		foreach ($datas as $one) {
			$resource = array(
					'con_resource' => $one['controller'] . '-' . $one['resource'],
					'gid'        => $gid,
					'controller' => $one['controller'],
					'resource'     => $one['resource'],
				);	
			$one = self::where(array('con_resource'=>$resource['con_resource'], 'gid'=> $resource['gid']))->first();
			if(empty($one)){
				DB::table($this->table)->insert($resource);
			}			
		}
		return true;

	}

	public function getGidByResource($controller, $resource){
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

	public function removeResources($datas, $gid){
		if(empty($datas) || empty($gid)){
			return false;
		}
		$keyArr = array();
		foreach ($datas as $one) {
			$keyArr[] = $one['controller'] . '-' . $one['resource'];
		}	
		$keyArr = array_chunk($keyArr, 100);
		foreach ($keyArr as $chunkKey) {
			DB::table($this->table)->where(array('gid'=>$gid))->whereIn('con_resource', $chunkKey)->delete();
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

	public function getResourcesByGids($gids){
		if(empty($gids)){
			return array();
		}
		$ret = self::whereIn('gid', $gids)->get();
		return empty($ret) ? array() : $ret->toArray();
	}	
}
