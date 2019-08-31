<?php

namespace App\Modules\Admin\Access\Constants;

use App\Modules\Base\City\CityModule;

class AccessConst
{
    const MARK_SALELEADER = 'salesleader';
    const MARK_SALE        = 'seller';
    const MARK_SALE_LEADER = 'seller_leader';
    const MARK_SALE_DIMISSION = 'dimission';
    const MARK_SALE_CLOUD = 'seller_cloud';

    const MARK_SUPER_ADMIN = 'super_admin';
    const MARK_SERVICER= 'servicer';
    const MARK_CSLEADER= 'csleader';
    const MARK_SALE_ASSISTANT = 'salesassistant'; // 销售助理
    // 物流
    const MARK_DRIVER = 'driver';
    const MARK_PORTER = 'porter';
    // 采购
    const MARK_TEMP_PURCHASE = 'temp_purchase';
    const MARK_CENTRAL_PURCHASE = 'central_purchasing';

    //仓库资源
    const STORE_RESOURCE_LGY = 'store-bj-lgy';
    const STORE_RESOURCE_NK = 'store-bj-nk';
    const STORE_RESOURCE_CD_BY = 'store-cd-by';

    public static $storeReasorceMap = array(self::STORE_RESOURCE_LGY, self::STORE_RESOURCE_NK, self::STORE_RESOURCE_CD_BY);
    //仓库PDA资源
    const STORE_RESOURCE_LGY_PDA = 'store-bj-lgy-pda';
    const STORE_RESOURCE_NK_PDA = 'store-bj-nk-pda';
    const STORE_RESOURCE_BY_PDA = 'store-cd-by-pda';

    public static $storePdaReasorceMap = array(1=>array(self::STORE_RESOURCE_LGY_PDA), 4=>array(self::STORE_RESOURCE_NK_PDA),51=>array(self::STORE_RESOURCE_BY_PDA));

    //返利支付状态修改权限用户
    const REBATE_PAY_STATUS_USER = 21923;

    //临采企业微信消息通知
    const DISPATCHER_NK = 'dispatcher_nk';
    const DISPATCHER_BK = 'dispatcher_bk';

    public static $saleMark  = array(self::MARK_SALE, self::MARK_SALE_LEADER);
    public static $salesMark = array(self::MARK_SALELEADER, self::MARK_SALE_LEADER, self::MARK_SALE);
    public static $cloudSaleMark = array(self::MARK_SALE_CLOUD);
    public static $saleWithDissionMark  = array(self::MARK_SALE, self::MARK_SALE_LEADER,self::MARK_SALE_DIMISSION);
    public static $servicerMark  = array(self::MARK_SERVICER, self::MARK_CSLEADER);
    public static $saleDimissionMark = array(self::MARK_SALE_DIMISSION);
    public static $tempPurchaseMark = array(self::MARK_TEMP_PURCHASE);
    public static $saleAll = [self::MARK_SALELEADER,self::MARK_SALE,self::MARK_SALE_LEADER,self::MARK_SALE_DIMISSION,self::MARK_SALE_CLOUD];

    public static $wechatNk = array(self::DISPATCHER_NK);
    public static $wechatBk = array(self::DISPATCHER_BK);
    public static $wechatAll = array(self::DISPATCHER_NK,self::DISPATCHER_BK);

    public static $resourceList = array(
            'test'=>array(
                    'desc'     => '测试资源',
                    'resource' => array(
                            'order_list-search_city'         => '订单列表查找-城市',
                         ),
            ),
            'order' => array(
                'desc' => '订单系统',
                'resource' => array(
                    'show-all-order' => '查看全部订单',
                    'opf-wholesale-order' => '1.5批可查看',
                    'can-dblink-price' => '不可自定义价格',
                    'opf-wholesale-edit' => '可修改1.5批商户',
                ),
            ),
            'refund'=>array(
                'desc'     => '退换货审核',
                'resource' => array(
                    'refund-servicer-check'   => '客服初审',
                    'refund-servicer-recheck' => '客服复核',
                    'refund-express-check'    => '物流拉货',
                    'refund-store-check'      => '仓库审核',
                    'refund-finance-check'    => '财务审核',
                    'refund-final-check'      => '关单确认',
                ),
            ),
            'workorder' => array(
                'desc' => '工单系统',
                'resource' => array(
                    'workorder-admin' => '工单管理员',
                    'workorder-servicer-dispose' => '客服处理',
                    'workorder-aftersale-dispose' => '售后处理',
                    'purchase-workorder-dispose' => '临采工单处理',
                ),
            ),
            'saleCustomer' => array(
                'desc'  =>  '销售客户管理',
                'resource'  =>  array(
                    'allocationPubCustomer'    => '分配公共海域客户',
                    'findAllocationUserDetail' => '查找重分配查看客户详情',
                    'allocationPriSeaCustomer' => '分配任意私海数据',
                ),
            ),
            'cpUserRole' => array(
                'desc' => '用户身份资源',
                'resource' => array(
                    'sale-baixu-group'   => '销售一部（白旭）',
                    'sale-caoqian-group' => '销售二部（云济）',
                    'sale-ka-group'      => '销售三部（树强）',
                    'sale-group-four'    => '销售四部（志杰）',
                    'sale-tj-group'      => '天津加盟销售',
                    'sale-cd-group'      => '成都销售',
                ),
            ),
            'express' => array(
                'desc' => '物流',
                'resource' => array(
                    'order-sort-print-button' => '订单详情显示拣货单按钮',
                ),
            ),
            'expressStore' => array(
                'desc' => '物流仓库',
                'resource' => array(
                    'store-bj-lgy' => '来广营仓库',
                    'store-bj-nk'  => '南库',
                    'store-bj-sand'  => '砂石水泥联运仓库',
                    'store-bj-zf'    => '直发仓库',
                    'store-tj-join'  => '加盟仓库',
                    'store-bj-third'  => '第三方云仓',
                    'store-bj-lgy-def' => '来广营次品库',
                    'store-bj-nk-def' => '南库次品仓',
                    'store-bj-nk-sand-stock' => '南库砂石封存库',
                    'store-bj-lgy-stock' => '北库备货库',
                    'store-bj-nk-stock' => '南库备货库',
                    'store-bj-new-bk-product' => '新北库备用 产品仓',
                    'store-cd-by' => '八益仓库',
                    'store-cd-third' => '成都联营仓库',
                ),
            ),
            'orderFlow' => array(
                'desc' => '订单流转工具',
                'resource' => array(
                    'show-all-order' => '查看全部订单',
                    'step-create-order' => '创建订单节点',
                    'step-confirm-order' => '确认订单节点',
                    'step-print-order' => '仓库打单节点',
                    'step-shipped-order' => '装车完成节点',
                    'step-complete-order' => '客户签收节点',
                    'step-pay-order' => '客户付款节点',
                ),
            ),
            'compCustomerAccount' => array(
                'desc' => '企业客户对账',
                'resource' => array(
                    'show-compcustomer-order' => '查看企业客户报表',
                ),
            ),
            'createGoods' => array(
                'desc' => '商品',
                'resource' => array(
                    'create-pur-info' => '创建采购信息',
                    'create-store-info' => '创建仓库信息',
                    'create-dispatch-info' => '创建调度信息',
                    'update-goods-base-info' => '更新基础信息',
                    'show-goods-supplier-info' => '展示商品详情页的供应商信息',
                ),
            ),
            'supplier' => array(
                'desc' => '供应商',
                'resource' => array(
                    'show-all-supplier' => '显示商品全部供应商',
                    'show_supplier_config' => '查看供应商返点、商品信息',
                ),
            ),
            'credentialCreate' => array(
                'desc' => '生成荣誉证书',
                'resource' => array(
                    'create-topHundred' => '百强工长',
                    'create-strategicPartner' => '战略合作伙伴',
                    'create-highQuality' => '品质工长',
                ),
            ),
            'cityList' => [
                'desc'     => '城市',
                'resource' => [
                    CityModule::ADCODE_BJ => '北京',
                    CityModule::ADCODE_TJ => '天津',
                    CityModule::ADCODE_CD => '成都',
                ],
            ],
            'storeSort' => [
                'desc' => '仓配资源',
                'resource' => [
                   'store-bj-lgy-pda' => '北库PDA',
                   'store-bj-nk-pda' => '南库PDA',
                   'store-cd-by-pda' => '八益仓库PDA',
                ],
            ],
    );

    const ACCESS_VAL_ALL = 'all';

    const ACCESS_KEY_EXPRESS = 'express';
    const ACCESS_KEY_CITY    = 'city';

    //在菜单上提供
    public static $allAccessPath = array(
        self::ACCESS_KEY_EXPRESS => array(
            'desc'     => '仓库',
            'resource' => 'expressStore',
            'parent_access' => self::ACCESS_KEY_CITY,
            'controllers' => array(
                'App\Http\Controllers\Cp\Express',
                'App\Http\Controllers\Cp\ExpressBase',
                'App\Http\Controllers\Cp\ExpressBill',
                'App\Http\Controllers\Cp\TempPurchase',
                'App\Http\Controllers\Cp\ExpressDispatch',
                'App\Http\Controllers\Cp\OrderController',
                'App\Http\Controllers\Cp\OrderRefund',
                'App\Http\Controllers\Cp',
                'App\Http\Controllers\Cp\InstoreController',
                'App\Http\Controllers\Cp\OutstoreController',
                'App\Http\Controllers\Cp\StockController',
                'App\Http\Controllers\Cp\InoutQueryController',
                'App\Http\Controllers\Cp\OverSalesController',
                'App\Http\Controllers\Cp\ChargeController',
                'App\Http\Controllers\Cp\PurchaseStoreController',
                'App\Http\Controllers\Cp\PurchaseRefundController',
                'App\Http\Controllers\Cp\CheckController',
                'App\Http\Controllers\Cp\GainLossController',
            ),
            'rules' => [
                'App\Http\Controllers\Cp\OrderRefund' => [
                    'check_key' => ['refundid'],
                    'callback'  => [
                        'class'    => 'App\Modules\Order\RefundModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'store_id',
                    ], 
                ],
                'App\Http\Controllers\Cp\InstoreController' => [
                    'check_key' => ['id'],
                    'callback'  => [
                        'class'    => 'App\Modules\Store\InstoreModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'store_id',
                    ], 
                ],
                'App\Http\Controllers\Cp\OutstoreController' => [
                    'check_key' => ['id'],
                    'callback'  => [
                        'class'    => 'App\Modules\Store\OutstoreModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'store_id',
                    ], 
                ],
                'App\Http\Controllers\Cp\InoutQueryController' => [
                    'check_key' => ['id'],
                    'callback'  => [
                        'class'    => 'App\Modules\Store\InoutQueryModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'store_id',
                    ], 
                ],
            ],
        ),
        self::ACCESS_KEY_CITY => [
            'desc'        => '城市',
            'resource'    => 'cityList',
            'parent_access' => '',
            'controllers' => [
                'App\Http\Controllers\Cp\OrderController',
                'App\Http\Controllers\Cp\Express',
                'App\Http\Controllers\Cp\SalesController',
                'App\Http\Controllers\Cp\GoodsController',
                'App\Http\Controllers\Cp\ExpressBase',
                'App\Http\Controllers\Cp\ExpressBill',
                'App\Http\Controllers\Cp\ExpressDispatch',
                'App\Http\Controllers\Cp\TempPurchase',
                'App\Http\Controllers\Cp\StatsController',
                'App\Http\Controllers\Cp\ReportFormController',
                'App\Http\Controllers\Cp\OverSalesController',
                'App\Http\Controllers\Cp\ChargeoffController',
                'App\Http\Controllers\Cp\PaymentController',
                'App\Http\Controllers\Cp\InoutQueryController',
                'App\Http\Controllers\Cp\SupplierController',
                'App\Http\Controllers\Cp\SellerProcessController',
                'App\Http\Controllers\Cp\HomeController',
                'App\Http\Controllers\Cp\ThirdPartyController',
                'App\Http\Controllers\Cp\ThirdSupplierBillController',
                'App\Http\Controllers\Cp\UserAddressController',
                'App\Http\Controllers\Cp\KaController',
                'App\Http\Controllers\Cp\Workorder',
                'App\Http\Controllers\CpApi\WorkorderController',
            ],
            'rules' => [
                'App\Http\Controllers\Cp\OrderController' => [
                    'check_key' => ['order_id', 'orderid', 'id'],
                    'callback'  => [
                        'class'    => 'App\Modules\Order\OrderModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ], 
                ],
                'App\Http\Controllers\Cp\Express' => [
                    'check_key' => ['express_id', 'expressid', 'id'],
                    'callback'  => [
                        'class'    => 'App\Modules\Express\ExpressModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ],
                ],
                'App\Http\Controllers\Cp\ExpressBase' => [
                    'check_key' => ['express_id', 'expressid'],
                    'callback'  => [
                        'class'    => 'App\Modules\Express\ExpressModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ],
                ],
                'App\Http\Controllers\Cp\ExpressBill' => [
                    'check_key' => ['express_id', 'expressid'],
                    'callback'  => [
                        'class'    => 'App\Modules\Express\ExpressModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ],
                ],
                'App\Http\Controllers\Cp\ExpressDispatch' => [
                    'check_key' => ['express_id', 'expressid', 'id'],
                    'callback'  => [
                        'class'    => 'App\Modules\Express\ExpressModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ],
                ],
                'App\Http\Controllers\Cp\GoodsController' => [
                    'check_key' => ['goods_id', 'goodsid',],
                    'callback'  => [
                        'class'    => 'App\Modules\Goods\GoodsModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ],
                ],
                'App\Http\Controllers\Cp\SupplierController' => [
                    'check_key' => ['supplierid'],
                    'callback'  => [
                        'class'    => 'App\Modules\Supplier\SupplierModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ],
                ],
                'App\Http\Controllers\Cp\SellerProcessController' => [
                    'check_key' => ['order_id', 'seller_id'],
                    'callback'  => [
                        'class'    => 'App\Modules\SellerProcess\SellerProcessModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ],
                ],
                'App\Http\Controllers\Cp\ThirdPartyController' => [
                    'check_key' => ['id'],
                    'callback'  => [
                        'class'    => 'App\Modules\Order\OrderThirdPartyModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ],
                ],
                'App\Http\Controllers\Cp\KaController' => [
                    'check_key' => ['company_id'],
                    'callback'  => [
                        'class'    => 'App\Modules\Order\KaModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ],
                ],
                'App\Http\Controllers\Cp\Workorder' => [
                    'check_key' => ['id'],
                    'callback'  => [
                        'class'    => 'App\Modules\Workorder\WorkorderModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ],
                ],
                'App\Http\Controllers\CpApi\WorkorderController' => [
                    'check_key' => ['id'],
                    'callback'  => [
                        'class'    => 'App\Modules\Workorder\WorkorderModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ],
                ],
                'App\Http\Controllers\Cp\UserAddressController' => [
                    'check_key' => ['id'],
                    'callback'  => [
                        'class'    => 'App\Modules\Goods\GoodsThirdPartyModule',
                        'function' => 'accessPathCallback',
                        'data_key' => 'city_id',
                    ],
                ]
            ],
        ],
    );
    
    // 权限间关系配置
    public static $accessRelaConf = [
        self::ACCESS_KEY_CITY => [
            CityModule::ADCODE_BJ => [
                'store-bj-lgy',
                'store-bj-nk',
                'store-bj-sand',
                'store-bj-zf',
                'store-bj-third',
                'store-bj-nk-stock',
                'store-bj-nk-def',
                'store-bj-lgy-def',
                'store-bj-nk-sand-stock',
                'store-bj-new-bk-product',
            ],
            CityModule::ADCODE_TJ => [
                'store-tj-join',
            ],
            CityModule::ADCODE_CD => [
                'store-cd-by',
                'store-cd-third'
            ],
        ]

    ];
    //企业与销售的对应关系映射
    public static $compSalerMap = array(
        1 => 10010,
        2 => 21923,
        3 => 10022,
        4 => 10013
    );

    // 权限可编辑列表
    public static $accessProjectList = [
        'zsfucai' => '掌上辅材',
        'passport' => 'passport',
        'shanhujia' => '珊瑚家',
    ];

    // zsfucai shanhujia权限组的redis key
    const REDIS_ZSFUCAI_ACCESS_LIST = 'zf:st:zsfucai_access_list';
    const REDIS_SHANHUJIA_ACCESS_LIST = 'zf:st:shanhujia_access_list';

    // 菜单缓存key
    const REDIS_ACTION_MD5KEY  = "ps:st:action_list_md5_key:";
    const REDIS_ACTION_MENUKEY = 'ps:st:action_list_menu_key:';
    const REDIS_ACCESS_PATH_ALL = "ps:st:access_path_all:";
    const REDIS_ACCESS_PATH = "ps:st:access_path:";
}
