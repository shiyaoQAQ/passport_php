<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CpAdminLog extends Model
{

	const REF_TYPE_ALLOCATION = 1;//数据分配
    const REF_TYPE_ORDER_PAY  = 2;//订单付款记录
    const REF_TYPE_ORDER_REFUND  = 3;//订单付款记录

    protected $table = 'cp_admin_log';

    public function add($data){
    	$id = DB::table($this->table)->insertGetId($data);
    	return $id;
    }

    public function getList($refTypeArr, $refId, $limit = 0){
        if(!is_array($refTypeArr)){
            $refTypeArr = [$refTypeArr];
        }
        if (empty($limit)) {
            $ret = self::where('ref_id',$refId)->whereIn('ref_type',$refTypeArr)->orderBy('id', 'desc')->get();
        } else {
            $ret = self::where('ref_id',$refId)->whereIn('ref_type',$refTypeArr)->orderBy('id', 'desc')->take($limit)->get();
        }
    	return empty($ret) ? array() : $ret->toArray();
    }
}
