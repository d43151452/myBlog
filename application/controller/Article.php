<?php

namespace app\controller;

use think\Controller;
use think\Request;
use app\model\Articles;
use app\model\Tags;
use app\model\ArticlesTags;
use app\model\Sorts;
use app\model\Comments;
use think\Image;

class Article extends Controller
{
    protected $middleware = [
        'Check'=>[
            'except'=>['read','comment']
        ]
    ];
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
            return $this->success('添加成功','/article');
        }
        return $this->error('添加失败');
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
        Articles::where('id', $id)->setInc('hits');
        $data['article'] = Articles::get($id);
        $data['comments'] = Comments::where('articles_id',$id)->select();
        $data['next'] = Articles::where('id','>',$id)->field('id,title')->order('id','asc')->find();
        $data['last'] = Articles::where('id','<',$id)->field('id,title')->order('id','desc')->find();
        return view('',$data);
    }

     /**
     * 提交评论
     *
     * @param  int  $id
     * @param  \think\Request  $req
     * @return \think\Response
     */
    public function comment(Request $req, $id)
    {
        $data = $req->post();
        $reg = '/\[:EM(\d+)\]/';
        $count = preg_match_all($reg, $data['content'], $arr);
        if($count){
            foreach($arr[1] as $k => $v){
                if($v>100 || $v <1){
                    return $this->error('您选择的表情似乎有问题');
                }
            }
        }
        $val = [
            'nick_name|昵称'=>'require|max:10',
            'content|内容'=>'require|max:255',
            'captcha|验证码'=>'require|captcha'
        ];
        if($req->url){
            $val['url|网址'] = 'url';
        }

        if($req->email){
            $val['email|邮箱'] = 'email';
        }

        $validate = \Validate::make($val);
        if(!$validate->check($data)){
            return $this->error($validate->getError());
        }
        
        $data['articles_id'] = $id;
        Articles::where('id', $id)->setInc('comments');
        $info = Comments::create($data);
        if($info){
            return $this->success('评论成功');
        }
        return $this->error('评论失败');
    }

    /**
     * 显示编辑文章表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $data['sorts'] = Sorts::select();
        $data['article'] = Articles::get($id);
        return view('',$data);        
    }

    /**
     * 保存更新的文章
     *
     * @param  \think\Request  $req
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $req, Articles $art, $id)
    {
        $data = $req->param();
        
        $file = $req->file();
        if($file){

            $article = Articles::get($id);
            $path = \Env::get('root_path').'public'.$article->cover;
            if(is_file($path)){
                unlink($path);
            }

            $file = $req->file('cover');
            $rp = \Env::get('root_path');
            $info = $file->move($rp.'public/upload');
            $path = $rp.'public/upload/'.$info->getSaveName();
            $image = Image::open($path);
            $image->thumb(400,205,Image::THUMB_SCALING)->save($path);

            $data['cover'] = '/upload/'.$info->getSaveName();
        }
        $res = $art->save($data, ['id'=>$id]);
        if($res){
            return $this->success('修改成功','/article');
        }
        return $this->error('修改失败');
    }

    /**
     * 删除指定文章
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $art = Articles::get($id);
        $path = \Env::get('root_path').'public'.$art->cover;
        if(is_file($path)){
            unlink($path);
        }
        $art->delete();
        ArticlesTags::where('articles_id',$id)->delete();
        return $this->success('删除成功');
    }
}
