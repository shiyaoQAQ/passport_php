<?php

namespace App\Modules\Admin\Project;

use App\Modules\Admin\Project\Constants\ProjectConst;

/**
 * 项目管理类
 */
class ProjectModule
{
    /**
     * 获取项目名
     *
     * @param [type] $project
     * @return string
     */
    public static function getProjectName($project)
    {
        return array_get(ProjectConst::$projectNameMap, $project ?: 0) ?: '';
    }

    /**
     * 获取有效projectMap
     * 不要用 $projectNameMap
     * 里面可能会有无效的project
     */
    public static function getProjectMap()
    {
        return array_only(ProjectConst::$projectNameMap, ProjectConst::$projectList);
    }

}

