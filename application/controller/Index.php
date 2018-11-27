<?php
namespace app\controller;

use think\Controller;
use think\Request;
use app\model\Articles;
use app\model\Tags;
use app\model\Sorts;

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
     * 处理退出
     *
     * @return \think\Response
     */
    public function logout(Request $req)
    {
        \Session::clear();
        return $this->success('退出成功');
    }

    /**
     * 分类内容显示
     *
     * @return 
     * @return \think\Response
     */
    public function sort($id)
    {
        $data['articles'] = Articles::where('sort_id',$id)->order('id', 'desc')->paginate(10);
        $data['sort'] = Sorts::get($id);
        return view('',$data);
    }

    /**
     * 标签检索显示
     *
     * @return 
     * @return \think\Response
     */
    public function tag($id)
    {
        $data['articles'] = Articles::where('id', 'in', function ($query) use ($id) {
            $query->table('articles_tags')->where('tags_id', $id)->field('articles_id');
        })->order('id', 'desc')->paginate(10);

        $data['tag'] = Tags::get($id);
        return view('',$data);
    }
}
