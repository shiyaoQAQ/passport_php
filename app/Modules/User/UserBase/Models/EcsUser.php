<?php

namespace App\Modules\Admin\Access\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Modules\Admin\Access\Models\LaravelSmsTmp;
use App\Modules\CpAccess;

class EcsUser extends Model
{
    const ROLE_USER  = 'user';
    const ROLE_ADMIN = 'admin';

    protected $table = 'ecs_users';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    /**
     * 电销系统用户关系
     */
    public function salesCustomer()
    {
        return $this->hasMany('App\Modules\Admin\Access\Models\Sales\SalesCustomer', 'uid', 'user_id');
    }

    public function order()
    {
        return $this->hasMany('App\Modules\Admin\Access\Models\Order\OrderInfo', 'user_id', 'user_id');
    }

    public function address()
    {
        return $this->hasMany('App\Modules\Admin\Access\Models\User\EcsUserAddress', 'user_id', 'user_id');
    }

    public function cpUser()
    {
        return $this->hasOne('App\Modules\Admin\Access\Models\CpUser', 'uid', 'user_id');
    }

    public function add($data)
    {
        if (isset($data['user_id'])) {
            unset($data['reg_time']);
            self::where('user_id', $data['user_id'])->update($data);
            return $data['user_id'];
        }
        $one = self::where('mobile_phone', $data['mobile_phone'])->first();
        if (empty($one)) {
            $id = self::insertGetId($data);
        } else {
            unset($data['reg_time']);
            self::where('mobile_phone', $data['mobile_phone'])->update($data);
            $id = $one->user_id;
        }
        return $id;
    }

    public function getById($id)
    {
        $one = self::where('user_id', $id)->first();
        return empty($one) ? array() : $one->toArray();
    }

    public function getByMobile($mobile)
    {
        return self::where('mobile_phone', $mobile)->first();
    }

    public function getNameById($id)
    {
        $one = self::where('user_id', $id)->first();
        return empty($one) ? '' : $one->user_name;
    }

    public function getUserByIdIn($uids)
    {
        if (empty($uids)){
            return array();
        }
        $list = self::whereIn('user_id', $uids)->get();
        return empty($list) ? array() : $list->toArray();
    }

    public function getUserNameByIdIn($uids)
    {
        if (empty($uids)){
            return array();
        }
        $list = self::whereIn('user_id', $uids)->get()->keyBy('user_id');
        return empty($list) ? array() : $list->toArray();
    }
        
    // //扣除返现
    // public function reduceUserSurplus($userId, $reduceSurplus)
    // {
    //     //给我发一条短信
    //     // $smsObj = new LaravelSmsTmp;
    //     // $smsObj->add('13655491631', "{$userId}扣除了{$reduceSurplus}元", 0);
    //     $user = self::findOrFail($userId);
    //     $user->user_money -= $reduceSurplus;
    //     $user->save();
    // }

    // //增加返现
    // public function increaseUserSurplus($userId, $reduceSurplus)
    // {
    //     //给我发一条短信
    //     // $smsObj = new LaravelSmsTmp;
    //     // $smsObj->add('13655491631', "{$userId}增加了{$reduceSurplus}元", 0);
        
    //     $user = self::findOrFail($userId);
    //     $user->user_money += $reduceSurplus;
    //     $user->save();
    // }

    public function getUsersByKeyword($keyword)
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

    /**
     * 获取用户的user_rank
     *
     * @param [type] $userid
     * @return void
     */
    public function getUserRankById($userid)
    {
        $user = $this->getById($userid);
        $rank = EcsUserRank::find(array_get($user, 'user_rank', 0));
        if ($rank->rank_type == 'discount') {
            $rank->rankDiscount = empty($rank->discount) ? 100 : intval($rank->discount);
            $rank->rankRebate = 0;
        } elseif ($rank->rank_type == 'rebate') {
            $rank->rankDiscount = 100;
            $rank->rankRebate = empty($rank->rebate) ? 0 : intval($rank->rebate);
        } else {
            $rank = EcsUserRank::find(1);
            $rank->rankDiscount = 100;
            $rank->rankRebate = 0;
        }

        return $rank;
    }

    public function createSimpleUser($mobile, $data = [])
    {
        $user = new self;
        $user->user_name = isset($data['user_name']) ? $data['user_name'] : $mobile_phone;
        $user->reg_time = time();
        $user->mobile_phone = $mobile;
        $user->is_validated = 0;
        $user->alias = '';
        $user->password = isset($data['password']) ? $data['password'] : '';
        $user->msn = '';
        $user->user_rank = isset($data['user_rank']) ? $data['user_rank'] : 6;
        $user->qq = '';
        $user->office_phone = '';
        $user->home_phone = '';
        $user->last_time = '2017-01-01 00:00:00';
        $user->birthday = '2017-01-01';
        $user->credit_line = 0;
        $user->role = self::ROLE_USER;
        $user->refer = array_get($data, 'refer', '');
        $user->save();
        return $user;
    }
}
