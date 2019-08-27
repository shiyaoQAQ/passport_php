<?php

namespace App\Modules\Admin\Access\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * CP组织架构部门表
 */
class CpDepartment extends Model
{

    const IS_DELETED  = 1;
    const NOT_DELETED = 0;

    protected $table = 'cp_department';
    public $timestamps = false;

    //存储所有的部门节点
    private static $allDepart;


    public function add($name, $mark, $cityId, $parentId, $uid, $email){
        $data = array(
                'parent_id'   => $parentId,
                'name'        => $name,
                'operator_id' => $uid,
                'ctime'       => date('Y-m-d H:i:s'),
                'city_id'     => $cityId,
                'email'       => $email,
            );
        if(!empty($mark)){
            $data['mark'] = $mark;
        }        
        $id = DB::table($this->table)->insertGetId($data);
        return $id;
    }    

    public function getByIds($ids){
        $node = self::where(array('is_deleted'=>self::NOT_DELETED))->whereIn('id', $ids)->get()->toArray();
        return empty($node) ? array() : $node;        
    }

    public function getById($id){
        $node = self::where(array('id'=>$id,'is_deleted'=>self::NOT_DELETED))->first()->toArray();
        return empty($node) ? array() : $node;        
    }

    public function getAllDepart(){
        if(empty(self::$allDepart)){
            $list = self::where('is_deleted', 0)->get()->toArray();
            self::$allDepart = empty($list) ? array() : $list;
        }
        return self::$allDepart;
    }

    public function getDepartmentTree($id = 0, $childList = array()){
        $departList = !empty($childList) ? $childList : $this->getChildDepart($id);
        if(empty($departList)){
            return array();
        }
        foreach ($departList as &$departDetail) {
            $childList = $this->getChildDepart($departDetail['id']);
            if(!empty($childList)){
                $ret = $this->getDepartmentTree($departDetail['id'], $childList);
                if(!empty($ret)){
                    $departDetail['child'] = $ret;
                }
            }   
        }
        return $departList;
    }

    public function getChildDepart($id){
        $allDepart   = $this->getAllDepart();
        $childDepart = array();
        foreach ($allDepart as $depart) {
            if($depart['parent_id'] == $id){
                $childDepart[] = $depart;
            }
        }
        return $childDepart;
    }  

    public function getParentDepart($id){
        $node = self::find($id);
        if(empty($node) || $node->parent_id == 0){
            return array();
        }
        $parentNode = self::find($node->parent_id);
        if(empty($parentNode)) {
            return array();
        }
        return $parentNode->toArray();        
    } 

    public function getAllChildNode($id){
        $list = array();
        $departList = $this->getChildDepart($id);
        if(empty($departList)){
            return $list;
        }
        $list = $departList;
        foreach ($departList as $departDetail) {
            $childList = $this->getChildDepart($departDetail['id']);
            if(!empty($childList)){
                $ret = $this->getAllChildNode($departDetail['id']);
                if(!empty($ret)){
                    $list = array_merge($list, $ret);
                }
            }   
        }
        return $list;
    }    

    public function updateDepart($id, $data){
        if(empty($id) || empty($data)){
            return false;
        }
        $ret = DB::table($this->table)->where(array('id'=>$id))->update($data);
        return $ret ? true : false;        
    }

    public function getDeaprtByMark($mark){
        if(empty($mark)){
            return array(); 
        }
        if(!is_array($mark) && is_string($mark)){
            $mark = array($mark);   
        }
        $ret = self::whereIn('mark',$mark)->get();
        return empty($ret) ? array() : $ret->toArray();
    }


    //代理商审核通过后给其添加签证商品
    public static function agentInitVisaProdPrice($AgentID){
        if(empty($AgentID)) return false;
        $prod_list = DB::table("Agent_BaseProduct")->get();
        if($prod_list){
            foreach($prod_list as $k=>$v){
                $data['BaseProdID'] = $v->BaseProdID;
                $data['AgentID'] = $AgentID;
                $data['ChildProfit'] = $v->ChildProfit;
                $data['BabyProfit'] = $v->BabyProfit;
                $data['AdultProfit'] = $v->AdultProfit;
                $data['OlderProfit'] = $v->OlderProfit;
                $data['BeVip'] = 2;
                $data['Status'] = 2;
                self::insert($data);
            }
        }
        return true;
    }
}
