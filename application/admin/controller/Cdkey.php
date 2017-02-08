<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;

class Cdkey extends Controller
{
	public function index(){
        if(!$userInfo = chickLogin(db('users')))
		{
			$this->error('未登录','User/login');
			exit;
		}
		
		$cdkey = db('cdkey');
		if(input('param.mode'=='add') && input('?param.quantity') && input('?param.day') && input('param.plan'))
		{
			$cdkeyInfo = [];
			for($i=0;$i<=input('param.quantity');$i++)
			{
				$cdkeyInfo[] = [
					'value'=>md5(time().date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 16)),
					'plan'=>input('param.plan'),
					'expiration'=>input('param.day')*86400,
					'status'=>0,
					'note'=>input('param.note'),
				];
			}
			$cdkey->insertAll($cdkeyInfo);
			//var_dump($cdkeyInfo);
			$this->success('成功添加');
		}
		elseif(input('param.mode'=='edit'))
		{
			
		}
		else
		{
			$cdkeyList = $cdkey->select();
			$this->assign('planList',db('plans')->select());
			$this->assign('cdkeyList',$cdkeyList);
			$this->assign('userInfo',$userInfo);
			return $this->fetch();
		}
	}
}