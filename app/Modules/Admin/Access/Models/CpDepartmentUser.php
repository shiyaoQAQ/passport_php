<?php

namespace App\Modules\Admin\Access\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class CpDepartmentUser extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    const IS_DELETED  = 1;
    const NOT_DELETED = 0;

    protected $table = 'cp_department_user';
    public $timestamps = false;

    public function del($did, $uid, $operatorId){
        if(empty($did) || empty($uid)){
            return false;
        }
        $data = array(
                // 'is_deleted' => self::IS_DELETED,
                'admin_uid'  => $operatorId,
                'deleted_at' => date('Y-m-d H:i:s'),
            );
        $con = array(
                'uid'           => $uid,
                'department_id' => $did,
            );
        $ret = self::where($con)->update($data);
        return $ret ? true : false;
    }

    public function add($did, $userId, $opUid = 0){
        $userData = array(
            'department_id' => $did,
            'uid' => $userId,
            'is_deleted' => self::NOT_DELETED,
            'admin_uid'  => $opUid,
        );
        $id = DB::table($this->table)->insertGetId($userData);
        return $id;
    }    

    public function get($did, $userId){
        $con = array('department_id'=>$did, 'uid'=>$userId, 'is_deleted'=>self::NOT_DELETED);
        $info = self::where($con)->first();
        return empty($info) ? array() : $info->toArray();
    }
    public function getDidByUser($userId){
        return self::select('department_id')->where([
            'uid'=>$userId,
            'is_deleted'=>self::NOT_DELETED
        ])->pluck('department_id')->toArray();
    }
    public function getUserByDidIn($did){
        if(empty($did)){
            return array();
        }
        if(!is_array($did)){
            return $this->getUserByDid($did);
        }
        $ret = self::whereIn('department_id', $did)->where(array('is_deleted'=>self::NOT_DELETED))->get();
        return empty($ret) ? array() : $ret->toArray();
    }
    public function getUserByDid($did){
        if(empty($did)){
            return array();
        }
        $ret = self::where('department_id', $did)->where(array('is_deleted'=>self::NOT_DELETED))->get();
        return empty($ret) ? array() : $ret->toArray();
    }
    public function getUserDepartByUid($uid){
        if(empty($uid)){
            return array();
        }
        $ret = self::where(array('uid'=>$uid,'is_deleted'=>self::NOT_DELETED))->get();
        return empty($ret) ? array() : $ret->toArray();
    }
}
