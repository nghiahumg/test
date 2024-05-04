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
 * 网站配置
 * Class Item
 * @package app\admin\controller
 */
class Info extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'LcInfo';
    protected $reward_table = 'LcReward';
    protected $version_table = 'LcVersion';
    protected $popup_table = 'LcPopup';

    /**
     * 网站设置
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function set()
    {
        $this->title = '网站设置';
        $this->_form($this->table, 'form');
    }

    /**
     * 表单数据处理
     * @param array $vo
     * @throws \ReflectionException
     */
    protected function _form_filter(&$vo){
        if ($this->request->isPost()&&isset($vo['ban_ip'])&&!empty($vo['ban_ip'])){
            $vo['ban_ip'] = trim(str_replace('，',',',$vo['ban_ip']));
        }
    }
    
    /**
     * 奖励设置
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function reward()
    {
        $this->title = '奖励设置';
        $this->_form($this->reward_table, 'reward');
    }
     /**
     * 版本控制
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function version()
    {
        $this->title = 'APP版本控制';
        $this->_form($this->version_table, 'version');
    }
    
    /**
     * 首页弹窗设置
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function popup()
    {
        $this->title = '首页弹窗设置';
        $this->_form($this->popup_table, 'popup');
    }

    /**
     * 支付设置
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function pay()
    {
        $this->title = '支付设置';
        $this->_form($this->table, 'pay');
    }

    /**
     * 图片设置
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function img()
    {
        $this->title = '支付设置';
        $this->_form($this->table, 'img');
    }
}
