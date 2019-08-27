<?php

namespace App\Modules\Admin\Access\Models;

use App\Modules\Base\City\CityModule;
use App\Modules\Admin\Access\CpAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DmSellerOriginationChart extends Model
{
    protected $table = 'dm_seller_origination_chart';
    public $timestamps = false;

    public static function getMonthOriginationRecord($month, $argument = [], $isCityControl = false)
    {
        if(strtotime($month) < strtotime('2019-03-01')) {
            $month = '2019-03-01';
        }
    	$startTime = date('Y-m-01', strtotime($month));
    	$endTime   = date('Y-m-t', strtotime($month));
    	$contructor = self::where('date', '>=', $startTime)
    	->where('date', '<=', $endTime)
        ->orderBy('date', 'asc');
        if($argument['group']) {
            $contructor->where('group_id', $argument['group']);
        }
        if($argument['depart']) {
            $contructor->where('depart_id', $argument['depart']);
        }
        if($argument['user']) {
            $contructor->where('seller_id', $argument['user']);
        }

        if($isCityControl == true){
            $departIds = CpAccess::getDepartByResource('cityList', CityModule::getCurrentCity());
            if(empty($departIds['data'])){
                return [];
            }
            $contructor->whereIn('group_id', $departIds['data']);
        }

    	$recordList = $contructor->get()
    	->toArray();
    	return $recordList;
    }

    /**
    * 获取历史组织架构（不排除离职人员）
    * @param [array] dimisstion 是否包括离职人员
    */
    public static function getSellerOriginationRecord($argument =[]) 
    {
        $where = ' 0 = 0';
        if($argument['dimisstion']) {
            $sellerIdList = self::// where('date', date('Y-m-d', time() - 3600 * 24))
                                where('date', '2019-03-19')
                                ->get()
                                ->pluck('seller_id')
                                ->toArray();
            $where .= " and seller_id in (" . implode(",", $sellerIdList) . ")";
        }
        $sql = "SELECT MIN(seller_id) seller_id, min(group_id) group_id, min(depart_id) depart_id
                from dm_seller_origination_chart
                where" . $where . " 
                group by seller_id, group_id, depart_id";
        $sellerList = DB::select($sql);
        $sellerList = json_decode(json_encode($sellerList), true);
        
        return $sellerList;
    }

    public static function getSellerByDate($startDate,$endDate){
        if($startDate == '' || $endDate == ''){
            return [];
        }
        if(strtotime($startDate) < strtotime('2019-03-01')) {
            $startDate = '2019-03-01';
        }
        if(strtotime($endDate) < strtotime('2019-03-01')) {
            $endDate = '2019-03-01';
        }
        $contructor = self::where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->orderBy('date', 'asc');
        $recordList = $contructor->get()->toArray();

        $result = [];
        if(!empty($recordList)){
            foreach ($recordList as $record){
                $result[$record['date'].'_'.$record['seller_id']] = $record;
            }
        }

        return $result;
    }
}
