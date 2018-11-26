<?php

namespace app\controller;

use think\Controller;
use think\Request;
use app\model\Articles;
use app\model\Tags;
use app\model\ArticlesTags;
use app\model\Sorts;
use think\Image;

class Article extends Controller
{
    /**
     * 显示文章列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data['articles'] = Articles::select();
        return view('',$data);
    }

    /**
     * 显示创建文章表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $data['sorts'] = Sorts::select();
        return view('', $data);
    }

    /**
     * 处理文本图片上传
     * 
     * @param \think\Request $req
     * @return json
     */
    public function image(Request $req){
        $file = $req->file('image');
        $info = $file->move(\Env::get('root_path').'public/upload');
        if($info){
            return json([
                'success'=>true,
                'file_path'=>'/upload/'.$info->getSaveName()
            ]);
        }
        return json([
            'success'=>false,
            'msg'=>'上传失败',
        ]);
    }

    /**
     * 保存新建的文章
     *
     * @param  \think\Request  $req
     * @return \think\Response
     */
    public function save(Request $req)
    {
        $data = $req->param();

        $file = $req->file('cover');
        $rp = \Env::get('root_path');
        $info = $file->move($rp.'public/upload');
        $path = $rp.'public/upload/'.$info->getSaveName();
        $image = Image::open($path);
        $image->thumb(400,205,Image::THUMB_SCALING)->save($path);

        $data['cover'] = '/upload/'.$info->getSaveName();
        $res = Articles::create($data);
        if($res){
            return '成功';
        }
    }

    /**
     * 修改标签界面
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function at($id)
    {
        $subsql = \Db::table('articles_tags')->where('articles_id',$id)->field(['articles_id','tags_id'])->buildSql();
        $data['tags'] = Tags::alias('a')->leftJoin([$subsql=>'b'],'a.id = b.tags_id')->select();
        $data['article'] = Articles::get($id);
        return view('',$data);
    }

    /**
     * 保存界面
     *
     * @param  int  $id
     * @param  \think\Request  $req
     * @return \think\Response
     */
    public function atSave(Request $req, $id)
    {
        $data = $req->param('tags_id');
        foreach($data as $k => $v){
            $data[$k] = ['articles_id'=>$id,'tags_id'=>$data[$k]];
        }
        ArticlesTags::where('articles_id',$id)->delete();
        $at = new ArticlesTags;
        $info = $at->saveAll($data);
        if($info){
            return $this->success('修改成功','/article.html');
        }
        return $this->error->error('修改失败');
    }

    /**
     * 显示指定的文章
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑文章表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的文章
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
     * 删除指定文章
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
