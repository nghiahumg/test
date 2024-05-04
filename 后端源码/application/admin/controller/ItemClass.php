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
use library\tools\Data;
use think\Db;

/**
 * 项目分类管理
 * Class Item
 * @package app\admin\controller
 */
class ItemClass extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'LcItemClass';

    /**
     * 项目分类管理
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
        $this->title = '项目分类管理';
        $query = $this->_query($this->table)->like('zh_cn');
        $query->order('sort asc,id desc')->page(false);
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
        $data = Data::arr2table($data);
    }

    /**
     * 添加项目分类
     * @auth true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function add()
    {
        $this->title = '添加项目分类';
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑项目分类
     * @auth true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit()
    {
        $this->title = '编辑项目分类';
        $this->_form($this->table, 'form');
    }

    /**
     * 删除项目分类
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function remove()
    {
        $this->_delete($this->table);
    }

    /**
     * 表单数据处理
     * @param array $vo
     * @throws \ReflectionException
     */
    protected function _form_filter(&$vo){
        if ($this->request->isGet()) {
            if (empty($vo['pid']) && $this->request->get('pid', '0')) $vo['pid'] = $this->request->get('pid', '0');
            $class = Db::name($this->table)->order('sort asc,id asc')->column('id,pid,zh_cn,zh_hk,en_us,th_th,vi_vn,ja_jp,ko_kr,ms_my,tr_tr,es_es,img,note');
            $this->class = Data::arr2table(array_merge($class, [['id' => '0', 'pid' => '-1', 'zh_cn' => '顶级分类']]));
            if (isset($vo['id'])) foreach ($this->class as $key => $c) if ($c['id'] === $vo['id']) $vo = $c;
            foreach ($this->class as $key => &$c) {
                if ($c['spt'] >= 3 ) unset($this->class[$key]);
                if (isset($vo['spt']) && $vo['spt'] <= $c['spt']) unset($this->class[$key]);
            }
        }
        if (empty($vo['add_time'])) $vo['add_time'] = date("Y-m-d H:i:s");
    }

}
