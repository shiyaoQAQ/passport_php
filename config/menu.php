<?php

$passportUrl = 'http://'. config('app.url');
$zsfucaiCpUrl = 'https://cp.' . config('app.zsfucai_url');
$shanhujiaCpUrl = 'http://cp.' . config('app.shanhujia_url');

if (config('app.env') == 'dev' && php_sapi_name() != 'cli' && $referUrl = app('request')->input('devurl')) {
    // 获取refer
    // 获取其他人的环境名
    $youcaiindex = 0;
    $urlSlice = explode('.', $referUrl);
    $youcaiindex = array_search('youcai123', $urlSlice);
    $dever = '';
    if ($youcaiindex > 0) {
        // 先判断前一个是否是缩写项目
        if (strlen($urlSlice[$youcaiindex - 1]) == 2) {
            $dever = $urlSlice[$youcaiindex - 2];
        } else {
            $dever = $urlSlice[$youcaiindex - 1];
        }
    }

    if ($dever) {
        // 替换对应域名的url
        $zsfucaiCpUrl = str_replace('shihongda', $dever, $zsfucaiCpUrl);
        $shanhujiaCpUrl = str_replace('shihongda', $dever, $shanhujiaCpUrl);
    }
}

return [
    'cp_menu' => [
        '权限' => [
            'logo' => 'locked',
            'menu_list' => [
                $passportUrl . '/cp/user/add'                        => '新增\关闭账户',
                $passportUrl . '/cp/departments#/index'              => '组织架构（新）',
                $passportUrl . '/cp/departments#/actionGroup'     => '权限组管理（新）',
                $passportUrl . '/cp/departments#/resourceGroup'     => '资源组管理（新）',
                $passportUrl . '/cp/department'                      => '组织架构',
                $passportUrl . '/cp/department/actiongrouplist'   => '权限组管理',
                $passportUrl . '/cp/department/resourcegrouplist' => '资源组管理',
                $passportUrl . '/cp/user/addDepartmentUser'          => '新增管理员',
                $zsfucaiCpUrl . '/log/index'                       => '操作日志',
                $passportUrl . '/cp/oauth/clients'          => 'oauth客户端列表',
            ],
        ],
        '电销' => [
            'logo' => 'social-whatsapp',
            'menu_list' => [
                $zsfucaiCpUrl. "/sellerProcess/sellerPanels" => '销售工作面板',
                $zsfucaiCpUrl. "/sellerProcess/sellerPanelsTotal" => '销售任务管理面板',
                $zsfucaiCpUrl. "/report/seller/process/all" => '销售目标完成度管理看板-总表',
                $zsfucaiCpUrl. "/report/seller/process/days" => '销售目标完成度管理看板-按天',
                $zsfucaiCpUrl. "/report/seller/process/groups" => '销售目标完成度管理看板-分拆总表',
                $zsfucaiCpUrl. "/report/seller/process/partDays" => '销售目标完成度管理看板-分拆按天',
                $zsfucaiCpUrl. "/sales/dailyWork" => '今日智能推荐客户',
                $zsfucaiCpUrl. "/sales/adduser" => '添加新客户',
                $zsfucaiCpUrl. "/sales/userlist" => '客户列表',
                $zsfucaiCpUrl. "/user/address" => '工地列表',
                $zsfucaiCpUrl. "/user/polt"    => '小区列表',
                $zsfucaiCpUrl. "/sales/dataallocation" => '数据分配',
                $zsfucaiCpUrl. "/sales/getPubseaList" => '公共海域',
                $zsfucaiCpUrl. "/sales/findAllocation" => '查找重分配',
                $zsfucaiCpUrl. "/sales/coupon/create" => '创建加急券',
                $zsfucaiCpUrl. "/sales/wholesale/opfList" => '批发商户',
                $zsfucaiCpUrl. "/sales/wholesale/saleOpfGoodsList" =>'批发价格查询',
                $zsfucaiCpUrl. "/sales/marketing/create" => '营销工具'
            ],
        ],
        '订单' => [
            'logo' => 'bag',
            'menu_list' => [
                $zsfucaiCpUrl. "/orders/orderList" => '订单列表',
                $zsfucaiCpUrl. "/order/refunds" => '退换货列表',
                $zsfucaiCpUrl. "/orders/create" => '创建订单',
                $zsfucaiCpUrl. "/order/bonus" => '红包列表',
                $zsfucaiCpUrl. "/order/bonusFilter" => '红包过滤器列表',
                $zsfucaiCpUrl. "/zone/list" => '优惠专区列表',
            ],
        ],
        '物流' => [
            'logo' => 'android-bus',
            'menu_list' => [
                $zsfucaiCpUrl. "/express/dispatchPanel" => '调度工作面板',
                $zsfucaiCpUrl. "/expresses" => '物流排线列表',
                $zsfucaiCpUrl. "/salestats/shippingPanel" => '物流运力面板',
                $zsfucaiCpUrl. "/express/carLocation" => '车辆实时位置面板',
                $zsfucaiCpUrl. "/express/drivers/signin" => '签到司机列表',
                $zsfucaiCpUrl. "/express/driverBill" => '物流司机结算',
                $zsfucaiCpUrl. "/express/porterBill" => '物流搬运工结算',
                $zsfucaiCpUrl. "/express/driverBillTotal" => '司机结算汇总',
                $zsfucaiCpUrl. "/express/porterBillTotal" => '搬运工结算汇总',
                $zsfucaiCpUrl. "/express/drivers" => '司机列表',
                $zsfucaiCpUrl. "/express/porters" => '搬运工列表',
                $zsfucaiCpUrl. "/report/express/driverToolReport" => '司机小程序使用明细',
                $zsfucaiCpUrl. "/report/express/driverToolTotal" => '司机小程序使用汇总',
                $zsfucaiCpUrl. "/express/dispatch/feeCalc" => '物流费用计算器',
                $zsfucaiCpUrl. "/express/obdManage" => 'OBD盒子管理页面',
                $zsfucaiCpUrl. "/express/truckOperation" => '车辆运营报表',
                $zsfucaiCpUrl. "/express/repertoryBoard" => '仓库场内操作看板',
            ],
        ],  
         '采购' => [
            'logo' => 'document-text',
            'menu_list' => [
                $zsfucaiCpUrl . "/purchasestore/list" => '采购单列表',
                $zsfucaiCpUrl . "/purchaserefund/list" => '采购退货单列表',
                $zsfucaiCpUrl . "/report/unsalableGoods" => '滞销报表',
                $zsfucaiCpUrl . "/purchase/temp" => '临采明细',
                $zsfucaiCpUrl . "/purchase/bill" => '临采账务',
                $zsfucaiCpUrl . "/goods/stockWarning" => '库存预警',
                $zsfucaiCpUrl . "/report/oemKeyData" => 'OEM-壁贝关键数据报表',
                $zsfucaiCpUrl . "/report/OEMReport" => 'OEM销售数据报表',
                $zsfucaiCpUrl . "/report/newWoodsReport" => '新木类数据报表',
                $zsfucaiCpUrl . "/purchase/profit/classProfit" => '分类毛利报表',
                $zsfucaiCpUrl . "/report/goodsSale/categoryInfo" => '品类销售数据',
                $zsfucaiCpUrl . "/report/goodsSale/brandInfo" => 'TOP10品牌销售数据',
                $zsfucaiCpUrl . "/report/goodsSale/skuInfo" => 'TOP20商品销售数据',
                $zsfucaiCpUrl . "/report/goodsSale/salerInfo" => '商品销售概况',
                $zsfucaiCpUrl . "/goods/exammingReport" => '商品检测报告',
                $zsfucaiCpUrl . "/report/powderOemReplaceRate" => '粉类OEM替代率数据报表',
                $zsfucaiCpUrl . "/report/powderOemCustomerReplaceRate" => '粉类-客户OEM替代率报表',
                $zsfucaiCpUrl . "/purchase/profit/skuProfitnewTest" => '商品毛利(最新)',
            ],
        ],    
         '工单' => [
            'logo' => 'network',
            'menu_list' => [
                $zsfucaiCpUrl . "/workorders" => '工单列表',
                $zsfucaiCpUrl . "/workorders/create" => '创建工单',
                $zsfucaiCpUrl . "/append/orderAppend" => '增补单列表',
            ],
        ], 
        '报表' => [
            'logo' => 'stats-bars',
            'menu_list' => [
                '管理' => [
                    $zsfucaiCpUrl. "/report/seller/sellerAchievement" => 'G1',
                    $zsfucaiCpUrl. "/report/incomeCostReport" => 'G2',
                    $zsfucaiCpUrl. "/reportform/seller/amoeba" => '阿米巴0.1版',
                ],
                '通用' => [
                    $zsfucaiCpUrl . '/exportData/order/orderDeliveryInfo' => '自助导出数据',
                    $zsfucaiCpUrl . "/salestats/rePurchaseRate" => '复购率报表',
                    $zsfucaiCpUrl . "/salestats/orderTime" => '订单节点效率图',
                    $zsfucaiCpUrl . '/report/skuDimensionPriceCost' => 'sku维度的价格和成本数据',
                    $zsfucaiCpUrl . '/report/homeDecorateBoom' => '家装景气指数',
                    $zsfucaiCpUrl . '/report/compDecorateBoom' => '工装景气指数',
                ],
                '销售' => [
                    $zsfucaiCpUrl . "/report/salerPerformance" => '销售绩效统计报表',
                    $zsfucaiCpUrl . "/report/salerPerformance/hr"=> '销售绩效统计表（原始版）',
                    $zsfucaiCpUrl . "/sales/firstOrderReferReport" => '首单渠道数据报表',
                    $zsfucaiCpUrl . "/reportform/salerank" => '销售排行榜',
                    $zsfucaiCpUrl . "/report/seller/online/pk" => '销售线上下单争霸赛报表',
                    $zsfucaiCpUrl . "/reportform/dailySaleRank" => '掌辅英雄榜',
                    $zsfucaiCpUrl . "/reportform/sellingPK" => '销售PK赛况表',
                    $zsfucaiCpUrl . "/salestats/sellerDailyReport" => '销售每日关键数据',
                    $zsfucaiCpUrl . "/salestats/smartPanel" => '智能推荐面板跟进报表',
                    $zsfucaiCpUrl . '/report/seller/address' => '工地管理报表',
                    $zsfucaiCpUrl . "/report/orderPriceStat" => '客单价区间报表',
                    $zsfucaiCpUrl . "/report/OrderPriceReport" => '客单价趋势报表',
                    $zsfucaiCpUrl . "/report/SABGoodsReport" => 'SAB商品的报表',
                    $zsfucaiCpUrl . "/salestats/compCustomer" => '企业客户对账表',
                    $zsfucaiCpUrl . "/reportform/saleReport" => '销售管理报表',
                    $zsfucaiCpUrl . "/salestats/keyCustomer" => '重点客户数据',
                    $zsfucaiCpUrl . "/salestats/keyCustomerTrend" => '重点客户数据趋势图',
                    $zsfucaiCpUrl . '/report/marketCost' => '市场成本明细报表',
                    $zsfucaiCpUrl . '/report/sellerImportCustom' => '销售录入数据报表'
                ],
                '销售-风控'=>[
                    $zsfucaiCpUrl . "/sales/unpayOrderList" => '欠款订单列表',
                    $zsfucaiCpUrl . "/sales/unpayUser" => '应收-客户列表',
                    $zsfucaiCpUrl . "/salestats/badOrderAmount" => '不良应收管理报表',
                    $zsfucaiCpUrl . "/reportform/salerReceiving" => '应收列表',
                    $zsfucaiCpUrl . "/report/chargeIncrement" => '总监主管绩效统计报表',
                    $zsfucaiCpUrl . "/report/salerBadAmount" => '销售坏账扣款报表',
                ],
                '客服售后' => [
                    $zsfucaiCpUrl . '/report/servicePerformanceReport' => '客服绩效统计报表',
                    $zsfucaiCpUrl . "/salestats/refundTime" => '退换货节点效率图',
                    $zsfucaiCpUrl . "/report/subsidyDetail" => '售后补贴明细表',
                ],
                '物流' => [
                    $zsfucaiCpUrl . "/reportform/shipping" => '物流费用汇总表',
                    $zsfucaiCpUrl . "/reportform/shippingDetail" => '物流费用明细表',
                    $zsfucaiCpUrl . '/report/driverManage' => '核心司机运营报表',
                    $zsfucaiCpUrl . '/report/driver/driverRank' => '掌辅司机英雄榜',
                    $zsfucaiCpUrl . "/salestats/lateMatch" => 'OBD和司机小程序迟配报表',
                    $zsfucaiCpUrl . "/report/storageExpressReport" => '自营联营仓储物流报表',
                    $zsfucaiCpUrl . "/report/express/ExpressLoadTime" => '物流装车时效报表',
                    $zsfucaiCpUrl . "/report/express/ExpressUseTime" => '配送单各流程耗时表',
                    $zsfucaiCpUrl . "/report/warehouse" => '库房绩效统计报表',
                    $zsfucaiCpUrl . "/report/orderExpressCost" => '订单维度物流成本报表',
                ],
                '采购' => [
                    $zsfucaiCpUrl . '/report/tempPurchaseReport' => '临采时效报表',
                    $zsfucaiCpUrl . "/report/purchasePriChangeReport" => '采购价格变更记录',
                    $zsfucaiCpUrl . "/report/oversales/list" => '超卖商品报表',
                    $zsfucaiCpUrl . '/report/cateBrandProfit/list' => '分类品牌毛利报表',
                ],
                '缺货统计' => [
                    $zsfucaiCpUrl . '/report/stockout' => '缺货统计',
                    $zsfucaiCpUrl . '/report/orderStockout' => '缺货统计（下单后）'
                ],
                '联营' => [
                    $zsfucaiCpUrl . '/report/sandOrder' => '砂石销售概况',
                    $zsfucaiCpUrl . '/report/sandProfit/detail' => '砂石毛利报表',
                ],
            ],
        ], 
        '商品及供应商' => [
            'logo' => 'stats-bars',
            'menu_list' => [
                $zsfucaiCpUrl . "/goods/addGoods" => '商品添加',
                $zsfucaiCpUrl . "/goods/goodsList" => '商品列表',
                $zsfucaiCpUrl . "/supplier/addSupplier" => '供应商添加',
                $zsfucaiCpUrl . "/supplier/supplierList" => '供应商列表',
                $zsfucaiCpUrl . "/goods/showLargeOrder" => '商品价格',
            ],
        ],
        '联营' => [
            'logo' => 'document-text',
            'menu_list' => [
                $zsfucaiCpUrl . "/thirdParty/transportation" => '联营商运力管理面板',
                $zsfucaiCpUrl . "/purchase/thirdSupplierBill/billList" => '联营订单费用表',
                $zsfucaiCpUrl . "/goods/thirdParty" => '联营商品列表',
                $zsfucaiCpUrl . "/thirdParty/manageThirdParty" => '联营商列表',
            ],
        ], 
        '财务' => [
            'logo' => 'social-yen',
            'menu_list' => [
                $zsfucaiCpUrl . "/finance/transfer" => '转账工具',
                $zsfucaiCpUrl . "/finance/subsidy" => '补贴工具',
                $zsfucaiCpUrl . "/finance/goOnline" => '付款明细',
                "应付" => [
                    $zsfucaiCpUrl . "/charge/list" => '记账单列表',
                    $zsfucaiCpUrl . "/payment/list" => '付款单列表',
                    $zsfucaiCpUrl . "/chargeoff/list" => '核销单列表',
                ],
            ],
        ],
        'KA'  => [
            'logo' => 'person',
            'menu_list' => [
                $zsfucaiCpUrl . "/ka/publicSea" => 'KA公海',
                $zsfucaiCpUrl . "/ka/selfSea" => 'KA私海',
                $zsfucaiCpUrl . "/ka/dutiesList" => '任务列表',
                $zsfucaiCpUrl . "/ka/company/list" => 'KA签约客户表',
                $zsfucaiCpUrl . "/ka/companyOrder" => 'KA客户对账表',
                $zsfucaiCpUrl . "/ka/compBill" => 'KA客户对账表(汇总表)',
                $zsfucaiCpUrl . "/ka/companyPermeation" => '企业客户渗透管理报表',
                $zsfucaiCpUrl . "/ka/companyOperation" => 'KA核心运营指标报表',
            ],
        ],
        '仓库' => [
            'logo' => 'model-s',
            'menu_list' => [
                $zsfucaiCpUrl . "/instore/list" => '入库单列表',
                $zsfucaiCpUrl . "/outstore/list" => '出库单列表',
                $zsfucaiCpUrl . "/check/list" => '盘点单列表',
                $zsfucaiCpUrl . "/gainloss/list" => '损益单列表',
                $zsfucaiCpUrl . "/stock/list" => '库存查询',
                $zsfucaiCpUrl . "/inoutquery/list" => '产品出入库查询',
            ],
        ],
        '珊瑚家' => [
            'logo' => 'ios-home',
            'menu_list' => [
                // $shanhujiaCpUrl . "/" => '珊瑚家欢迎页',
                $shanhujiaCpUrl . "/goods" => '商品',
                $shanhujiaCpUrl . "/order" => '订单',
            ],
        ],
    ],
    'menu_url' => [
        'passport' => $passportUrl,
        'zsfucai' => $zsfucaiCpUrl,
        'shanhujia' => $shanhujiaCpUrl,
    ],
];
