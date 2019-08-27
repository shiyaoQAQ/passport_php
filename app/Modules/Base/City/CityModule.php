<?php

namespace App\Modules\Base\City;

use App\Modules\Admin\Access\CpAccess;
use App\Modules\Admin\Access\UserModule;

class CityModule
{
    const ADCODE_BJ = 110100;
    const ADCODE_TJ = 120100;
    const ADCODE_CD = 510100;

    const SYS_ADCODE_BJ = 52;
    const SYS_ADCODE_TJ = 343;
    const SYS_ADCODE_CD = 322;

    public static $cityCodeMap = [
        self::ADCODE_BJ,
        self::ADCODE_TJ,
        self::ADCODE_CD,
    ];
    public static $cityMap = [
        self::ADCODE_BJ => '北京',
        self::ADCODE_TJ => '天津',
        self::ADCODE_CD => '成都',
    ];

    public static $adCodeMapping = [
        self::ADCODE_BJ => self::SYS_ADCODE_BJ,
        self::ADCODE_TJ => self::SYS_ADCODE_TJ,
        self::ADCODE_CD => self::SYS_ADCODE_CD,
    ];

    //城市地图相关初始化经纬度
    public static $cityLocationMap = [
        self::ADCODE_BJ => [[
            [116.355042,39.948463],
            [116.433878,39.948949],
            [116.435766,39.900518],
            [116.35723,39.899662]
        ]],
        self::ADCODE_TJ => [[
            [117.15000,39.13122],
            [117.18000,39.15222],
            [117.20000,39.13333],
            [117.16000,39.12333],
        ]],
        self::ADCODE_CD => [[
            [104.041915,30.673962],
            [104.113779,30.668495],
            [104.123553,30.630716],
            [104.00742,30.629224],
        ]
    ]];
    //城市地图中心点经纬度
    public static $centerLocation = [
        self::ADCODE_BJ => [116.397428, 39.90923],
        self::ADCODE_TJ => [117.18000,39.15222],
        self::ADCODE_CD => [104.07756,30.653584],
    ];

    /**
     * 获取CP用户当前的城市
     * 固定返回索引数组
     *
     * @return [array]
     */
    public static function getCurrentCity()
    {
        $list = CpAccess::getAccessDetail(CpAccess::ACCESS_KEY_CITY);
        return $list; 
    }

    /**
    * 获取CP用户城市资源列表
    * mark 一般别用这个方法，小程序无法手动选择当前资源专用
    */
    public static function getUserCityAccess()
    {
        $list = CpAccess::getAccessList(CpAccess::ACCESS_KEY_CITY);

        return $list;
    }

    /**
     * 获取城市权限sql
     *
     * @param boolean $transStore 是否将城市权限列表转换成对应仓库列表
     * @return void
     */
    public static function getCurrentCitySql($transStore = false)
    {
        // 这里由于storeModule的原因 先注释掉
        // $list = self::getCurrentCity();
        // if (!$transStore) {
        //     if (empty($list)) {
        //         return " IS NULL ";
        //     }
        //     return " IN (" . implode(',', $list) . ") ";
        // } else {
        //     $storeList = StoreModule::getCityListStoreList($list);
        //     if (empty($storeList)) {
        //         return " IS NULL ";
        //     }
        //     return " IN (" . implode(',', $storeList) . ") ";
        // }
    }

    public static function checkNowCityBeijing()
    {
        $list = self::getCurrentCity();
        return in_array(self::ADCODE_BJ, $list);
    }

    /**
     * 获取CP用户当前的城市映射
     * 固定返回 code=>desc 的关联数组
     *
     * @return void
     */
    public static function getCurrentCityMap()
    {
        return self::getCityMap(self::getCurrentCity());
    }

    /**
     * 获取城市名称
     *
     * @param [type] $cityId
     * @return void
     */
    public static function getCityDesc($cityId)
    {
        return (string)(array_get(self::$cityMap, $cityId ?: 0, ''));
    }

    /**
     * 将城市列表转化成城市映射
     *
     * @param [type] $cityList
     * @return void
     */
    public static function getCityMap($cityList)
    {
        if (!is_array($cityList)) {
            return [];
        }
        $result = [];
        foreach ($cityList as $code) {
            $result[$code] = self::getCityDesc($code);
        }
        return $result;
    }

    /**
     * 获取城市销售组
     * 
     *@param 是否显示下级，是否显示离职欠款，城市列表
     * @return [array]
     */
    public static function getCitySalerMap($isShowAll = false, $isDimission = true,array $cityList = [], $isAll = false)
    {
        $adminId = CpAccess::theUid();
        if ($isShowAll) {
            if($isDimission) {
                if($isAll == true){
                    $salerList = CpAccess::getUserByMark(CpAccess::$saleAll);
                }else{
                    $salerList = CpAccess::getUserByMark(CpAccess::$saleWithDissionMark);
                }
            }else {
                $salerList = CpAccess::getUserByMark(CpAccess::$saleMark);
            }
        } else {
            $salerList = CpAccess::getUserChildUser($adminId, CpAccess::$saleWithDissionMark);
        }
        $uidList[]  = $adminId;
        if ($salerList['code'] == 0 && !empty($salerList['data'])) {
            $uidList = array_merge(array_keys($salerList['data']), $uidList);
        }
        if(empty($uidList) || !is_array($uidList)) {
            return [];
        }
        if(empty($cityList)) {
            $cityList = self::getCurrentCity();
        }
        $otherResourceRes =  CpAccess::getUserByResource('cityList', $cityList);
        if($otherResourceRes['code'] == 0 && !empty($otherResourceRes['data'])) {
            $uidList =  array_intersect($uidList, $otherResourceRes['data']);
        }
        $list = UserModule::getUserNameMap($uidList);
        return $list;
    }
    /**
     * 返回当前城市id，全部为all
     */
    static function getNowCity($default = false)
    {   
        // 需要有默认城市是选择北京
        $city = CpAccess::getAccessVal('city');
        if ($city == 'all' && $default) {
            $list = self::getCurrentCity();
            if (count($list) >= 2 ) {
                $city = self::ADCODE_BJ;
            } else {
                $city = $list[0];
            }
        }
        // 防止脚本调用
        if (empty($city)) {
            $city = self::ADCODE_BJ;
        }
        return $city;
    }

    /**
     * [getAdcodeByLocation 通过坐标获取城市]
     * @param  [type] $location [坐标]
     * @return [type]           [code ,desc]
     */
    public static function getAdcodeByLocation($location)
    {
        if(!is_string($location) || count(explode(',', $location)) != 2) {
            throw new Exception("传入坐标格式不正确", 10000100);
        }
        $regeo = \YC_Geo::regeo($location);
        if(empty($regeo['adcode'])) {
            throw new Exception("获取城市失败", 10000101);
        }
        $prefixNum = substr($regeo['adcode'], 0, 3);
        $result = [];
        foreach (self::$cityCodeMap as $adcode) {
            $ad = substr($adcode, 0, 3);
            if($prefixNum == $ad) {
                $result = [ 'adcode' => $adcode , 'desc' => self::getCityDesc($adcode)];
                break;
            }
        }
        if(empty($result)) {
            // throw new Exception("不存在的城市信息", 10000501);
            $result = [ 'adcode' => self::ADCODE_BJ , 'desc' => self::getCityDesc(self::ADCODE_BJ)];
        }

        return $result;

    }

    /**
    * 通过当前城市获取当前城市的地图经纬度区域
    */
    public static function getNowCityMap()
    {
        $cityId = self::getNowCity();
        if ($cityId == 'all') {
            $cityId = self::ADCODE_BJ;
        }
        $cityMap = [
            'cityLocationMap' => self::$cityLocationMap[$cityId],
            'centerLocation' => self::$centerLocation[$cityId],
        ];
        return $cityMap;
    }
}
