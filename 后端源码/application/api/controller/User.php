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

namespace app\api\controller;

use library\Controller;
use Endroid\QrCode\QrCode;
use think\Db;

/**
 * 用户中心
 * Class Index
 * @package app\index\controller
 */
class User extends Controller
{
    public function info()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $user = Db::name("LcUser")->find($uid);
        $wait_money = Db::name('LcInvestList')->where("uid = $uid AND status = '0' AND money2 > 0")->sum('money2');
        $wait_lixi = Db::name('LcInvestList')->where("uid = $uid AND status = '0' AND money1 > 0")->sum('money1');
        $all_lixi = Db::name('LcInvestList')->where("uid = $uid AND status = '1' AND money1 > 0")->sum('money1');
        $all_money = $wait_money + $wait_lixi + $user['money'];
        $data = array(
            "wait_lixi" => sprintf("%.2f", $wait_lixi),
            "wait_money" => sprintf("%.2f", $wait_money),
            "all_lixi" => sprintf("%.2f", $all_lixi),
            "reward" => sprintf("%.2f", $user['reward']),
            "mobile" => $user['phone'],
            "money" => $user['money'],
            "all_money" => sprintf("%.2f", $all_money),
            "name" => $user['name'],
            "uid" => $uid,
            "is_auth" => $user['auth'],
            "user_icon" => getInfo('logo_img'),
            "credit" => $user['integral'],
            "vip_name" => getUserMember($user['member']),
        );
        $this->success("获取成功", $data);
    }

    /**
     * Describe:会员分享
     * DateTime: 2020/5/17 14:03
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function share()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $phone = Db::name('LcUser')->where(['id' => $uid])->value('phone');
        $share_user = Db::name('LcUser')->where(['top' => $uid])->field('phone,auth,time')->select();
        $reward = Db::name('LcReward')->field("register2")->find(1);
        $qrCode = new QrCode();
        $qrCode->setText(getInfo('domain') . '/#/register?m=' . $phone);
        $qrCode->setSize(300);
        $shareCode = $qrCode->getDataUri();
        $shareLink = getInfo('domain') . '/#/register?m=' . $phone;
        $data = array(
            'share_user' => $share_user,
            'share_image_url' => $shareCode,
            'share_link' => $shareLink,
            'reward' => $reward['register2'],
            'user_icon' => getInfo('logo_img'),
            'phone' => $phone
        );
        $this->success("获取成功", $data);
    }
    /**
     * Describe:我的团队
     * DateTime: 2020/5/17 14:03
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function myTeam()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $phone = Db::name('LcUser')->where(['id' => $uid])->value('phone');
        $reward = Db::name('LcReward')->field("invest1,invest2,invest3")->find(1);
        $qrCode = new QrCode();
        $qrCode->setText(getInfo('domain') . '/#/register?m=' . $phone);
        $qrCode->setSize(300);
        $shareCode = $qrCode->getDataUri();
        $shareLink = getInfo('domain') . '/#/register?m=' . $phone;
        $top1 = Db::name('LcUser')->where(['top' => $uid])->field('phone,time')->select();
        $top2 = Db::name('LcUser')->where(['top2' => $uid])->field('phone,time')->select();
        $top3 = Db::name('LcUser')->where(['top3' => $uid])->field('phone,time')->select();
        $myRecharge = Db::name('LcRecharge r , lc_user u')->where("status = 1 AND r.uid = u.id AND u.id = $uid")->sum('r.money');
        $top1Recharge = Db::name('LcRecharge r , lc_user u')->where("status = 1 AND r.uid = u.id AND u.top = $uid")->sum('r.money');
        $top2Recharge = Db::name('LcRecharge r , lc_user u')->where("status = 1 AND r.uid = u.id AND u.top2 = $uid")->sum('r.money');
        $top3Recharge = Db::name('LcRecharge r , lc_user u')->where("status = 1 AND r.uid = u.id AND u.top3 = $uid")->sum('r.money');
        $countRecharge = $myRecharge+$top1Recharge+$top2Recharge+$top3Recharge;
        $countCommission = Db::name('LcFinance')->where("uid = $uid AND reason LIKE '%推荐_%'")->sum('money');
        
        $data = array(
            'share_image_url' => $shareCode,
            'share_link' => $shareLink,
            'user_icon' => getInfo('logo_img'),
            'phone' => $phone,
            'reward' => $reward,
            'top1' => $top1,
            'top2' => $top2,
            'top3' => $top3,
            'count_recharge' => $countRecharge,
            'countCommission' => $countCommission,
        );
        $this->success("获取成功", $data);
    }

    /**
     * Describe:订单列表
     * DateTime: 2020/9/5 13:41
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function order()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $params = $this->request->param();
        $language = $params["language"];
        
        $list = Db::name('LcInvest')->field("$language,id,money,rate,day,status,time,time2")->where('uid', $uid)->order("id desc")->select();
        $wait_money = Db::name('LcInvestList')->where("uid = $uid AND status = '0' AND money2 > 0")->sum('money2');
        $wait_lixi = Db::name('LcInvestList')->where("uid = $uid AND status = '0' AND money1 > 0")->sum('money1');
        $all_money = Db::name('LcInvestList')->where("uid = $uid AND status = '1' AND money1 > 0")->sum('money1');
        $this->success("获取成功", ['list' => $list, 'on_money' => sprintf("%.2f", $wait_money), 'on_apr_money' => sprintf("%.2f", $wait_lixi), 'ok_apr_money' => sprintf("%.2f", $all_money)]);
    }

    /**
     * Describe:获取银行卡
     * DateTime: 2020/5/16 16:37
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bank()
    {
        $this->checkToken();
        $userInfo = $this->userInfo;
        $bank = Db::name('LcBank')->where(['uid' => $userInfo['id']])->order('id desc')->select();
        foreach ($bank as $k => $v) {
            $bank[$k]['account'] = dataDesensitization($v['account'], 4, 8);
        }
        $data = array(
            'count' => count($bank),
            'list' => $bank,
        );
        $this->success("获取成功", $data);
    }

    /**
     * Describe:获取银行卡及支付宝
     * DateTime: 2020/5/17 21:59
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function my_bank()
    {
        $this->checkToken();
        $userInfo = $this->userInfo;
        $this->user = Db::name('LcUser')->find($userInfo['id']);
        $intPwd = $this->user['mwpassword2']=='123456'?true:false;
        $bank = Db::name('LcBank bank,lc_withdrawal_wallet wallet')->field("bank.account as account,bank.bank as bank,bank.id as id,wallet.charge as charge,wallet.type as type,wallet.rate as rate,wallet.mark as mark")->where('bank.wid=wallet.id AND wallet.show=1')->where(['bank.uid' => $userInfo['id']])->order('bank.id desc')->select();
        foreach ($bank as $k => $v) {
            $bank[$k]['account'] = dataDesensitization($v['account'], 2, strlen($v['account'])-6);
        }
        $data = array(
            'count' => count($bank),
            'bank' => $bank,
            'money' => $this->user['money'],
            'intPwd' => $intPwd
        );
        $this->success("获取成功", $data);
    }
    /**
     * Describe:添加银行卡
     * DateTime: 2020/5/16 16:47
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bank_add()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $card = input('post.account/s', '');
        $bank = input('post.bank/s', '');
        $area = input('post.area/s', '');
        $img = input('post.img/s', '');
        $type = input('post.type/s', '');
        $this->user = Db::name('LcUser')->find($uid);
        $name = $this->user['phone'];
        $params = $this->request->param();
        $language = $params["language"];
        if (!$card) $this->error(Db::name('LcTips')->field("$language")->find('79'));
        $wallet = Db::name('lc_withdrawal_wallet')->find($params['wid']);
        if(!$wallet) $this->error(Db::name('LcTips')->field("$language")->find('190'));
        if($params['name']){
            $name = $params['name'];
        }
        if ($this->user['auth'] != 1) $this->error(Db::name('LcTips')->field("$language")->find('105'));
        $check_bank = Db::name('LcBank')->where(['account' => $card])->find();
        if ($check_bank) $this->error(Db::name('LcTips')->field("$language")->find('106'));
        if (getInfo('bank') == 1) {
            $auth_check = bankAuth($this->user['name'], $card, $this->user['idcard']);
            if ($auth_check['code'] == 0) $this->error($auth_check['msg']);
            $bank = $auth_check['bank'];
        } 
        
        $add = ['uid' => $uid, 'bank' => $bank, 'area' => $area, 'account' => $card, 'img' => $img, 'name' => $name,'type' => $type,'wid' => $wallet['id']];
        if (Db::name('LcBank')->insert($add)) $this->success(Db::name('LcTips')->field("$language")->find('107'));
        $this->error(Db::name('LcTips')->field("$language")->find('108'));
    }

    /**
     * Describe:删除银行卡
     * DateTime: 2020/5/16 16:38
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function bank_remove()
    {
        $this->checkToken();
        $userInfo = $this->userInfo;
        $id = input('post.id/d', '');
        $re = Db::name('LcBank')->where(['uid' => $userInfo['id'], 'id' => $id])->delete();
        if ($re) $this->success("操作成功");
        $this->error("操作失败");
    }
    
    /**
     * Describe:设置初始交易密码
     * DateTime: 2020/5/16 16:59
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function setIniPwd()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $params = $this->request->param();
        $language = $params["language"];
        $userInfo = $this->userInfo;
        
        $user = Db::name('LcUser')->find($uid);
        if (!$user) $this->error(Db::name('LcTips')->field("$language")->find('46'));
        
        if (payPassIsContinuity($params['password'])) $this->error(Db::name('LcTips')->field("$language")->find('122'));
        $data = ['password2' => md5($params['password']), 'mwpassword2' => $params['password']];
        //开启事务
        Db::startTrans();
        $res = Db::name('LcUser')->where('id', $uid)->update($data);
        if ($res) {
            Db::commit();
            $this->success(Db::name('LcTips')->field("$language")->find('112'));
        } else {
            Db::rollback();
            $this->error(Db::name('LcTips')->field("$language")->find('113'));
        }
    }

    /**
     * Describe:重置交易密码
     * DateTime: 2020/5/16 16:59
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function resetpaypwd_code()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $params = $this->request->param();
        $language = $params["language"];
        $userInfo = $this->userInfo;
        $user = Db::name('LcUser')->find($uid);
        if (!$user) $this->error(Db::name('LcTips')->field("$language")->find('46'));
        if (!$params['code']) $this->error(Db::name('LcTips')->field("$language")->find('119'));
        $sms_code = Db::name("LcSmsList")->where("phone = '{$user['phone']}'")->order("id desc")->value('ip');
        if ($params['code'] != $sms_code) $this->error(Db::name('LcTips')->field("$language")->find('120'));
        if (payPassIsContinuity($params['npassword'])) $this->error(Db::name('LcTips')->field("$language")->find('122'));
        $data = ['password2' => md5($params['npassword']), 'mwpassword2' => $params['npassword']];
        //开启事务
        Db::startTrans();
        $res = Db::name('LcUser')->where('id', $uid)->update($data);
        if ($res) {
            Db::commit();
            $this->success(Db::name('LcTips')->field("$language")->find('112'));
        } else {
            Db::rollback();
            $this->error(Db::name('LcTips')->field("$language")->find('113'));
        }
    }

    /**
     * Describe:提现申请
     * DateTime: 2020/5/16 18:06
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function cost_apply()
    {
        $this->checkToken();
        $params = $this->request->param();
        $language = $params["language"];
        $uid = $this->userInfo['id'];
        $this->user = Db::name('LcUser')->find($uid);
        $this->min_cash = getInfo('cash');
        $this->withdraw_num = getInfo('withdraw_num');
        $this->bank = Db::name('LcBank')->where('uid', $uid)->order("id desc")->select();
        if ($this->app->request->isPost()) {
            $bank = "";
            $wallet="";
            if ($this->user['auth'] != 1) $this->error(Db::name('LcTips')->field("$language")->find('126'));
            $data = $this->request->param();
            if (!is_numeric($data['money'])||$data['money']<=0) $this->error('ERROR 404');
            if (!$this->bank) $this->error(Db::name('LcTips')->field("$language")->find('127'));
            if ($data['bank_id'] != 0) {
                $bank = Db::name('LcBank')->where('id', $data['bank_id'])->find();
                if ($bank['uid'] != $uid || empty($bank)) $this->error(Db::name('LcTips')->field("$language")->find('128'));
            } else {
                if (empty($this->user['alipay'])) $this->error(Db::name('LcTips')->field("$language")->find('129'));
            }
            $wallet = Db::name('lc_withdrawal_wallet')->where('id', $bank['wid'])->find();
            if(!$wallet) $this->error(Db::name('LcTips')->field("$language")->find('128'));
            
            $invest = Db::name('LcInvest')->where('uid', $uid)->find();
            $today = date('Y-m-d 00:00:00');
            if ($this->user['password2'] != md5($data['passwd'])) $this->error(Db::name('LcTips')->field("$language")->find('130'));
            if ($data['money'] < $this->min_cash) 
            {
                $returnData = array(
                    "$language" =>Db::name('LcTips')->where(['id' => '131'])->value("$language").$this->min_cash.Db::name('LcTips')->where(['id' => '180'])->value("$language")
                );
                $this->error($returnData);
            }
            if ($this->user['money'] < $data['money']) $this->error(Db::name('LcTips')->field("$language")->find('132'));
            if (empty($invest)) $this->error(Db::name('LcTips')->field("$language")->find('133'));
            if ($this->withdraw_num <= Db::name('LcCash')->where("uid = $uid AND time > '$today' AND (status = 1 OR status = 0)")->count()){
                $returnData = array(
                    "$language" =>Db::name('LcTips')->where(['id' => '134'])->value("$language").$this->withdraw_num
                );
                $this->error($returnData);
            }
            $chargeMoney = 0.00;
            if($wallet['charge']>0){
                $chargeMoney = round($data['money']*$wallet['charge']/ 100, 2);
            }
            $num11 = 2;
            if($wallet['type']==1){
                if($wallet['rate']>10) $num11 = 4;
                if($wallet['rate']>1000) $num11 = 6;
                if($wallet['rate']>10000) $num11 = 8;
            }
            if ($data['bank_id'] == 0) {
                $add = array('uid' => $uid, 'name' => $bank['name'], 'bid' => $data['bank_id'], 'bank' => "Alipay", 'area' => 0, 'account' => $this->user['alipay'], 'money' => $data['money'],'charge' => $chargeMoney, 'status' => 0, 'time' => date('Y-m-d H:i:s'), 'time2' => '0000-00-00 00:00:00');
            } else {
                $add = array('uid' => $uid, 'name' => $bank['name'], 'bid' => $data['bank_id'], 'bank' => $bank['bank'], 'area' => $bank['area'] ?: 0, 'account' => $bank['account'], 'img' => $bank['img'], 'money' => $data['money']-$chargeMoney,'money2' => round($data['money']/$wallet['rate'],$num11)-$chargeMoney,'charge' => $chargeMoney, 'status' => 0, 'time' => date('Y-m-d H:i:s'), 'time2' => '0000-00-00 00:00:00');
            }
            if (Db::name('LcCash')->insert($add)) {
                //手续费
                $withdrawMoney = $data['money'];
                if($wallet['charge']>0){
                    $charge = round($data['money']*$wallet['charge']/ 100, 2);
                    //提现金额为：提现金额-手续费
                    $withdrawMoney = $withdrawMoney - $charge;
                    $LcTips = Db::name('LcTips')->where(['id' => '191']);
                    addFinance($uid, $charge, 2, 
                    $LcTips->value("zh_cn").$charge,
                    $LcTips->value("zh_hk").$charge,
                    $LcTips->value("en_us").$charge,
                    $LcTips->value("th_th").$charge,
                    $LcTips->value("vi_vn").$charge,
                    $LcTips->value("ja_jp").$charge,
                    $LcTips->value("ko_kr").$charge,
                    $LcTips->value("ms_my").$charge,
                    $LcTips->value("tr_tr").$charge,
                    $LcTips->value("es_es").$charge,
                    "","",9
                    );
                    setNumber('LcUser', 'money', $charge, 2, "id = $uid");
                }
                //提现流水
                $LcTips = Db::name('LcTips')->where(['id' => '136']);
                addFinance($uid, $withdrawMoney, 2, 
                $LcTips->value("zh_cn").$withdrawMoney,
                $LcTips->value("zh_hk").$withdrawMoney,
                $LcTips->value("en_us").$withdrawMoney,
                $LcTips->value("th_th").$withdrawMoney,
                $LcTips->value("vi_vn").$withdrawMoney,
                $LcTips->value("ja_jp").$withdrawMoney,
                $LcTips->value("ko_kr").$withdrawMoney,
                $LcTips->value("ms_my").$withdrawMoney,
                $LcTips->value("tr_tr").$withdrawMoney,
                $LcTips->value("es_es").$withdrawMoney,
                "","",2
                );
                setNumber('LcUser', 'money', $withdrawMoney, 2, "id = $uid");
                $this->success(Db::name('LcTips')->field("$language")->find('137'));
            } else {
                $this->error(Db::name('LcTips')->field("$language")->find('138'));
            }
        }
    }
     /**
     * Describe:提现列表
     * DateTime: 2020/5/17 13:41
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function cash_search()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $result = Db::name("LcCash")->where(['uid' => $uid, 'status' => 0])->select();
        $this->success('获取成功！', $result);
    }
    /**
     * Describe:充值选项
     * DateTime: 2020/5/17 13:41
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function recharge()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $user = Db::name('LcUser')->find($uid);
        $info = Db::name('LcInfo')->find(1);
        $payment = Db::name('LcPayment')->where([ 'show' => 1])->where("level <= ".$user['value'])->order("sort asc,id desc")->select();
        $list = array();
        if ($payment) {
            foreach ($payment as $k => $v) {
                $list[$k]["id"] = $v["id"];
                $list[$k]["type"] = $v["type"];
                $list[$k]["logo"] = $v["logo"];
                $list[$k]["give"] = $v["give"];
                $list[$k]["rate"] = $v["rate"];
                $list[$k]["mark"] = $v["mark"];
                $list[$k]["description"] = $v["description"];
                switch ($v["type"])
                {
                case 1:
                    $list[$k]["name"] = $v["crypto"];
                    $list[$k]["address"] = $v["crypto_qrcode"];
                    $list[$k]["qrcode"] = $v["crypto_link"];
                  break;  
                case 2:
                    $list[$k]["name"] = $v["alipay"];
                    $list[$k]["qrcode"] = $v["alipay_qrcode"];
                  break;
                case 3:
                    $list[$k]["name"] = $v["wx"];
                    $list[$k]["qrcode"] = $v["wx_qrcode"];
                  break;
                case 4:
                    $list[$k]["name"] = $v["bank"];
                    $list[$k]["user"] = $v["bank_name"];
                    $list[$k]["account"] = $v["bank_account"];
                  break;
                default:
                }
            }
        }
        $data = array(
            'money' => $user['money'],
            'min_recharge' => $info['min_recharge'],
            'payment'=>$list,
        );
        $this->success('获取成功！', $data);
    }
     /**
     * Describe:钱包选项
     * DateTime: 2020/5/17 13:41
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function wallet_type()
    {
        $this->checkToken();
        $params = $this->request->param();
        $language = $params["language"];
        $uid = $this->userInfo['id'];
        $this->user = Db::name('LcUser')->find($uid);
        if ($this->user['auth'] != 1) $this->error(Db::name('LcTips')->field("$language")->find('126'));
        $wallet = Db::name('lc_withdrawal_wallet')->where([ 'show' => 1])->order("sort asc,id desc")->select();
        $this->success('获取成功！', $wallet);
    }

    /**
     * Describe:充值
     * DateTime: 2020/5/17 13:26
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function recharge_type()
    {
        $this->checkToken();
        $params = $this->request->param();
        $language = $params["language"];
        $uid = $this->userInfo['id'];
        $this->user = Db::name('LcUser')->find($uid);
        $money = $params["money"];
        $paymentId = $params["id"];
        $info = Db::name('LcInfo')->find(1);
        if ($money < $info['min_recharge']) 
        {
            $returnData = array(
                "$language" =>Db::name('LcTips')->where(['id' => '140'])->value("$language").$info['min_recharge']
            );
            $this->error($returnData);
        }
        $payment = Db::name('LcPayment')->find($paymentId);
        $paymentArr = array();
        if (!$payment)
        {
            $this->error(Db::name('LcTips')->field("$language")->find('141'));
        }else{
            $paymentArr["id"] = $payment["id"];
            $paymentArr["type"] = $payment["type"];
            $paymentArr["give"] = $payment["give"];
            $paymentArr["logo"] = $payment["logo"];
            $paymentArr["rate"] = $payment["rate"];
            $paymentArr["mark"] = $payment["mark"];
            $paymentArr["description"] = $payment["description"];
            switch ($payment["type"])
            {
                case 1:
                    
                    $paymentArr["name"] = $payment["crypto"];
                    $paymentArr["qrcode"] = $payment["crypto_qrcode"];
                    $paymentArr["address"] = $payment["crypto_link"];
                  break;  
                case 2:
                    $paymentArr["name"] = $payment["alipay"];
                    $paymentArr["qrcode"] = $payment["alipay_qrcode"];
                  break;
                case 3:
                    $paymentArr["name"] = $payment["wx"];
                    $paymentArr["qrcode"] = $payment["wx_qrcode"];
                  break;
                case 4:
                    $paymentArr["name"] = $payment["bank"]."-".$payment["bank_name"];
                    $paymentArr["bank"] = $payment["bank"];
                    $paymentArr["username"] = $payment["bank_name"];
                    $paymentArr["account"] = $payment["bank_account"];
                  break;
                default:
            }
        }
        if ($this->user['auth'] != 1) $this->error(Db::name('LcTips')->field("$language")->find('142'), "", 405);
        $orderid = 'PAY' . date('YmdHis') . rand(1000, 9999) . rand(100, 999);
        $num11 = 2;
        if($payment['type']==1){
            if($payment['rate']>10) $num11 = 4;
            if($payment['rate']>1000) $num11 = 6;
            if($payment['rate']>10000) $num11 = 8;
        }
        $add = array(
            'orderid' => $orderid,
            'uid' => $uid,
            'pid' => $paymentId,
            'money' => $money,
            'money2' => round($money/$payment['rate'],$num11),
            'type' => $paymentArr["name"],
            'status' => 3,
            'time' => date('Y-m-d H:i:s'),
            'time2' => '0000-00-00 00:00:00'
        );
        $re = Db::name('LcRecharge')->insertGetId($add);
        $data = array(
            'payment' => $paymentArr,
            'orderId'=> $re
        );
        if ($re) $this->success('获取成功！', $data);
        $this->error("操作失败");
    }
    
    /**
     * Describe:充值申请
     * DateTime: 2020/5/17 13:40
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function recharge_apply()
    { 
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $data = $this->request->param();
        $update = array('status' =>'0',
                        'reason' =>$data['address'],
                        'warn' =>'0');
        $re = Db::name('LcRecharge')->where(['uid' => $uid, 'status' => 3, 'id' => $data['id']])->update($update);
        if ($re) $this->success('获取成功！');
        $this->error("操作失败");
    }

    /**
     * Describe:充值申请
     * DateTime: 2020/5/17 13:40
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function bank_apply()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $data = $this->request->param();
        $update = array('status' =>'0',
                        'warn' =>'0',
                        'reason' => '付款人：' . $data['name'] . '<br/>转账附言：' . $data['remark']);
        $re = Db::name('LcRecharge')->where(['uid' => $uid, 'status' => 3, 'id' => $data['id']])->update($update);
        if ($re) $this->success('获取成功！');
        $this->error("操作失败");
    }
    

    /**
     * @description：检查身份认证
     * @date: 2020/5/8 0008
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function check_auth()
    {
        $this->checkToken();
        $userInfo = $this->userInfo;
        $user = Db::name('LcUser')->find($userInfo['id']);
        $data = array(
            "idcard" => $user['idcard'],
            "is_auth" => $user['auth'] ? 'Y' : 'N',
            "mobile" => $user['phone'],
            "name" => $user['name']
        );
        $this->success("获取成功", $data);
    }
    /**
     * @description：身份认证
     * @date: 2020/5/15 0015
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function auth_email()
    {
        $this->checkToken();
        $userInfo = $this->userInfo;
        $params = $this->request->param();
        $language = $params["language"];
        $data = $this->request->param();
        $user = Db::name('LcUser')->find($userInfo['id']);
        if ($user['auth'] == 1) $this->error(Db::name('LcTips')->field("$language")->find('144')); 
        if (!$data['code']) $this->error(Db::name('LcTips')->field("$language")->find('87'));
        $sms_code = Db::name("LcSmsList")->where("phone = '{$user['phone']}'")->order("id desc")->value('ip');
        
        if ($data['code'] != $sms_code) $this->error(Db::name('LcTips')->field("$language")->find('88'));
        //开启事务
        $data = ['auth' => 1];
        Db::startTrans();
        $res = Db::name('LcUser')->where('id', $userInfo['id'])->update($data);
        if ($res) {
            Db::commit();
            $this->success(Db::name('LcTips')->field("$language")->find('148'));
        } else {
            Db::rollback();
            $this->error(Db::name('LcTips')->field("$language")->find('149'));
        }
    }

    /**
     * @description：签到
     * @date: 2020/5/15 0015
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function sign()
    {
        $this->checkToken();
        $params = $this->request->param();
        $language = $params["language"];
        $uid = $this->userInfo['id'];
        $user = Db::name('LcUser')->field("qiandao,qdnum")->find($uid);
        $today = strtotime(date('Y-m-d'));
        if ($today <= strtotime($user['qiandao'])) $this->error(Db::name('LcTips')->field("$language")->find('188'));
        $days = $user['qdnum'] + 1;
        $reward = Db::name("LcSignReward")->where(['days' => $days])->find();
        if ($reward) {
            $money = $reward['reward_num'];
            $this->sign_reward_money($reward['reward_num'], $uid);
        } else {
            $money = getReward('qiandao');
            $this->sign_reward_money($money, $uid);
        }
        Db::name('LcUser')->where(['id' => $uid])->update(['qiandao' => date('Y-m-d H:i:s')]);
        Db::name("LcUserSignLog")->insert(['date' => date("Y-m-d"), 'uid' => $uid]);
        setNumber('LcUser', 'qdnum', 1, 1, "id=$uid");
        $this->success("签到成功", ['days' => $days, 'reward_num' => $money, 'reward_type' => 1]);
    }

    /**
     * Describe:签到处理
     * DateTime: 2020/9/5 18:23
     * @param $money
     * @param $uid
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function sign_reward_money($money, $uid)
    {
        $LcTips186 = Db::name('LcTips')->where(['id' => '189']);
        addFinance($uid, $money, 1, 
            $LcTips186->value("zh_cn").$money,
            $LcTips186->value("zh_hk").$money,
            $LcTips186->value("en_us").$money,
            $LcTips186->value("th_th").$money,
            $LcTips186->value("vi_vn").$money,
            $LcTips186->value("ja_jp").$money,
            $LcTips186->value("ko_kr").$money,
            $LcTips186->value("ms_my").$money,
            $LcTips186->value("tr_tr").$money,
            $LcTips186->value("es_es").$money,
            "","",4
        );
        setNumber('LcUser', 'money', $money, 1, "id=$uid");
        setNumber('LcUser', 'reward', $money, 1, "id=$uid");
    }

    /**
     * Describe:本月签到记录
     * DateTime: 2020/9/5 16:17
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sign_log()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $month = getAllMonthDays();
        foreach ($month as $k => $v) {
            $sign_log = Db::name("LcUserSignLog")->where(['date' => $v, 'uid' => $uid])->find();
            $data[$k]['date'] = $v;
            $data[$k]['is_signin'] = $sign_log ? 1 : 0;
        }
        $this->success("获取成功", ['date_list' => $data]);
    }

    /**
     * Describe:签到奖励列表
     * DateTime: 2020/9/5 17:47
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sign_reward()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $today = strtotime(date('Y-m-d'));
        $today_sign = false;
        $user = Db::name("LcUser")->field("qiandao,qdnum")->find($uid);
        $sign_num = $user['qdnum'];
        if ($today <= strtotime($user['qiandao'])) $today_sign = true;
        if (!$today_sign) $sign_num = $sign_num + 1;
        $today_reward = Db::name("LcSignReward")->where(['days' => $sign_num])->find();
        if (!$today_reward) {
            $today_reward['reward_type'] = 1;
            $today_reward['reward_num'] = getReward('qiandao');
        }
        $reward = Db::name('LcSignReward')->select();
        foreach ($reward as &$v) {
            $v['can_draw'] = $user['qdnum'] >= $v['days'] ? 2 : 0;
        }
        $this->success("获取成功", ['reward_list' => $reward, 'signin_days' => $user['qdnum'], 'isSign' => $today_sign, 'today_reward' => $today_reward]);
    }

    /**
     * @description：消息列表
     * @date: 2020/5/15 0015
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function notice()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $msgtop = Db::name('LcMsg')->alias('msg')->where('(msg.uid = ' . $uid . ' or msg.uid = 0 ) and (select count(*) from lc_msg_is as msg_is where msg.id = msg_is.mid  and ((msg.uid = 0 and msg_is.uid = ' . $uid . ') or ( msg.uid = ' . $uid . ' and msg_is.uid = ' . $uid . ') )) = 0')->select();
        $msgfoot = Db::name('LcMsg')->alias('msg')->where('(select count(*) from lc_msg_is as msg_is where msg.id = msg_is.mid and msg_is.uid = ' . $uid . ') > 0')->select();
        $list = [];
        if ($msgtop) {
            foreach ($msgtop as $v) {
                $push['id'] = $v['id'];
                $push['time'] = $v['add_time'];
                $push['title'] = $v['title'];
                $push['title_zh_cn'] = $v['title_zh_cn'];
                $push['title_zh_hk'] = $v['title_zh_hk'];
                $push['title_en_us'] = $v['title_en_us'];
                $push['title_th_th'] = $v['title_th_th'];
                $push['title_vi_vn'] = $v['title_vi_vn'];
                $push['title_ja_jp'] = $v['title_ja_jp'];
                $push['title_ko_kr'] = $v['title_ko_kr'];
                $push['title_ms_my'] = $v['title_ms_my'];
                $push['title_tr_tr'] = $v['title_tr_tr'];
                $push['title_es_es'] = $v['title_es_es'];
                $push['content'] = strip_tags($v['content']);
                $push['content_zh_cn'] = strip_tags($v['content_zh_cn']);
                $push['content_zh_hk'] = strip_tags($v['content_zh_hk']);
                $push['content_en_us'] = strip_tags($v['content_en_us']);
                $push['content_th_th'] = strip_tags($v['content_th_th']);
                $push['content_vi_vn'] = strip_tags($v['content_vi_vn']);
                $push['content_ja_jp'] = strip_tags($v['content_ja_jp']);
                $push['content_ko_kr'] = strip_tags($v['content_ko_kr']);
                $push['content_ms_my'] = strip_tags($v['content_ms_my']);
                $push['content_tr_tr'] = strip_tags($v['content_tr_tr']);
                $push['content_es_es'] = strip_tags($v['content_es_es']);
                $push['is_read'] = false;
                array_push($list, $push);
            }
        }
        if ($msgfoot) {
            foreach ($msgfoot as $v) {
                $push['id'] = $v['id'];
                $push['time'] = $v['add_time'];
                $push['title'] = $v['title'];
                $push['title_zh_cn'] = $v['title_zh_cn'];
                $push['title_zh_hk'] = $v['title_zh_hk'];
                $push['title_en_us'] = $v['title_en_us'];
                $push['title_th_th'] = $v['title_th_th'];
                $push['title_vi_vn'] = $v['title_vi_vn'];
                $push['title_ja_jp'] = $v['title_ja_jp'];
                $push['title_ko_kr'] = $v['title_ko_kr'];
                $push['title_ms_my'] = $v['title_ms_my'];
                $push['title_tr_tr'] = $v['title_tr_tr'];
                $push['title_es_es'] = $v['title_es_es'];
                $push['content'] = strip_tags($v['content']);
                $push['content_zh_cn'] = strip_tags($v['content_zh_cn']);
                $push['content_zh_hk'] = strip_tags($v['content_zh_hk']);
                $push['content_en_us'] = strip_tags($v['content_en_us']);
                $push['content_th_th'] = strip_tags($v['content_th_th']);
                $push['content_vi_vn'] = strip_tags($v['content_vi_vn']);
                $push['content_ja_jp'] = strip_tags($v['content_ja_jp']);
                $push['content_ko_kr'] = strip_tags($v['content_ko_kr']);
                $push['content_ms_my'] = strip_tags($v['content_ms_my']);
                $push['content_tr_tr'] = strip_tags($v['content_tr_tr']);
                $push['content_es_es'] = strip_tags($v['content_es_es']);
                $push['is_read'] = true;
                array_push($list, $push);
            }
        }
        $this->success("获取成功", ['list' => $list, 'ok_read_num' => count($msgtop)]);
    }

    /**
     * @description：读取信息
     * @date: 2020/5/15 0015
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function notice_view()
    {
        $this->checkToken();
        $id = $this->request->param('id');
        $uid = $this->userInfo['id'];
        $where['uid'] = $uid;
        $where['mid'] = $id;
        $ret = Db::name('LcMsgIs')->where($where)->find();
        if (!$ret) Db::name('LcMsgIs')->insertGetId(['uid' => $uid, 'mid' => $id]);
        $notice = Db::name('LcMsg')->find($id);
        $data = array('view' => $notice,);
        $this->success("获取成功", $data);
    }

    /**
     * @description：资金流水
     * @date: 2020/5/15 0015
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function funds()
    {
        $this->checkToken();
        $language = $this->request->param('language');
        $uid = $this->userInfo['id'];
        $reason_id = $this->request->param('reason_id');
        $reason = array(
            "1" => "充值",
            "2" => "提现",
            "3" => "系统赠送",
            "4" => "签到",
            "5" => "分享奖励",
            "6" => "购买商品",
            "7" => "红包",
            "8" => "奖励",
            "9" => "新人福利",
            "10" => "推荐",
            "11" => "收益",
        );
        $user = Db::name("LcUser")->find($uid);
        $where[] = ['uid', 'eq', $uid];
        if ($reason_id) $where[] = ['reason_type', 'eq', "$reason_id"];
        $data = Db::name('LcFinance')->field("$language,id,money,type,reason,before,time,remark")->where($where)->order("id desc")->select();
        $money = array_column($data, "money");
        $this->success("获取成功", ['list' => $data,  'money' => $user['money'], 'username' => $user['name'] ?: $user['phone'], 'share_reward' => array_sum($money)]);
    }
}
