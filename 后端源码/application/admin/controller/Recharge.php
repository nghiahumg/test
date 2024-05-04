<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2019  [ http://www.kuyu.pro ]
// +----------------------------------------------------------------------
// | 官方网站: http://demo.thinkadmin.top
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/ThinkAdmin
// | github 代码仓库：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller;

use library\Controller;
use think\Db;

/**
 * 充值管理
 * Class Item
 * @package app\admin\controller
 */
class Recharge extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'LcRecharge';

    /**
     * 充值记录
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function index()
    {
        $auth = $this->app->session->get('user');
        $where = "";
        $this->adm = true;
        $this->recharge_sum = Db::name('LcRecharge')->where("status = 1")->sum('money');
        if($this->request->param('u_agent')){
            $this->recharge_sum = Db::name('LcRecharge r, lc_user u')->where("r.status = 1 AND r.uid = u.id AND u.agent = {$this->request->param('u_agent')}")->sum('r.money');
        }
        
        if($auth['authorize']==1){
            $where = "u.agent = {$auth['id']} AND i.status=1";
            $this->adm = false;
            $this->recharge_sum = Db::name('LcRecharge r , lc_user u')->where("status = 1 AND r.uid = u.id AND u.agent = {$auth['id']}")->sum('r.money');
        }
        
        $this->title = '充值记录';
        $query = $this->_query($this->table)->alias('i')->field('i.*,u.phone,u.name');
        $query->join('lc_user u','i.uid=u.id')->where($where)->equal('i.status#i_status')->like('i.orderid#i_orderid,i.type#i_type,u.phone#u_phone,u.agent#u_agent')->dateBetween('i.time#i_time')->order('i.id desc')->page();
    }

    /**
     * 数据列表处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _index_page_filter(&$data)
    {
        $this->type = Db::name($this->table)->field('type')->group('type')->select();
        $this->rejected = sprintf("%.2f",Db::name($this->table)->where("status = 2")->sum('money'));
        $this->finished = sprintf("%.2f",Db::name($this->table)->where("status = 1")->sum('money'));
        $this->reviewed = sprintf("%.2f",Db::name($this->table)->where("status = 0")->sum('money'));
        foreach($data as &$vo){
            $payment = Db::name("lc_payment")->find($vo['pid']);
            if($payment){
                // $num = 2;
                if($payment['type']==1){
                    // if($payment['rate']>10) $num = 4;
                    // if($payment['rate']>1000) $num = 6;
                    // if($payment['rate']>10000) $num = 8;
                    
                    $vo['money2'] = "(≈".floatval($vo['money2'])." ".$payment['mark'].")";
                }else{
                    $vo['money2'] = "(≈".$payment['mark'].floatval($vo['money2']).")";
                }
                
            }
        }
    }

    /**
     * 同意充值
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function agree()
    {
        $this->applyCsrfToken();
        $id = $this->request->param('id');
        $recharge = Db::name($this->table)->find($id);
        if($recharge&&$recharge['status'] == 0||$recharge['status'] == 3){
            $money = $recharge['money'];
            $uid = $recharge['uid'];
            $type = $recharge['type'];
            
            $LcTips152 = Db::name('LcTips')->where(['id' => '152']);
            $LcTips153 = Db::name('LcTips')->where(['id' => '153']);
            addFinance($uid, $money,1,
                $type .$LcTips152->value("zh_cn").$money,
                $type .$LcTips152->value("zh_hk").$money,
                $type .$LcTips152->value("en_us").$money,
                $type .$LcTips152->value("th_th").$money,
                $type .$LcTips152->value("vi_vn").$money,
                $type .$LcTips152->value("ja_jp").$money,
                $type .$LcTips152->value("ko_kr").$money,
                $type .$LcTips152->value("ms_my").$money,
                $type .$LcTips152->value("tr_tr").$money,
                $type .$LcTips152->value("es_es").$money,
                "","",1
            );
            setNumber('LcUser', 'money', $money, 1, "id = $uid");
            //成长值
            setNumber('LcUser', 'value', $money, 1, "id = $uid");
            //设置会员等级
            $user = Db::name("LcUser")->find($uid);
            setUserMember($uid,$user['value']);
            //上级奖励（一、二、三级）
            setRechargeReward($uid, $money);
            $this->_save($this->table, ['status' => '1','time2' => date('Y-m-d H:i:s')]);
        }
    }

    /**
     * 增减余额
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function change(){
        $this->applyCsrfToken();
        if($this->app->request->isPost()){
            $data = $this->request->param();
            if(!$data['name']) $this->error("用户账号必填");
            if(!$data['money']) $this->error("增减金额必填");
            $uid = Db::name("LcUser")->where(['phone'=>$data['name']])->value('id');
            if(!$uid) $this->error("暂无该用户");
            addFinance($uid, $data['money'], $data['type'], $data['zh_cn'],$data['zh_hk'],$data['en_us'],$data['th_th'],$data['vi_vn'],$data['ja_jp'],$data['ko_kr'],$data['ms_my'],$data['tr_tr'],$data['es_es']);
            setNumber('LcUser', 'money', $data['money'], $data['type'], "id = $uid");
            $this->success("操作成功");
        }
        $this->fetch('form');
    }

    /**
     * 拒绝充值
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function refuse()
    {
        $this->applyCsrfToken();
        $id = $this->request->param('id');
        $recharge = Db::name($this->table)->find($id);
        sendSms(getUserPhone($recharge['uid']), '18006', $recharge['money']);
        $this->_save($this->table, ['status' => '2','time2' => date('Y-m-d H:i:s')]);
    }

    /**
     * 删除充值记录
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function remove()
    {
        $this->applyCsrfToken();
        $this->_delete($this->table);
    }
}
