<?php
namespace app\controller;

use think\Controller;
use think\Request;
use app\model\Articles;
use app\model\Tags;

class Index extends Controller
{
    /**
     * 显示首页
     *
     * @return \think\Response
     */
    public function index()
    {
        $data['articles'] = Articles::order('id', 'desc')->paginate(10);
        $data['tags'] = Tags::select();
        return view('',$data);
    }

    /**
     * 作者登录
     *
     * @return \think\Response
     */
    public function login()
    {
        return view();
    }

    /**
     * 处理登录
     *
     * @param  \think\Request  $req
     * @return \think\Response
     */
    public function doLogin(Request $req)
    {
        if($req->uname != 'zyz' || $req->pwd != 'mimaSHI0104'){
            return $this->error('请输入正确的账号或密码');
        }
        session('login','success');
        return $this->success('登录成功','/');
    }

    /**
     * 处理登录
     *
     * @return \think\Response
     */
    public function logout(Request $req)
    {
        \Session::clear();
        return $this->success('退出成功');
    }
}
