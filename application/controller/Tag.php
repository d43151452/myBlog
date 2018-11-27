<?php

namespace app\controller;

use think\Controller;
use think\Request;
use app\model\Tags;
use app\model\ArticlesTags;

class Tag extends Controller
{
    protected $middleware = ['Check'];
    /**
     * 显示分类列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data['tags'] = Tags::select();
        return view('',$data);
    }

    /**
     * 保存新建的分类
     *
     * @param  \think\Request  $req
     * @return \think\Response
     */
    public function save(Request $req, Tags $tag)
    {
        $data = $req->param();
        $req = $tag->save($data);
        if($req){
           return $this->success('添加成功'); 
        }
        return $this->error('添加失败');
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
        $tag = Tags::get($id);
        $tag->tag_name = $req->tag_name;
        if($tag->save()){
            return $this->success('修改成功');
        }
        return $this->error('修改失败');
    }

    /**
     * 删除指定分类
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $tag = Tags::get($id);
        if($tag->delete()){
            return $this->success('删除成功');
        }
        ArticlesTags::where('tags_id',$id)->delete();
        return $this->error('删除失败');
    }
}
