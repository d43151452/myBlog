<?php

namespace app\controller;

use think\Controller;
use think\Request;
use app\model\Logs;
class Log extends Controller
{
    protected $middleware = [
        'Check'=>[
            'except'=>['index']
        ]
    ];
    /**
     * 显示更新日志列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data['logs_y'] = Logs::group('year')->order('year','desc')->select();
        $data['logs_m'] = Logs::group('month(date)')->order('date','desc')->select();
        $data['logs_d'] = Logs::order('date','desc')->select();
        $data['login'] = session('login');
        return view('', $data);
    }

    /**
     * 显示创建更新日志表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('');
    }

    /**
     * 保存新建的更新日志
     *
     * @param  \think\Request  $req
     * @return \think\Response
     */
    public function save(Request $req)
    {
        $data = $req->param();
        $data['date'] = $req->year.'-'.$req->month.'-'.$req->day;
        unset($data['day']);
        $info = Logs::create($data);
        if($info){
            return $this->success('添加成功','/log');
        }
        return $this->error('添加失败');
    }

    /**
     * 删除指定更新日志
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $info = Logs::destroy($id);
        if($info){
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }
}
