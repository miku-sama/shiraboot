<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;

class Article extends Controller
{
	public function __construct(){
		parent::__construct();
    }
	
    public function index()
    {
		$user=db('users');
		if(!$userInfo=isAdmin($user))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		$this->assign('userInfo',$userInfo);
		$this->assign('serverInfo',db('server')->select());
		$this->assign('articleList',db('article')->select());
		return $this->fetch();
    }
	
	public function add(){
		$user=db('users');
		if(!$userInfo=isAdmin($user))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		
		if(Request::instance()->isPost())
		{
			$articleInfo = [
				'title'=>input('post.title'),
				'type'=>input('post.type'),
				'content'=>input('post.content'),
				'time'=>input('?post.time') ? input('post.time') : time(),
				'author'=>$userInfo['username'],
			];
			//var_dump($articleData,input('param.'));
			$articleId = db('article')->insertGetId($articleInfo);
			$this->success('发布成功!','Admin/Article/index');
			
		}else
		{
			$this->assign('userInfo',$userInfo);
			return $this->fetch();
		}
	}
	
	public function edit(){
		$user=db('users');
		if(!$userInfo=isAdmin($user))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		$article = db('article');
		if (!input('?param.id') || !$articleInfo = $article->where('id',input('param.id'))->find())
		{
			$this->error('参数不正确或文章不存在!');
			exit;
		}
		
		
		if(Request::instance()->isPost())
		{
			$articleInfo = [
				'title'=>input('post.title'),
				'type'=>input('post.type'),
				'content'=>input('post.content'),
				'time'=>input('?post.time') ? input('post.time') : time(),
				'author'=>$userInfo['username'],
			];
			$article->where('id',input('param.id'))->update($articleInfo);
			$this->success('修改成功!','Admin/Article/index');
			
		}else
		{
			$this->assign('articleInfo',$articleInfo);
			$this->assign('userInfo',$userInfo);
			return $this->fetch();
		}
	}
	
	public function del(){
		$user=db('users');
		if(!$userInfo=isAdmin($user))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		$article = db('article');
		if (!input('?param.id') || !$articleInfo = $article->where('id',input('param.id'))->find())
		{
			$this->error('参数不正确或文章不存在!');
			exit;
		}
		else
		{
			$article->where('id',input('param.id'))->delete();
			$this->success('删除成功','Admin/Article/index');
		}
	}
}
