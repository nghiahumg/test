<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Users extends Model
{
    protected $user_table = 'LcUser';
    protected $finance_table = 'LcFinance';


    /**
     * Describe:添加流水
     * DateTime: 2020/9/5 19:52
     * @param $uid
     * @param $money
     * @param $type
     * @param $reason
     * @param $zh_cn
     * @param $en_us
     * @param $th_th
     * @param $vi_vn
     * @param $ja_jp
     * @param $ko_kr
     * @param $ms_my
     * @param $tr_tr
     * @param $es_es
     * @param string $remark
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addFinance($uid,$money,$type,$zh_cn,$zh_hk,$en_us,$th_th,$vi_vn,$ja_jp,$ko_kr,$ms_my,$tr_tr,$es_es,$remark="",$reason="",$reason_type=0){
        $user = Db::name($this->user_table)->find($uid);
        if($user['money']<0) return false;
        if(!$user) return false;
        $data = array(
        'uid' => $uid,
        'money' => $money,
        'type' => $type,
        'reason' => $reason,
        "zh_cn" =>$zh_cn,
        "zh_hk" =>$zh_hk,
        "en_us" =>$en_us,
        "th_th" =>$th_th,
        "vi_vn" =>$vi_vn,
        "ja_jp" =>$ja_jp,
        "ko_kr" =>$ko_kr,
        "ms_my" =>$ms_my,
        "tr_tr" =>$tr_tr,
        "es_es" =>$es_es,
        'remark' => $remark,
        'reason_type' => $reason_type,
        'before' => $user['money'],
        'time' => date('Y-m-d H:i:s')
        );
        Db::startTrans();
        $re = Db::name($this->finance_table)->insert($data);
        if($re){
            Db::commit();
            return true;
        }else{
            Db::rollback();
            return false;
        }
    }
}
