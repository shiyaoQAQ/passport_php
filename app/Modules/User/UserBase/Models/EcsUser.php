<?php

namespace App\Modules\User\UserBase\Models;

use App\Modules\User\UserBase\Constants\UserConst;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EcsUser extends Model
{
    protected $table = 'ecs_users';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    public function cpUser()
    {
        return $this->hasOne('App\Modules\Admin\Access\Models\CpUser', 'uid', 'user_id');
    }

    // public function add($data)
    // {
    //     if (isset($data['user_id'])) {
    //         unset($data['reg_time']);
    //         self::where('user_id', $data['user_id'])->update($data);
    //         return $data['user_id'];
    //     }
    //     $one = self::where('mobile_phone', $data['mobile_phone'])->first();
    //     if (empty($one)) {
    //         $id = self::insertGetId($data);
    //     } else {
    //         unset($data['reg_time']);
    //         self::where('mobile_phone', $data['mobile_phone'])->update($data);
    //         $id = $one->user_id;
    //     }
    //     return $id;
    // }

    /**
     * 获取用户基础信息
     *
     * @param [type] $uid
     * @param array $argument
     * @return void
     */
    public static function getUserInfo($uid, $argument = [])
    {
        if ($select = array_get($argument, 'select')) {
            $user = EcsUser::select($select)->find($uid);
        } else {
            $user = EcsUser::find($uid);
        }
        return $user;
    }

    /**
     * 获取用户基础信息列表
     *
     * @param [type] $uids
     * @return void
     */
    public static function getUserInfoByIds($uids, $argument = [])
    {
        $constructor = EcsUser::whereIn('user_id', $uids);
        if ($select = array_get($argument, 'select')) {
            $constructor->select($select);
        }
        $users = $constructor->get();
        return $users;
    }

    /**
     * 通过手机号获取用户基础信息
     *
     * @param [type] $mobile
     * @return void
     */
    public static function getByMobile($mobile, $argument = [])
    {
        $constructor = self::where('mobile_phone', strval($mobile));
        if ($select = array_get($argument, 'select')) {
            $constructor->select($select);
        }
        $users = $constructor->first();
        return $users;
    }

    /**
     * 通过用户uids获取id name的kv映射
     */
    public static function getUserNameMap($uids)
    {
        $result = EcsUser::whereIn('user_id', $uids)
            ->pluck('user_name', 'user_id')
            ->toArray();
        return $result;
    }

    /**
     * 通过用户uids获取id mobile的kv映射
     */
    public static function getUserMobileMap($uids)
    {
        $result = EcsUser::whereIn('user_id', $uids)
            ->pluck('mobile_phone', 'user_id')
            ->toArray();
        return $result;
    }

    // public function getNameById($id)
    // {
    //     $one = self::where('user_id', $id)->first();
    //     return empty($one) ? '' : $one->user_name;
    // }

    // public function getUserByIdIn($uids)
    // {
    //     if (empty($uids)){
    //         return array();
    //     }
    //     $list = self::whereIn('user_id', $uids)->get();
    //     return empty($list) ? array() : $list->toArray();
    // }

    // public function getUserNameByIdIn($uids)
    // {
    //     if (empty($uids)){
    //         return array();
    //     }
    //     $list = self::whereIn('user_id', $uids)->get()->keyBy('user_id');
    //     return empty($list) ? array() : $list->toArray();
    // }

    public static function getUsersByKeyword($keyword)
    {
        $users = self::select([
                'user_id', 
                'user_name', 
                'mobile_phone', 
            ])->whereRaw("(user_name like '%$keyword%' or mobile_phone like '$keyword%' or user_id = '$keyword')")
            ->limit(20)
            ->orderBy('user_id', 'asc')
            ->get();
        if ($users->isEmpty()) {
            return [];
        } else {
            return $users->toArray();
        }
    }

    public static function createSimpleUser($data = [])
    {
        $user = new self;
        $user->user_name = $data['user_name'] ?? $data['mobile'];
        $user->reg_time = time();
        $user->mobile_phone = $data['mobile'];
        $user->city_id = $data['city_id'] ?: 0;
        $user->refer = $data['refer'] ?: 'default';
        $user->is_validated = 0;
        $user->alias = '';
        $user->msn = '';
        $user->qq = '';
        $user->office_phone = '';
        $user->home_phone = '';
        $user->last_time = '2017-01-01 00:00:00';
        $user->birthday = '2017-01-01';
        $user->credit_line = 0;
        $user->user_rank = $data['user_rank'] ?? UserConst::DEFAULT_RANK;
        $user->role = UserConst::ROLE_USER;
        $user->save();
        return $user;
    }
}
