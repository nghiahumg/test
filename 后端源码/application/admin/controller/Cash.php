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
 * 提现管理
 * Class Item
 * @package app\admin\controller
 */
class Cash extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'LcCash';

    /**
     * 提现记录
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
        $this->cash_sum = Db::name('LcCash')->where("status = 1")->sum('money');
        if($this->request->param('u_agent')){
            $this->cash_sum = Db::name('LcCash c, lc_user u')->where("c.status = 1 AND c.uid = u.id AND u.agent = {$this->request->param('u_agent')}")->sum('c.money');
        }
        
        if($auth['authorize']==1){
            $where = "u.agent = {$auth['id']} AND i.status=1";
            $this->adm = false;
            $this->cash_sum = Db::name('LcCash c , lc_user u')->where("status = 1 AND c.uid = u.id AND u.agent = {$auth['id']}")->sum('c.money');
        }
        
        
        $this->title = '提现记录';
        $query = $this->_query($this->table)->alias('i')->field('i.*,u.phone');
        $query->join('lc_user u','i.uid=u.id')->where($where)->equal('i.status#i_status')->like('u.phone#u_phone,u.agent#u_agent')->dateBetween('i.time#i_time')->order('i.id desc')->page();
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
        foreach($data as &$vo){
            $bank = Db::name("lc_bank")->find($vo['bid']);
            if($bank){
                $wallet = Db::name("lc_withdrawal_wallet")->find($bank['wid']);
                if($wallet){
                    if($wallet['type']==1){
                        $vo['money2'] = "(≈".floatval($vo['money2'])." ".$wallet['mark'].")";
                    }else{
                        $vo['money2'] = "(≈".$wallet['mark'].floatval($vo['money2']).")";
                    }
                }
            }else{
                $vo['money2'] = "(该钱包已删除，请谨慎操作)";
            }
        }

    }

    /**
     * 同意提现
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function agree()
    {
        $this->applyCsrfToken();
        $id = $this->request->param('id');
        $cash = Db::name($this->table)->find($id);
        //sendSms(getUserPhone($cash['uid']), '18007', $cash['money']);
        $this->_save($this->table, ['status' => '1','time2' => date('Y-m-d H:i:s')]);
    }

    /**
     * 拒绝提现
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function refuse()
    {
        $this->applyCsrfToken();
        $id = $this->request->param('id');
        $cash = Db::name($this->table)->find($id);
       //拒绝时返还提现金额
        $LcTips = Db::name('LcTips')->where(['id' => '155']);
        addFinance($cash['uid'], $cash['money'],1, 
        $LcTips->value("zh_cn"). $cash['money'] ,
        $LcTips->value("zh_hk"). $cash['money'] ,
        $LcTips->value("en_us"). $cash['money'] ,
        $LcTips->value("th_th"). $cash['money'] ,
        $LcTips->value("vi_vn"). $cash['money'] ,
        $LcTips->value("ja_jp"). $cash['money'] ,
        $LcTips->value("ko_kr"). $cash['money'] ,
        $LcTips->value("ms_my"). $cash['money'] ,
        "","",2
        );
        setNumber('LcUser', 'money', $cash['money'], 1, "id = {$cash['uid']}");
        //返还手续费
        if($cash['charge']>0){
            $LcTips191 = Db::name('LcTips')->where(['id' => '191']);
            addFinance($cash['uid'], $cash['charge'],1, 
            $LcTips191->value("zh_cn"). $cash['charge'] ,
            $LcTips191->value("zh_hk"). $cash['charge'] ,
            $LcTips191->value("en_us"). $cash['charge'] ,
            $LcTips191->value("th_th"). $cash['charge'] ,
            $LcTips191->value("vi_vn"). $cash['charge'] ,
            $LcTips191->value("ja_jp"). $cash['charge'] ,
            $LcTips191->value("ko_kr"). $cash['charge'] ,
            $LcTips191->value("ms_my"). $cash['charge'] ,
            "","",9
            );
            setNumber('LcUser', 'money', $cash['charge'], 1, "id = {$cash['uid']}");
        }
        
        //sendSms(getUserPhone($cash['uid']), '18008', $cash['money']);
        $this->_save($this->table, ['status' => '2', 'time2' => date('Y-m-d H:i:s')]);
    }
    
    /**
     * 删除记录
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
