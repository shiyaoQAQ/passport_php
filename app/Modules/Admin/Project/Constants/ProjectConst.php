<?php

namespace App\Modules\Admin\Project\Constants;

class ProjectConst
{
    // 感觉不用定义这个
    const PROJECT_PASSPORT = 'passport';
    const PROJECT_ZSFUCAI = 'zsfucai';
    const PROJECT_SHANHUJIA = 'shanhujia';

    // 有效project列表
    static public $projectList = [
        self::PROJECT_PASSPORT,
        self::PROJECT_ZSFUCAI,
        self::PROJECT_SHANHUJIA,
    ];
    // 全部project列表
    static public $allProjectList = [
        self::PROJECT_PASSPORT,
        self::PROJECT_ZSFUCAI,
        self::PROJECT_SHANHUJIA,
    ];

    // 权限可编辑列表
    public static $projectNameMap = [
        self::PROJECT_PASSPORT => 'passport',
        self::PROJECT_ZSFUCAI => '掌上辅材',
        self::PROJECT_SHANHUJIA => '珊瑚家',
    ];

    // zsfucai shanhujia权限组的redis key
    const REDIS_ZSFUCAI_ACCESS_LIST = 'zf:st:zsfucai_access_list';
    const REDIS_SHANHUJIA_ACCESS_LIST = 'zf:st:shanhujia_access_list';
    const REDIS_ZSFUCAI_ROUTE_MAP = 'zf:st:zsfucai_route_map';
    const REDIS_SHANHUJIA_ROUTE_MAP = 'zf:st:shanhujia_route_map';

    // 菜单缓存key
    const REDIS_ACTION_MD5KEY  = "ps:st:action_list_md5_key:";
    const REDIS_ACTION_MENUKEY = 'ps:st:action_list_menu_key:';
    const REDIS_ACCESS_PATH_ALL = "ps:st:access_path_all:";
    const REDIS_ACCESS_PATH = "ps:st:access_path:";
}
