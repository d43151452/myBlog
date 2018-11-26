<?php

namespace app\controller;

use think\Controller;
use think\Request;
use app\model\Sorts;

class Sort extends Controller
{
    protected $middleware = ['Check'];
    /**
     * 显示分类列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data['sorts'] = Sorts::select();
        return view('',$data);
    }

    /**
     * 显示创建分类表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的分类
     *
     * @param  \think\Request  $req
     * @return \think\Response
     */
    public function save(Request $req)
    {
        //
    }

    /**
     * 显示指定的分类
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑分类表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的分类
     *
     * @param  \think\Request  $req
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $req, $id)
    {
        //
    }

    /**
     * 删除指定分类
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
