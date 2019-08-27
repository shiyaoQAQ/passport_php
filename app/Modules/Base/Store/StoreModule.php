<?php 

namespace App\Modules\Base\Store;

use App\Modules\Admin\Access\CpAccess;
use \YC_Geo;
use App\Modules\Base\City\CityModule;

/**
* 仓库模块
*/
class StoreModule
{
    const DEFAULT_STORE = 1;

    const STORE_BJ_LGY = 1; //北库
    const STORE_BJ_NK  = 4; //南库
    const STORE_BJ_SAND = 7;

    const STORE_BJ_LGY_DEFECTIVE = 2;
    const STORE_BJ_LGY_TEST = 3;
    const STORE_BJ_NK_DEFECTIVE = 5;
    const STORE_BJ_NK_TEST = 6;
    const STORE_BJ_SHIP = 8;
    const STORE_BJ_NK_SAND_STOCK = 9;
    const STORE_BJ_LGY_STOCK = 11;
    const STORE_BJ_NK_STOCK = 12;
    const STORE_BJ_THIRD = 14;
    const STORE_BJ_REFUSH_SERVICE = 15;
    const STORE_BJ_NEW_BK_PRODUCT = 16;

    const STORE_CD_BY = 51;
    const STORE_CD_THIRD = 52;


    //供应商直发仓库
    const STORE_BJ_ZF = 10;
    
    //全部仓库
    public static $allStoreMap = [
       self::STORE_BJ_LGY => '来广营仓库',
       self::STORE_BJ_LGY_DEFECTIVE => '来广营次品库',
       self::STORE_BJ_LGY_TEST => '测试仓北！！！',
       self::STORE_BJ_NK => '南库',
       self::STORE_BJ_NK_DEFECTIVE => '南库次品仓',
       self::STORE_BJ_NK_TEST => '测试仓南！！！',
       self::STORE_BJ_SAND => '砂石水泥联营库',
       self::STORE_BJ_SHIP => '运费库',
       self::STORE_BJ_NK_SAND_STOCK => '南库砂石封存库',
       self::STORE_BJ_ZF => '直发仓库',
       self::STORE_BJ_LGY_STOCK => '北库备货库',
       self::STORE_BJ_NK_STOCK => '南库备货库',
       self::STORE_BJ_THIRD => '第三方云仓',
       self::STORE_TJ_JOIN => '加盟仓库',
       self::STORE_BJ_REFUSH_SERVICE => '刷新服务专用仓',
       self::STORE_BJ_NEW_BK_PRODUCT => '新北库备用 产品仓 ',
       self::STORE_CD_BY => '八益仓库',
       self::STORE_CD_THIRD => '成都联营仓库',
    ];

    public static $orderStoreList = [
        self::STORE_BJ_LGY , //北库
        self::STORE_BJ_NK  , //南库
        self::STORE_BJ_SAND ,
        self::STORE_BJ_ZF ,
        self::STORE_BJ_THIRD ,
        self::STORE_TJ_JOIN ,
        self::STORE_CD_BY,
        self::STORE_CD_THIRD,
    ];

    // 天津仓库
    const STORE_TJ_JOIN = 13;

    public static $selfStoreList = [self::STORE_BJ_LGY, self::STORE_BJ_NK, self::STORE_CD_BY];

    public static $mallStoreList = [
        CityModule::ADCODE_BJ => [
            self::STORE_BJ_LGY,
            self::STORE_BJ_NK
        ],
        CityModule::ADCODE_TJ => [
            self::STORE_TJ_JOIN
        ],
        CityModule::ADCODE_CD => [
           self::STORE_CD_BY
        ]
    ];

    public static $showStoreList = [
        self::STORE_BJ_LGY => [
            'id' => 1,
            'store_name' => '来广营仓库',
            'location' => '116.463305,40.056618',
            'address' => '北京市朝阳区顺黄路41号附近居然装饰物流配送中心'
        ],
        self::STORE_BJ_NK => [
            'id' => 4,
            'store_name' => '南库',
            'location' => '116.350818,39.821783',
            'address' => '北京市丰台区南苑西路掌上辅材城南配送中心'
        ],
        self::STORE_BJ_SAND => [
            'id' => 7,
            'store_name' => '沙石水泥联运仓库',
            'location' => '116.316047,39.790790',
            'address' => '暂无详细地址，请联系客服'
        ],
        self::STORE_BJ_ZF => [
            'id'         => self::STORE_BJ_ZF,
            'store_name' => '直发仓库',
            'location'   => '117.156475,36.667861',
            'address' => '暂无详细地址，请联系客服'
        ],
        self::STORE_TJ_JOIN => [
            'id'         => self::STORE_TJ_JOIN,
            'store_name' => '加盟仓库',
            'location'   => '117.245369,39.03212',
            'address' => '天津市西青区津兴道世纪宇航物流有限公司'
        ],
        self::STORE_BJ_THIRD =>[
            'id'    => self::STORE_BJ_THIRD,
            'store_name' => '第三方云仓',
            'location'   => '116.607159,35.346488',//地址目前济宁，冲突再改
            'address' => '暂无详细地址，请联系客服'
        ],
        self::STORE_CD_BY => [
            'id'  => self::STORE_CD_BY,
            'store_name' => '八益仓库',
            'location'   => '104.005165,30.600213',
            'address'    => '成都市武侯区华兴街道簇桥上街1号沈家桥5组449号',
        ],
        self::STORE_CD_THIRD => [
            'id' => self::STORE_CD_THIRD,
            'store_name' => '成都联营仓库',
            'location' => '30.5728790,104.066145',
            'address' => '暂无详细地址，请联系客服'
        ],
    ];

    //测试用
    // public static $showStoreList = [
    //     3 => [
    //         'id' => 3,
    //         'store_name' => '测试仓北！！！',
    //         'location' => '116.463305,40.056618',
    //     ],
    //     6 => [
    //         'id' => 6,
    //         'store_name' => '测试仓南！！！',
    //         'location' => '116.350818,39.821783',
    //     ],
    // ];

    public static $cityStoreList = [
        CityModule::ADCODE_BJ => [
            self::STORE_BJ_LGY => [
                'id' => 1,
                'store_name' => '来广营仓库',
                'access' => 'store-bj-lgy',
            ],
            self::STORE_BJ_NK => [
                'id' => 4,
                'store_name' => '南库',
                'access' => 'store-bj-nk',
            ],
            self::STORE_BJ_SAND => [
                'id' => 7,
                'store_name' => '砂石水泥联运仓库',
                'access' => 'store-bj-sand',
            ],
            self::STORE_BJ_ZF => [
                'id'         => self::STORE_BJ_ZF,
                'store_name' => '直发仓库',
                'access' => 'store-bj-zf',
            ],
            self::STORE_BJ_THIRD => [
                'id'         => self::STORE_BJ_THIRD,
                'store_name' => '第三方云仓',
                'access' => 'store-bj-third',
            ],
            self::STORE_BJ_LGY_STOCK => [
                'id' => self::STORE_BJ_LGY_STOCK,
                'store_name' => '北库备货库',
                'access' => 'store-bj-lgy-stock',
            ],
            self::STORE_BJ_NK_STOCK => [
                'id' => self::STORE_BJ_NK_STOCK,
                'store_name' => '南库备货库',
                'access' => 'store-bj-nk-stock',
            ],
            self::STORE_BJ_NK_DEFECTIVE => [
                'id' => self::STORE_BJ_NK_DEFECTIVE,
                'store_name' => '南库次品仓',
                'access' => 'store-bj-nk-def',
            ],
            self::STORE_BJ_LGY_DEFECTIVE => [
                'id' => self::STORE_BJ_LGY_DEFECTIVE,
                'store_name' => '北库次品仓', 
                'access' => 'store-bj-lgy-def',         
            ],
            self::STORE_BJ_NK_SAND_STOCK => [
                'id' => self::STORE_BJ_NK_SAND_STOCK,
                'store_name' => '南库砂石封存库',
                'access' => 'store-bj-nk-sand-stock',
            ],
            self::STORE_BJ_NEW_BK_PRODUCT => [
                'id' => self::STORE_BJ_NEW_BK_PRODUCT,
                'store_name' => '新北库备用 产品仓',
                'access' => 'store-bj-new-bk-product',
            ],
        ],
        CityModule::ADCODE_TJ => [
            self::STORE_TJ_JOIN => [
                'id'         => self::STORE_TJ_JOIN,
                'store_name' => '加盟仓库',
                'access' => 'store-tj-join',
            ],
        ],
        CityModule::ADCODE_CD => [
            self::STORE_CD_BY => [
                'id'         => self::STORE_CD_BY,
                'store_name' => '八益仓库',
                'access' => 'store-cd-by',
            ],
            self::STORE_CD_THIRD => [
                'id'         => self::STORE_CD_THIRD,
                'store_name' => '成都联营仓库',
                'access' => 'store-cd-third',
            ],
        ],
    ];

    public static $storeCityMap = [
        self::STORE_BJ_LGY => CityModule::ADCODE_BJ,
        self::STORE_BJ_NK => CityModule::ADCODE_BJ,
        self::STORE_BJ_SAND => CityModule::ADCODE_BJ,
        self::STORE_BJ_ZF => CityModule::ADCODE_BJ,
        self::STORE_BJ_THIRD => CityModule::ADCODE_BJ,
        self::STORE_TJ_JOIN => CityModule::ADCODE_TJ,
        self::STORE_CD_BY => CityModule::ADCODE_CD,
        self::STORE_CD_THIRD => CityModule::ADCODE_CD,
    ];

    public static $storeList = [
        1 => [
            'id' => 1,
            'store_name' => '来广营仓库',
            'location' => '116.463305,40.056618',
        ],
        2 => [
            'id' => 2,
            'store_name' => '来广营次品仓',
            'location' => '116.463305,40.056618',
        ],
        3 => [
            'id' => 3,
            'store_name' => '测试仓北！！！',
            'location' => '116.463305,40.056618',
        ],
        4 => [
            'id' => 4,
            'store_name' => '南库',
            'location' => '116.350818,39.821783',
        ],
        5 => [
            'id' => 5,
            'store_name' => '南库次品仓',
            'location' => '116.463305,40.056618',
        ],
        6 => [
            'id' => 6,
            'store_name' => '测试仓南！！！',
            'location' => '116.350818,39.821783',
        ],
        7 => [
            'id' => 7,
            'store_name' => '沙石水泥联运仓库',
            'location' => '116.316047,39.790790',
        ],
        self::STORE_BJ_ZF => [
            'id'         => self::STORE_BJ_ZF,
            'store_name' => '直发仓库',
            'location'   => '117.156475,36.667861',
        ],
        self::STORE_TJ_JOIN => [
            'id'         => self::STORE_TJ_JOIN,
            'store_name' => '加盟仓库',
            'location'   => '117.245369,39.03212',
        ],
        self::STORE_BJ_THIRD => [
            'id'         => self::STORE_BJ_THIRD,
            'store_name' => '第三方云仓',
            'location'   => '116.607159,35.346488',//地址目前济宁，冲突再改
        ],
        self::STORE_CD_BY => [
            'id'         => self::STORE_CD_BY,
            'store_name' => '八益仓库',
            'location'   => '104.005165,30.600213',
        ],    
        self::STORE_CD_THIRD => [
            'id'         => self::STORE_CD_THIRD,
            'store_name' => '成都联营仓库',
            'location'   => '30.5728790,104.066145',// 成都市政府
        ],        
    ];

    //资源和仓库的映射关系
    public static $resourceToStore = [
        'store-bj-lgy' => self::STORE_BJ_LGY,
        'store-bj-nk'  => self::STORE_BJ_NK,
        'store-bj-sand' => self::STORE_BJ_SAND,
        'store-bj-zf'    => self::STORE_BJ_ZF,
        'store-bj-third'    => self::STORE_BJ_THIRD,
        'store-tj-join' => self::STORE_TJ_JOIN,
        'store-bj-lgy-def' => self::STORE_BJ_LGY_DEFECTIVE,
        'store-bj-nk-def' => self::STORE_BJ_NK_DEFECTIVE,
        'store-bj-nk-sand-stock' => self::STORE_BJ_NK_SAND_STOCK,
        'store-bj-lgy-stock' => self::STORE_BJ_LGY_STOCK,
        'store-bj-nk-stock' => self::STORE_BJ_NK_STOCK,
        'store-bj-new-bk-product' => self::STORE_BJ_NEW_BK_PRODUCT,
        'store-cd-by' => self::STORE_CD_BY,
        'store-cd-third' => self::STORE_CD_THIRD,
    ];
    //库区定义
    const LOCATION_AREA_ALL = 0;
    const LOCATION_AREA_FIVE = 1;
    const LOCATION_AREA_EIGHT = 2;
    const LOCATION_AREA_A = 3;
    const LOCATION_AREA_B = 4;
    const LOCATION_AREA_C = 5;
    const LOCATION_AREA_PURCHASE = 6;
    public static $locationAreaMap = [
        //全部
        0 => [
            self::LOCATION_AREA_ALL => '全部',
            self::LOCATION_AREA_FIVE => '5号',
            self::LOCATION_AREA_EIGHT => '8号',
            self::LOCATION_AREA_A => 'A',
            self::LOCATION_AREA_B => 'B',
            self::LOCATION_AREA_C => 'C',
            self::LOCATION_AREA_PURCHASE => '临采',           
        ],
        self::STORE_BJ_LGY => [
            self::LOCATION_AREA_A => 'A',
            self::LOCATION_AREA_B => 'B',
            self::LOCATION_AREA_C => 'C',
            self::LOCATION_AREA_PURCHASE => '临采',
        ],
        self::STORE_BJ_NK => [
            self::LOCATION_AREA_A => 'A',
            self::LOCATION_AREA_B => 'B',
            self::LOCATION_AREA_C => 'C',
            self::LOCATION_AREA_PURCHASE => '临采',
        ],

    ];
    /**
     * 获取k-v形式的仓库列表
     */
    public static function getSimpleStoreList()
    {
        $storeList = self::$showStoreList;
        $result = [];
        foreach ($storeList as $store) {
            $result[$store['id']] = $store['store_name'];
        }
        $storeAccess = CpAccess::getAccessDetail(CpAccess::ACCESS_KEY_EXPRESS, StoreModule::$resourceToStore);
        if (!in_array(self::STORE_BJ_ZF, $storeAccess)) {
            unset($result[self::STORE_BJ_ZF]);
        }
        return $result;
    }

    /**
     * 获取仓库地址 新 location
     */
    public static function getStoreDistance($location, $cityId = CityModule::ADCODE_BJ)
    {
        $showStoreList = self::getCityStoreList($cityId) ?: [];
        $storeAccess = CpAccess::getAccessDetail(CpAccess::ACCESS_KEY_EXPRESS, StoreModule::$resourceToStore);
        if (!in_array(self::STORE_BJ_ZF, $storeAccess)) {
            unset($showStoreList[self::STORE_BJ_ZF]);
        }
        //仓库位置
        $storeGeos = [];
        foreach ($showStoreList as $store) {
            $storeGeos[] = self::getStoreLocation($store['id']);
        }
        //这里改成获取地点到仓库的最短距离
        foreach ($showStoreList as &$store) {
            $storeLocation = self::getStoreLocation($store['id']);
            $result = YC_Geo::getDriveDistance($storeLocation, $location);
            if (isset($result['status']) && $result['status']==1 && isset($result['route']['paths'][0]['distance'])) {
                $distance = $result['route']['paths'][0]['distance'];
            } else {
                $distance = -1;
            }
            $store['distance'] = floatval($distance);
            unset($store);
        }
        // $result = YC_Geo::getPointsDistance($storeGeos, $location);
        // if (array_get($distanceResult, 'status') == 0) {
        //     $index = 0;
        //     foreach ($showStoreList as &$store) {
        //         $store['distance'] = array_get($result, 'results.'.$index++.'.distance', 0) / 1000;
        //     }
        //     unset($store);
        // }
        return $showStoreList;
    }

    public static function getStoreName($storeId)
    {
        return array_get(self::$storeList, $storeId . '.' . 'store_name', '');
    }

    public static function getStoreLocation($storeId)
    {
        return array_get(self::$storeList, $storeId . '.' . 'location', '');
    }

    /**
     * 根据城市转换城市的仓库（正常列表页调用）
     *
     * @param [type] $cityId
     * @return array
     */
    public static function getCityStoreList($cityId)
    {
        if ($cityId == 'all') {
            $storeInfo = self::$cityStoreList;
            $storeList = [];
            foreach ($storeInfo as $skey => $store) {
                foreach ($store as $stores) {
                    $storeList[] = $stores;
                }
            }
            foreach ($storeList as $storeId => $arr) {
                if(!in_array($arr['id'], self::$orderStoreList)) {
                    unset($storeList[$storeId]);
                }
            }
        }else{
            $storeList = array_get(self::$cityStoreList, $cityId ?: 0, []);
            foreach ($storeList as $storeId => $arr) {
                if(!in_array($storeId, self::$orderStoreList)) {
                    unset($storeList[$storeId]);
                }
            }
        }
        return $storeList;
    }

    /**
    * 根据城市id获取到相应城市所有的仓库资源与仓库id的对应
    */
    public static function getAllCityStoreIdList($cityId)
    {
        if ($cityId == 'all') {
            $storeInfo = self::$cityStoreList;
            $storeList = [];
            foreach ($storeInfo as $skey => $store) {
                foreach ($store as $stores) {
                    $storeList[] = $stores;
                }
            }
        }else{
            $storeList = array_get(self::$cityStoreList, $cityId ?: 0, []);
        }
        $storeResourceId = array_column($storeList, 'id' ,'access');
        return $storeResourceId;
    }

    /**
     * 根据城市列表转换仓库列表
     *
     * @param [type] $cityIds
     * @return [array] $result
     */
    public static function getCityListStoreList($cityIds)
    {
        $result = [];
        foreach ($cityIds as $cityId) {
            $result = array_merge($result, array_keys(self::getCityStoreList($cityId)));
        }
        return $result;
    }

    /**
     * 获取CP用户当前的仓库
     * 固定返回索引数组
     *
     * @return [array]
     */
    public static function getCurrentStore()
    {
        $list = CpAccess::getAccessDetail(CpAccess::ACCESS_KEY_EXPRESS, StoreModule::$resourceToStore);
        
        return $list; 
    }

    /**
     * 获取CP用户当前的仓库映射
     * 固定返回 code=>desc 的关联数组
     *
     * @return void
     */
    public static function getCurrentStoreMap()
    {
        return self::getStoreMap(self::getCurrentStore());
    }

    /**
     * 将城市列表转化成城市映射
     *
     * @param [type] $cityList
     * @return void
     */
    public static function getStoreMap($storeList)
    {
        if (!is_array($storeList)) {
            return [];
        }
        $result = [];
        foreach ($storeList as $storeId) {
            $result[$storeId] = self::getStoreName($storeId);
        }
        return $result;
    }

    public static function getStoreCity($storeId)
    {
        return array_get(self::$storeCityMap, $storeId ?: 0, 0);
    }

}
