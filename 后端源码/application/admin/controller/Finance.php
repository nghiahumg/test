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
 * 流水记录
 * Class Item
 * @package app\admin\controller
 */
class Finance extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'LcFinance';

    /**
     * 流水记录
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
        if($auth['authorize']==1){
            $where = "u.agent = {$auth['id']}";
        }
        $this->title = '流水记录';
        $query = $this->_query($this->table)->alias('i')->field('i.*,u.phone,u.name');
        $query->join('lc_user u','i.uid=u.id')->equal('i.type#i_type')->where($where)->like('i.reason#i_reason,u.phone#u_phone')->dateBetween('i.time#i_time')->valueBetween('i.money')->order('i.id desc')->page();
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
