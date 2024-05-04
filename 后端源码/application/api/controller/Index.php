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
use library\File;
use think\facade\Session;
use library\tools\Data;
use think\Image;

/**
 * 首页
 * Class Index
 * @package app\index\controller
 */
class Index extends Controller
{
   
    public function webconfig()
    {
        $info = Db::name('LcInfo')->find(1);
        $data = array(
            "title" => $info['webname'],
            "web_name" => $info['webname'],
            "name" => $info['company'],
            "phone" => $info['tel'],
            "address" => $info['address'],
            "notice" => $info['notice'],
            "kefu_link" => $info['service'],
            "app_link" => $info['app'],
            "kefu_wx" => $info['wechat'],
            "kefu_qq" => $info['qq'],
            "kefu_tel" => $info['tel'],
            "ipcc_no" => $info['icp'],
            "is_maintain" => "N",
            "version" => "v1.2",
            "logo" => $info['tel']
        );
        $this->success("操作成功", $data);
    }
    /**
     * Describe:首页初始化
     * DateTime: 2020/5/17 1:08
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function int()
    {
        $params = $this->request->param();
        $language = $params["language"];
        $banner = Db::name('LcSlide')->field("$language,url")->where(['show' => 1,'type' =>0])->order('sort asc,id desc')->select();
        $popup = Db::name('LcPopup')->field("content_$language as content,show")->find(1);
        
        $ad1 = Db::name('LcSlide')->field("$language,url")->where(['show' => 1,'type' =>1])->limit(1)->select();
        $ad2 = Db::name('LcSlide')->field("$language,url")->where(['show' => 1,'type' =>2])->limit(2)->select();
        $ad3 = Db::name('LcSlide')->field("$language,url")->where(['show' => 1,'type' =>3])->limit(2)->select();
        $item1 = Db::name('LcItem')->field("$language,min,max,day,rate,total,id,desc_$language,percent")->where(['status' => 1,'index_type'=>1])->order('sort asc,id desc')->limit(8)->select();
        $item2 = Db::name('LcItem')->field("$language,min,max,day,rate,total,id,desc_$language,percent")->where(['status' => 1,'index_type'=>2])->order('sort asc,id desc')->limit(8)->select();
        $item3 = Db::name('LcItem')->field("$language,min,max,day,rate,total,id,desc_$language,percent")->where(['status' => 1,'index_type'=>3])->order('sort asc,id desc')->limit(8)->select();
        $version = Db::name('LcVersion')->find(1);
        $data = array(
            'banner' => $banner,
            'popup' => $popup,
            'ad1' => $ad1,
            'ad2' => $ad2,
            'ad3' => $ad3,
            'item1' => array(
                'list' => $item1,
                'show' => true,
            ),
            'item2' => array(
                'list' => $item2,
                'show' => true,
            ),
            'item3' => array(
                'list' => $item3,
                'show' => true,
            ),
            'version' => array(
                'app_version' => $version['app_version'],
                'android_app_down_url' => $version['android_app_down_url'],
                'ios_app_down_url' => $version['ios_app_down_url'],
                'app_instructions' => $version['app_instructions'],
                'wgt_down_url' => $version['wgt_down_url'],
                'wgt_instructions' => $version['wgt_instructions'],
            ),

        );
        $this->success("操作成功", $data);
    }

    /**
     * @description：活动列表
     * @date: 2020/9/4 0004
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function activity_list()
    {
        $params = $this->request->param();
        $language = $params["language"];
        $list = Db::name('LcActivity')->field("id,title_$language,desc_$language,img_$language,url,time")->where(['show' => 1])->order('sort asc,id desc')->select();
        $data = array(
            'list' => $list
            );
        $this->success("操作成功", $data);
    }
    /**
     * @description：活动详情
     * @date: 2020/9/4 0004
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function activity_detail()
    {
        $params = $this->request->param();
        $language = $params["language"];
        $activity = Db::name('LcActivity')->where(['show' => 1])->find($params["id"]);
        $data = array(
            'title' => $activity["title_$language"],
            'content' => $activity["content_$language"],
            'time'  => $activity["time"]
            );
        $this->success("操作成功", $data);
    }


    /**
     * Describe:关于我们列表
     * DateTime: 2020/5/17 1:22
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function about()
    {
        $article = Db::name('LcArticle')->where(['show' => 1, 'type' => 9])->order('sort asc,id desc')->select();
        $this->success("操作成功", ['list' => $article]);
    }

    /**
     * Describe:文章详情
     * DateTime: 2020/5/17 1:22
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function article_detail()
    {
        $params = $this->request->param();
        $language = $params["language"];
        $id = $this->request->param('id');
        $article = Db::name('LcArticle')->field("title_$language,content_$language")->find($id);
        $data = array(
            'title' => $article["title_$language"],
            'content' => $article["content_$language"]
            );
        $this->success("操作成功", $data);
    }


    /**
     * @description：搜索项目
     * @date: 2020/9/1 0001
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function item_search()
    {
        $params = $this->request->param();
        $language = $params["language"];
        $where = "";
        if (isset($params['title'])) $where = "$language LIKE '%{$params['title']}%'";
        if (isset($params['index_type'])&&$params['index_type']!=0) $where = "index_type = '{$params['index_type']}'";
        $data = Db::name('LcItem')->field("id,$language,min,max,num,total,rate,percent,day,desc_$language")->where($where)->order('sort asc,id desc')->select();
        $this->success("获取成功", ['list' => $data]);
    }

    /**
     * @description：项目列表
     * @date: 2020/5/14 0014
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function item()
    {
        $this->checkToken();
        $now = date('Y-m-d H:i:s');
        $data = Db::name('LcItem')->field("id,img,title,min,total,rate,percent,day,type,desc")->order("sort desc,id desc")->select();
        foreach ($data as &$v) {
            $v['apr_money'] = round($v['min'] * $v['rate'] / 100, 2);
            $v['schedule'] = round(getProjectPercent($v['id']), 2);
            $v['thumb'] = $v['img'];
        }
        $this->success("获取成功", ['list' => $data]);
    }
    /**
     * @description：项目详情
     * @date: 2020/9/4 0004
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function item_detail()
    {
        $params = $this->request->param();
        $language = $params["language"];
        $item = Db::name('LcItem')->field("id,$language as title,min,max,num,total,rate,percent,day,content_$language as content,img")->where(['status' => 1])->find($params["id"]);
        $this->success("操作成功", $item);
    }


    /**
     * Describe:提交订单
     * DateTime: 2020/9/5 3:19
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function item_apply()
    {
        $this->checkToken();
        $uid = $this->userInfo['id'];
        $this->user = Db::name('LcUser')->find($uid);
        $params = $this->request->param();
        $language = $params["language"];
        $money = $params["money"];
        
        if ($this->user['auth'] != 1) $this->error(Db::name('LcTips')->field("$language")->find('60'), '', 405);
        //余额
        if ($this->user['money'] < $params["money"]) $this->error(Db::name('LcTips')->field("$language")->find('65'));
        
        $item = Db::name('LcItem')->find($params["id"]);
        if (!$item) $this->error(Db::name('LcTips')->field("$language")->find('100'));
        
        //是否下架
        if (!$item['status']) $this->error(Db::name('LcTips')->field("$language")->find('67'));
        //进度
        if ($item['percent']>=100) $this->error(Db::name('LcTips')->field("$language")->find('68'));
        //投资金额
        if ($money<$item['min']){
            $returnData = array(
                "$language" =>Db::name('LcTips')->where(['id' => '72'])->value("$language").$item['min']
            );
            $this->error($returnData);
        }
        if ($money>$item['max']) {
            $returnData = array(
                "$language" =>Db::name('LcTips')->where(['id' => '77'])->value("$language").$item['max']
            );
            $this->error($returnData);
        }
        //限购次数
        $my_count = Db::name('LcInvest')->where(['uid' => $uid, 'pid' => $item['id']])->count();
        if($my_count >= $item['num']) {
            $returnData = array(
                "$language" =>Db::name('LcTips')->where(['id' => '70'])->value("$language").$item['num'].Db::name('LcTips')->where(['id' => '71'])->value("$language")
            );
            $this->error($returnData);
        }
        if (getInvestList($item['id'], $money, $uid)) {

            $LcTips73 = Db::name('LcTips')->where(['id' => '73']);
            $LcTips74 = Db::name('LcTips')->where(['id' => '74']);
            addFinance($uid, $money, 2, 
                $LcTips73->value("zh_cn").'《' . $item['zh_cn'] . '》，'.$LcTips74->value("zh_cn").$money,
                $LcTips73->value("zh_hk").'《' . $item['zh_hk'] . '》，'.$LcTips74->value("zh_hk").$money,
                $LcTips73->value("en_us").'《' . $item['en_us'] . '》，'.$LcTips74->value("en_us").$money,
                $LcTips73->value("th_th").'《' . $item['th_th'] . '》，'.$LcTips74->value("th_th").$money,
                $LcTips73->value("vi_vn").'《' . $item['vi_vn'] . '》，'.$LcTips74->value("vi_vn").$money,
                $LcTips73->value("ja_jp").'《' . $item['ja_jp'] . '》，'.$LcTips74->value("ja_jp").$money,
                $LcTips73->value("ko_kr").'《' . $item['ko_kr'] . '》，'.$LcTips74->value("ko_kr").$money,
                $LcTips73->value("ms_my").'《' . $item['ms_my'] . '》，'.$LcTips74->value("ms_my").$money,
                $LcTips73->value("tr_tr").'《' . $item['tr_tr'] . '》，'.$LcTips74->value("tr_tr").$money,
                $LcTips73->value("es_es").'《' . $item['es_es'] . '》，'.$LcTips74->value("es_es").$money,
                "","",6
            );
            setNumber('LcUser', 'money', $money, 2, "id = $uid");
            $this->success(Db::name('LcTips')->field("$language")->find('75'));
        }
        
        $this->error(Db::name('LcTips')->field("$language")->find('76'));
    }

    /**
     * @description：
     * @date: 2020/5/15 0015
     */
    public function sync()
    {
        $msg = 0;
        if ($this->checkLogin()) {
            $this->checkToken();
            $uid = $this->userInfo['id'];
            $msg = Db::name('LcMsg')->alias('msg')->where('(msg.uid = ' . $uid . ' or msg.uid = 0 ) and (select count(*) from lc_msg_is as msg_is where  msg.id = msg_is.mid  and ((msg.uid = 0 and msg_is.uid = ' . $uid . ') or ( msg.uid = ' . $uid . ' and msg_is.uid = ' . $uid . ') )) = 0')->count();
        }
        $data = ['check_dev_no' => false, 'is_open_notice_dialog' => $msg > 0 ? true : false];
        $this->success("操作成功", $data);
    }

    /**
     * @description：
     * @date: 2020/5/15 0015
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function login()
    {
        if ($this->request->isPost()) {
            $params = $this->request->param();
            $language = $params["language"];
            
            if (!$params['username'] || !$params['password']) $this->error(Db::name('LcTips')->field("$language")->find('79'));
            
            if (!judge($params['username'],"email"))  $this->error(Db::name('LcTips')->field("$language")->find('80'));
            
            $user = Db::name('LcUser')->where(['phone' => $params['username']])->find();
           
            if (!$user) $this->error(Db::name('LcTips')->field("$language")->find('81'));
           
            if ($user['password'] != md5($params['password'])) $this->error(Db::name('LcTips')->field("$language")->find('82'));
        
            if ($user['clock'] == 0) $this->error(Db::name('LcTips')->field("$language")->find('83'));
            
            Db::name('LcUser')->where(['id' => $user['id']])->update(['logintime' => time()]);
            $result = array(
                'token' => $this->getToken(['id' => $user['id'], 'phone' => $user['phone']]),
            );
            
            $this->success(Db::name('LcTips')->field("$language")->find('84'), $result);
        }
    }

    /**
     * @description：注册
     * @date: 2020/5/15 0015
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function register()
    {
        if ($this->request->isPost()) {
            $params = $this->request->param();
            $language = $params["language"];
            
            if (!judge($params['mobile'],"email")) $this->error(Db::name('LcTips')->field("$language")->find('86'));
            
            if (Db::name('LcUser')->where(['phone' => $params['mobile']])->find()) $this->error(Db::name('LcTips')->field("$language")->find('89'));
            
            if (strlen($params['password']) < 6 || 16 < strlen($params['password'])) $this->error(Db::name('LcTips')->field("$language")->find('90'));
            
            //一级邀请人
            $tid = 0;
            $top1Id = 0;
            if (isset($params['t_mobile']) && judge($params['t_mobile'],"email")) {
                $top1Id = Db::name('LcUser')->where(['phone' => $params['t_mobile']])->value('id');
                $tid = $top1Id ? $top1Id : 0;
            } else {
                $tid = isset($params['t_mobile']) ? $params['t_mobile'] : 0;
            }
            if (isset($params['t_mobile']) && !Db::name('LcUser')->find($tid)) $this->error(Db::name('LcTips')->field("$language")->find('91'));
            //二级邀请人
            $top2Id = 0;
            if(!empty($top1Id) && $top1Id > 0){
                $top2 = Db::name('LcUser')->where(['id' => $top1Id])->value('top');
                $top2Id = $top2 ? $top2 : 0;
            }
            //三级邀请人
            $top3Id = 0;
            if(!empty($top2Id) && $top2Id > 0){
                $top3 = Db::name('LcUser')->where(['id' => $top2Id])->value('top');
                $top3Id = $top3 ? $top3 : 0;
            }
            //判断代理线是否存在
            if (isset($params['agent']) && !Db::name('system_user')->find($params['agent'])) $this->error(Db::name('LcTips')->field("$language")->find('100'));
            
            $reward = Db::name('LcReward')->get(1);
            $member_id = Db::name('LcUserMember')->order("value asc")->value('id');
            $add = array(
                'phone' => $params['mobile'],
                'phone2' => $params['phone'],
                'password' => md5($params['password']),
                'password2' => md5("123456"),
                'mwpassword' => $params['password'],
                'mwpassword2' => "123456",
                'top' => $tid,
                'top2' => $top2Id,
                'top3' => $top3Id,
                'logintime' => time(),
                'money' => $reward['register'] ?: 0,
                'clock' => 1,
                'value' => $reward['registerzzz'] ?: 0,
                'time' => date('Y-m-d H:i:s'),
                'ip' => $this->request->ip(),
                'member' => $member_id,
                'agent' => $params['agent'],
            );
            $uid = Db::name('LcUser')->insertGetId($add);
            if (empty($uid)) $this->error(Db::name('LcTips')->field("$language")->find('92'));
            //注册赠送
            if ($reward['register'] > 0) {
                $LcTips93 = Db::name('LcTips')->where(['id' => '93']);
                $LcTips94 = Db::name('LcTips')->where(['id' => '94']);
                addFinance($uid, $reward['register'], 1, 
                    $LcTips93->value("zh_cn").$reward['register'].$LcTips94->value("zh_cn"),
                    $LcTips93->value("zh_hk").$reward['register'].$LcTips94->value("zh_hk"),
                    $LcTips93->value("en_us").$reward['register'].$LcTips94->value("en_us"),
                    $LcTips93->value("th_th").$reward['register'].$LcTips94->value("th_th"),
                    $LcTips93->value("vi_vn").$reward['register'].$LcTips94->value("vi_vn"),
                    $LcTips93->value("ja_jp").$reward['register'].$LcTips94->value("ja_jp"),
                    $LcTips93->value("ko_kr").$reward['register'].$LcTips94->value("ko_kr"),
                    $LcTips93->value("ms_my").$reward['register'].$LcTips94->value("ms_my"),
                    $LcTips93->value("tr_tr").$reward['register'].$LcTips94->value("tr_tr"),
                    $LcTips93->value("es_es").$reward['register'].$LcTips94->value("es_es"),
                    "","注册赠送",3
                );
            }
            //邀请注册赠送
            // if ($tid && $reward['register2'] > 0) {
            //     setNumber('LcUser', 'money', $reward['register2'], 1, "id = $tid");
            //     $LcTips94 = Db::name('LcTips')->where(['id' => '94']);
            //     $LcTips95 = Db::name('LcTips')->where(['id' => '95']);
            //     addFinance($tid, $reward['register2'], 1, 
            //         $LcTips95->value("zh_cn").$reward['register2'].$LcTips94->value("zh_cn"),
            //         $LcTips95->value("zh_hk").$reward['register2'].$LcTips94->value("zh_hk"),
            //         $LcTips95->value("en_us").$reward['register2'].$LcTips94->value("en_us"),
            //         $LcTips95->value("th_th").$reward['register2'].$LcTips94->value("th_th"),
            //         $LcTips95->value("vi_vn").$reward['register2'].$LcTips94->value("vi_vn"),
            //         $LcTips95->value("ja_jp").$reward['register2'].$LcTips94->value("ja_jp"),
            //         $LcTips95->value("ko_kr").$reward['register2'].$LcTips94->value("ko_kr"),
            //         $LcTips95->value("ms_my").$reward['register2'].$LcTips94->value("ms_my"),
            //         $params['mobile'],"推荐"
            //     );
            //     setNumber('LcUser', 'income', $reward['register2'], 1, "id = $tid");
            // }
            $data = array(
                'token' => $this->getToken(['id' => $uid, 'phone' => $params['mobile']]),
                'app_link' => getInfo('app')
            );
            
            $this->success(Db::name('LcTips')->field("$language")->find('96'), $data);
        }
    }
    

    /**
     * @description：忘记密码
     * @date: 2020/6/2 0002
     */
    public function forgetpwd()
    {
        $this->checkToken();
        
        if ($this->request->isPost()) {
            $params = $this->request->param();
            $language = $params["language"];
            
            $userInfo = $this->userInfo;
            $user = Db::name('LcUser')->find($userInfo['id']);
            if (!$user) $this->error(Db::name('LcTips')->field("$language")->find('46'));
            if (!$params['code']) $this->error(Db::name('LcTips')->field("$language")->find('44'));
            $sms_code = Db::name("LcSmsList")->where("phone = '{$user['phone']}'")->order("id desc")->value('ip');
            if ($params['code'] != $sms_code) $this->error(Db::name('LcTips')->field("$language")->find('45'));
            if (strlen($params['password']) < 6 || 16 < strlen($params['password'])) $this->error(Db::name('LcTips')->field("$language")->find('47'));
            if ($user['mwpassword'] == $params['password']) $this->error(Db::name('LcTips')->field("$language")->find('48'));
            $re = Db::name('LcUser')->where(['id' => $user['id']])->update(['password' => md5($params['password']), 'mwpassword' => $params['password']]);
            if ($re) $this->success(Db::name('LcTips')->field("$language")->find('49'));
            $this->error(Db::name('LcTips')->field("$language")->find('50'));
        }
    }
    
        /**
     * @description：忘记密码
     * @date: 2020/6/2 0002
     */
    public function forgetpwd_nologin()
    {
        if ($this->request->isPost()) {
            $params = $this->request->param();
            $language = $params["language"];
            
            if (!judge($params['mobile'],"email")) $this->error(Db::name('LcTips')->field("$language")->find('80'));
            if (!$params['code']) $this->error(Db::name('LcTips')->field("$language")->find('44'));
            $sms_code = Db::name("LcSmsList")->where("phone = '{$params['mobile']}'")->order("id desc")->value('ip');
            if ($params['code'] != $sms_code) $this->error(Db::name('LcTips')->field("$language")->find('45'));
            $user = Db::name('LcUser')->where(['phone' => $params['mobile']])->find();
            if (!$user) $this->error(Db::name('LcTips')->field("$language")->find('81'));
            if (strlen($params['password']) < 6 || 16 < strlen($params['password'])) $this->error(Db::name('LcTips')->field("$language")->find('47'));
            if ($user['mwpassword'] == $params['password']) $this->error(Db::name('LcTips')->field("$language")->find('48'));
            $re = Db::name('LcUser')->where(['id' => $user['id']])->update(['password' => md5($params['password']), 'mwpassword' => $params['password']]);
            if ($re) $this->success(Db::name('LcTips')->field("$language")->find('49'));
            $this->error(Db::name('LcTips')->field("$language")->find('50'));
        }
    }
    /**
     * @description：发送忘记密码验证
     * @date: 2020/6/2 0002
     */
    public function forgetpwd_code()
    {
        if ($this->request->isPost()) {
            $params = $this->request->param();
            $language = $params["language"];
            $phone = $params["mobile"];
            if (!$phone) $this->error(Db::name('LcTips')->field("$language")->find('34'));
            
            if (!Db::name('LcUser')->where(['phone' => $phone])->find()) $this->error(Db::name('LcTips')->field("$language")->find('46'));
            
            $sms_time = Db::name("LcSmsList")->where("phone = '$phone'")->order("id desc")->value('time');
            if ($sms_time && (strtotime($sms_time) + 300) > time()) $this->error(Db::name('LcTips')->field("$language")->find('37'));
            
            $rand_code = rand(1000, 9999);
            Session::set('forgetSmsCode', $rand_code);
            $msg = Db::name('LcTips')->where(['id' => '40'])->value($language).$rand_code.Db::name('LcTips')->where(['id' => '41'])->value($language);
            $data = array('phone' => $phone, 'msg' => $msg, 'code' => "忘记密码", 'time' => date('Y-m-d H:i:s'), 'ip' => $rand_code);
            $this->sendMail($phone,Db::name('LcTips')->where(['id' => '164'])->value($language),$msg);
            Db::name('LcSmsList')->insert($data);
            $this->success("操作成功");
        }
    }
    /**
     * @description：发送忘记密码验证
     * @date: 2020/6/2 0002
     */
    public function forgetpwd_email_code()
    {
        $this->checkToken();
        $userInfo = $this->userInfo;
        $user = Db::name('LcUser')->find($userInfo['id']);
        $phone = $user['phone'];
        $params = $this->request->param();
        $language = $params["language"];
        
        if ($user['auth'] == 0) $this->error(Db::name('LcTips')->field("$language")->find('39'));
        
        $sms_time = Db::name("LcSmsList")->where("phone = '$phone'")->order("id desc")->value('time');
        
        if ($sms_time && (strtotime($sms_time) + 300) > time()) $this->error(Db::name('LcTips')->field("$language")->find('37'));
        
        $rand_code = rand(1000, 9999);
        Session::set('forgetSmsCode', $rand_code);
        $msg =Db::name('LcTips')->where(['id' => '40'])->value($language).$rand_code.Db::name('LcTips')->where(['id' => '41'])->value($language);
        $data = array('phone' => $phone, 'msg' => $msg, 'code' => "忘记密码", 'time' => date('Y-m-d H:i:s'), 'ip' => $rand_code);
        $this->sendMail($phone,Db::name('LcTips')->where(['id' => '164'])->value($language),$msg);
        Db::name('LcSmsList')->insert($data);
        $this->success("操作成功");
    }
    
    /**
     * @description：验证邮箱
     * @date: 2020/6/2 0002
     */
    public function auth_email_code()
    {
        $this->checkToken();
        $userInfo = $this->userInfo;
        $user = Db::name('LcUser')->find($userInfo['id']);
        $phone = $user['phone'];
        $params = $this->request->param();
        $language = $params["language"];
        if ($user['auth'] == 1) $this->error(Db::name('LcTips')->field("$language")->find('160'));
        $sms_time = Db::name("LcSmsList")->where("phone = '$phone'")->order("id desc")->value('time');
        if ($sms_time && (strtotime($sms_time) + 300) > time()) $this->error(Db::name('LcTips')->field("$language")->find('37'));
        
        $rand_code = rand(1000, 9999);
        Session::set('authSmsCode', $rand_code);
        $msg =Db::name('LcTips')->where(['id' => '161'])->value($language).$rand_code.Db::name('LcTips')->where(['id' => '162'])->value($language);
        $data = array('phone' => $phone, 'msg' => $msg, 'code' => "验证邮箱", 'time' => date('Y-m-d H:i:s'), 'ip' => $rand_code);
        $this->sendMail($phone,Db::name('LcTips')->where(['id' => '163'])->value($language),$msg);
        Db::name('LcSmsList')->insert($data);
        $this->success("操作成功");
    }
    
     public function sendMail($to,$title, $content)
    {
        require_once env("root_path") . "/vendor/phpmailer/src/QQMailer.php";
        // 实例化 QQMailer
        $mailer = new \QQMailer(true); 
        $mailer->send($to, $title, $content);
    }
    
    /**
     * Describe:钱包地址上传
     * DateTime: 2020/5/17 20:01
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function bank_link_upload()
    {
        $this->checkToken();
        $params = $this->request->param();
        $language = $params["language"];
        
        if (!($file = $this->getUploadFile()) || empty($file)) $this->error(Db::name('LcTips')->field("$language")->find('56'));
        if (!$file->checkExt(strtolower(sysconf('storage_local_exts')))) $this->error(Db::name('LcTips')->field("$language")->find('57'));
        if ($file->checkExt('php,sh')) $this->error(Db::name('LcTips')->field("$language")->find('57'));
        $this->safe = boolval(input('safe'));
        $this->uptype = $this->getUploadType();
        $this->extend = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);
        $name = File::name($file->getPathname(), $this->extend, '', 'md5_file');
        $info = File::instance($this->uptype)->save($name, file_get_contents($file->getRealPath()), $this->safe);
        if (is_array($info) && isset($info['url'])) {
            $img = $this->safe ? $name : "http://".$_SERVER['HTTP_HOST'].$info['url'];
        } else {
            $this->error(Db::name('LcTips')->field("$language")->find('57'));
        }
        $this->success("操作成功", $img);
    }
      /**
     * 获取文件上传方式
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function getUploadType()
    {
        $this->uptype = input('uptype');
        if (!in_array($this->uptype, ['local', 'oss', 'qiniu'])) {
            $this->uptype = sysconf('storage_type');
        }
        return $this->uptype;
    }

    /**
     * Describe:获取本地上传文件
     * DateTime: 2020/5/17 19:46
     * @return array|\think\File|null
     */
    private function getUploadFile()
    {
        try {
            return $this->request->file('file');
        } catch (\Exception $e) {
            $this->error(lang($e->getMessage()));
        }
    }

}
