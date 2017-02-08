<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class Index extends Controller
{
    public function index()
    {
		//header('HTTP/1.1 301 Moved Permanently');//发出301头部
		//header('Location:/index.html');//跳转到带www的网址
		return $this->fetch();
	}
	
	public function news()
	{
			$article = db('article');
			$newsCount = $article->count();
			$page = input('?param.page')?input('param.page'):0;
			if(($page+1)*8 >= $newsCount){
				$paging['next'] = -1;
			}else{
				$paging['next'] = $page + 1;
			}
			if($page<=0){
				$paging['last'] = -1;
			}else{
				$paging['last'] = $page - 1;
			}
			$newList = $article->order('type desc,time desc')->limit($page*8,8)->select();
			$this->assign('newList',$newList);
			$this->assign('paging',$paging);
			return $this->fetch();
	}

}
