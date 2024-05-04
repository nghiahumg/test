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

namespace app\index\controller;

use library\Controller;
use think\Db;

/**
 * 应用入口
 * Class Index
 * @package app\index\controller
 */
class Index extends Controller
{

    /**
     * @description：首页
     * @date: 2020/5/13 0013
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function index()
    {
        if(getInfo("pc_open")) $this->fetch();
        if(check_wap()) $this->fetch();
    }

    /**
     * Describe:定时结算任务
     * DateTime: 2020/5/14 22:22
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function item_crontab()
    {
        $now = time();
        $invest_list = Db::name("LcInvestList")->where("UNIX_TIMESTAMP(time1) <= $now AND status = '0'")->select();
        if (empty($invest_list)) exit('暂无返息计划');
        foreach ($invest_list as $k => $v) {
            $max = Db::name("LcInvestList")->field('id')->where(['uid' => $v['uid'], 'iid' => $v['iid']])->order('num desc')->find();
            $is_last = false;
            if ($v['id'] == $max['id']) $is_last = true;
            $data = array('time2' => date('Y-m-d H:i:s'), 'pay2' => $v['pay1'], 'status' => 1);
            if (Db::name("LcInvestList")->where(['id' => $v['id']])->update($data)) {
                if ($v['pay1'] > 0) {
                    if ($is_last) {
                        if ($v['pay1'] <= 0) $v['pay1'] = 0;
                        Db::name('LcInvest')->where(['id' => $v['iid']])->update(['status' => 1, 'time2' => date("Y-m-d H:i:s")]);
                    }
                    $LcTips = Db::name('LcTips')->where(['id' => '182']);
                    addFinance($v['uid'], $v['pay1'], 1, 
                        $LcTips->value("zh_cn").$v['pay1'] ,
                        $LcTips->value("zh_hk").$v['pay1'] ,
                        $LcTips->value("en_us").$v['pay1'] ,
                        $LcTips->value("th_th").$v['pay1'] ,
                        $LcTips->value("vi_vn").$v['pay1'] ,
                        $LcTips->value("ja_jp").$v['pay1'] ,
                        $LcTips->value("ko_kr").$v['pay1'] ,
                        $LcTips->value("ms_my").$v['pay1'] ,
                        $LcTips->value("tr_tr").$v['pay1'] ,
                        $LcTips->value("es_es").$v['pay1'] ,
                        "","",11
                    );
                    setNumber('LcUser', 'money', $v['pay1'], 1, "id = {$v['uid']}");
                    setNumber('LcUser', 'income', $v['money1'], 1, "id = {$v['uid']}");
                }
            }
        }
    }

    /**
     * Describe:支付宝APP支付回调
     * DateTime: 2020/12/07 2:07
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function alipay_notify(){
        $data = $this->request->param();
        $out_trade_no = $data['out_trade_no'];
        $trade_status =  $data['trade_status'];
        if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
            require_once env("root_path") . "/vendor/alipayapp/aop/AopClient.php";
            $aop = new \AopClient();
            $aop->alipayrsaPublicKey = getInfo('alipay_public_key');
            $sign_check = $aop->rsaCheckV2($data, NULL, "RSA2");
            $recharge = Db::name("LcRecharge")->where(['orderid'=>$out_trade_no])->find();
            if($recharge&&$recharge['status'] == 0){
                $money = $recharge['money'];
                $uid = $recharge['uid'];
                $type = $recharge['type'];
                addFinance($uid, $money,1, $type . '入款' . $money);
                setNumber('LcUser', 'money', $money, 1, "id = $uid");
                sendSms(getUserPhone($uid), '18005', $money);
                $tid = Db::name('LcUser')->where('id', $uid)->value('top');
                if($tid) setRechargeRebate($tid, $money);
                $res = Db::name("LcRecharge")->where(['orderid'=>$out_trade_no])->update(['status' => '1','time2' => date('Y-m-d H:i:s')]);
                if($res) echo 'success';
            }elseif ($recharge['status'] == 1){
                echo 'success';
            }else {
                echo 'fail';
            }
        }else {
            echo "fail";
        }
    }


    public function item_auto_sale(){
        $item = Db::name("LcItem")->field("id,auto")->where("auto > 0")->select();
        if($item){
            foreach ($item as $v){
                setNumber('LcItem', 'sales_base', $v['auto'], 1, "id = {$v['id']}");
            }
        }
    }
    public function item_auto_percent(){
        $item = Db::name("LcItem")->field("id,percent,percent_add")->where("0<percent_add<100")->select();
        if($item){
            foreach ($item as $v){
                if($v['percent']+$v['percent_add']>100){
                    Db::name("LcItem")->where(['id'=>$v['id']])->update(['percent' => 100]);
                }else{
                    setNumber('LcItem', 'percent', $v['percent_add'], 1, "id = {$v['id']}");
                }
            }
        }
    }
}
