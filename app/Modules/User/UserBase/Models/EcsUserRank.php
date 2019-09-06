<?php

namespace App\Modules\User\UserBase\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EcsUserRank extends Model
{
    protected $table = 'ecs_user_rank';
    protected $primaryKey = 'rank_id';
    protected $timestamp = false;


    /**
     * 获取用户的user_rank
     *
     * @param [type] $userid
     * @return void
     */
    public static function getUserRank($rankId)
    {
        $rank = EcsUserRank::find($rankId);
        if (empty ($rank)) {
            $rank = EcsUserRank::find(1);
            $rank->rankDiscount = 100;
            $rank->rankRebate = 0;
        } elseif ($rank->rank_type == 'discount') {
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

}
