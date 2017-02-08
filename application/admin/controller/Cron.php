<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Cron extends Controller
{
	public function __construct(){
		if(!input('?param.'.config('cronpwd')))
		{
			die('you must die');
		}
		parent::__construct();
    }
	
	public function index(){

		$config = db('config');
		if($config->where('name','cron_user')->find()['value']+86400 >= time())
		{
			echo '用户次数无需更新';
		}
		else
		{
			Db::execute("update srk_users set remainder=maxnum"); //更新剩余次数
			Db::execute("update srk_users set plan=0, plan_name='未购买', maxtime=0, maxnum=0, maxboot=0, remainder=0, expiration=0, vip=0 where expiration < unix_timestamp()"); //更新用户套餐
			$config->where('name','cron_user')->update(['value'=>time()]);
			echo '用户次数更新完成';
		}
	}
}