<?php

namespace App\Modules\Admin\Access\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CpUser extends Model
{
    protected $table = 'cp_user';

    public function add($data){
        $id = DB::table($this->table)->insertGetId($data);
    	return $id;    	
    }

    /**
     * 获取cp用户基础信息
     *
     * @param [type] $uid
     * @param array $argument
     * @return void
     */
    public static function getUserInfo($uid, $argument = [])
    {
        if ($select = array_get($argument, 'select')) {
            $user = CpUser::select($select)->where('uid', $uid)->first();
        } else {
            $user = CpUser::where('uid', $uid)->first();
        }
        return $user;
    }
}
