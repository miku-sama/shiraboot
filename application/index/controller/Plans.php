<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class Plans extends Controller
{
	public function __construct(){
		parent::__construct();
    }
	
	public function index(){
        if(!$userInfo = chickLogin(db('users')))
		{
			$this->error('未登录','User/login');
			exit;
		}
		$this->assign('userInfo',$userInfo);
		$this->assign('api',db('config')->where('name','api_buy')->find()['value']==1);
		$this->assign('plansInfo',db('plans')->select());
		return $this->fetch();
	}
	
	public function buy(){
		$user = db('users');
        if(!$userInfo = chickLogin($user))
		{
			$this->error('未登录','User/login');
			exit;
		}
		$this->error('此接口为旧版测试接口,现在已经停用!');
		exit;
		$planId = input('param.id');
		$buy = db('plans');
		if($planId == 'api')
		{
			if(db('config')->where('name','api_buy')->find()['value']==1)
			{
				$apiData = [
					'uid'=>$userInfo['id'],
					'api_key'=>cpwd($userInfo['passwd'].time()),
					'status'=>1,
					'expiration'=>time() + 2592000,
				];
				db('api')->insert($apiData);
				$this->success('购买成功,api权限已成功应用到您的账户!');
				
			}
			else
			{
				$this->error('当前禁止购买api,请联系管理员!');
			}
			exit;
		}
		
		if(!$planInfo = $buy->where(['id'=>$planId,'status'=>1])->find())
		{
			$this->error('您购买的套餐不存在或处于停售状态!');
			exit;
		}
		unset($userInfo['percent']); //删除用户数据的百分比
		if($planInfo['type'] == 1) //普通套餐
		{
			if($planInfo['id'] != $userInfo['plan'] && $userInfo['expiration'] > time())
			{
				$this->error('您当前套餐未到期且购买的套餐和您当前套餐不同,禁止购买(您可以购买相同套餐来续期或等待套餐过期后再购买其它套餐).');
				exit;
			}

			if($userInfo['expiration'] > time()) //用户套餐还没有到期(续期)
			{
				$userInfo['expiration'] = $userInfo['expiration'] + $planInfo['cycle'];
				$successMsg = '续期成功!';
				
			}
			else
			{
				$userInfo['plan'] = $planId;
				$userInfo['plan_name'] = $planInfo['name'];
				$userInfo['maxtime'] = $planInfo['maxtime'];
				$userInfo['maxboot'] = $planInfo['maxboot'];
				$userInfo['maxnum'] = $planInfo['maxnum'];
				$userInfo['remainder'] = $planInfo['maxnum'];
				$userInfo['vip'] = $planInfo['vip'];
				$userInfo['expiration'] = time() + $planInfo['cycle'];
				$successMsg = '购买成功!';
			}
			$user->where('id',$userInfo['id'])->update($userInfo);
			
		}
		elseif($planInfo['type'] == 2) //临时补充包
		{
			$userInfo['remainder'] = $userInfo['remainder'] + $planInfo['maxnum'];
			$user->where('id',$userInfo['id'])->update($userInfo);
			$successMsg = '临时补充包以应用到您的账户,请在次日0点前使用,预期失效!';
		}
		elseif($planInfo['type'] == 3) //套餐补充包
		{
			$userInfo['maxnum'] = $userInfo['maxnum'] + $planInfo['maxnum'];
			$userInfo['remainder'] = $userInfo['remainder'] + $planInfo['maxnum'];
			$user->where('id',$userInfo['id'])->update($userInfo);
			$successMsg = '套餐补充包以应用到您的账户,请注意套餐到期时间,套餐补充包将随着套餐的到期而失效!';
		}
		elseif($planInfo['type'] == 4) //api权限
		{
			$apiData = [
				'uid'=>$userInfo['id'],
				'api_key'=>cpwd($userInfo['passwd'].time()),
				'expiration'=>time() + $cdkeyInfo['expiration'],
				'status'=>1,
			];
			db('api')->insert($apiData);
			$successMsg = 'api已经成功应用到您的账户.';
			$this->success('购买成功','hub/index');
		}
		elseif($planInfo['type'] == 5) //并发补充包
		{
			$userInfo['maxboot'] = $userInfo['maxboot'] + $planInfo['maxboot'];
			$user->where('id',$userInfo['id'])->update($userInfo);
			$successMsg = '并发补充包以应用到您的账户,请注意套餐到期时间,并发补充包将随着套餐的到期而失效!';
		}
		else
		{
			$this->error("您购买的套餐类型不正确!");
			exit;
		}
		$this->success($successMsg,'hub/index');
	}
	//跳转支付
	public function alipay(){
		$user = db('users');
        if(!$userInfo = chickLogin($user))
		{
			$this->error('未登录','User/login');
			exit;
		}
		//获取套餐信息
		if(!$planInfo = db('plans')->where(['id'=>input('param.id'),'status'=>1])->find()){
			$this->error('您购买的套餐不存在或处于停售状态!');
			exit;
		}
		//判断用户套餐是否一致,不一致时禁止购买.
		if($planInfo['id'] != $userInfo['plan'] && $userInfo['expiration'] > time()) {
			$this->error('您当前套餐未到期且购买的套餐和您当前套餐不同,禁止购买(您可以购买相同套餐来续期或等待套餐过期后再购买其它套餐).');
			exit;
		}
		//生成订单信息
		$orderInfo = [
			'uid'=>$userInfo['id'],
			'pay_total'=>$planInfo['price'],
			'plan_id'=>$planInfo['id'],
			'pay_type'=>'alipay',
			'pay_sn'=>'alip'.config('jsj_pay_id').time(), //生成订单号
			'pay_status'=>0,
			'time'=>time(),
			'pay_time'=>null
		];
		$orderId = db('order')->insertGetId($orderInfo);
		//生成支付信息
		$payInfo = [
			'total'=>$planInfo['price'],
			'uid'=>$userInfo['id'],
			'addnum'=> $orderInfo['pay_sn'],
			'apiid'=>config('jsj_pay_id'),
			'apikey'=>md5(config('jsj_pay_key')),
			'showurl'=>'http://'.$_SERVER['HTTP_HOST'].url('index/plans/buy2'),
		];
		echo "
			<form name='form1' action='http://api.web567.net/plugin.php?id=add:alipay' method='POST'>
				<input type='hidden' name='uid' value='".$payInfo['uid']."'>
				<input type='hidden' name='addnum' value='".$payInfo['addnum']."'>
				<input type='hidden' name='total' value='".$payInfo['total']."'>
				<input type='hidden' name='apiid' value='".$payInfo['apiid']."'>
				<input type='hidden' name='showurl' value='".$payInfo['showurl']."'>
				<input type='hidden' name='apikey' value='".$payInfo['apikey']."'>
			</form>
			<script>window.onload=function(){document.form1.submit();}</script> 
		";
		exit;
	}
	//支付回调
	public function buy2(){
		//以下四行无需更改
		$addnum = input('param.addnum');		//接收到的订单编号
		$uid = input('param.uid');				//接收到的会员id
		$total = round(input('param.total'), 2);			//接收到的支付金额
		$apikey = input('param.apikey');		//接收到的验证加密字串
		if(!input('?param.apikey')){
			$this->redirect('index/user/index');
		}
		//验证回调是否为金沙江(金沙江同步回调有fd漏洞)
		if(getIP()!=config('jsj_ip')){
			$this->error('支付完成,您的订单已被处理,如果2小时后您仍未获得套餐请联系管理员.','index/user/index');
			exit;
		}
		if($apikey!=md5(config('jsj_pay_key').$addnum)){
			$this->error('您的支付信息存在安全问题,请联系管理员!','index/user/index');
			exit;
		}
		$user = db('users');
		$order = db('order');
		//订单信息查询和空订单提示
		if(!$orderInfo = $order->where('pay_sn',$addnum)->find()){
			$this->error('没有查询到您的订单信息,您的订单编号为:"'.$addnum.'"请保存此订单编号,联系管理员解决!');
			exit;
		}
		//金额校验
		if( round($orderInfo['pay_total'],2) > $total){
			$this->error('您的支付金额有误!');
			exit;
		}
		//订单状态校验
		if($orderInfo['pay_status'] != 0){
			$this->error('您的订单信息状态有误,此订单可以已经支付!');
			exit;
		}
		
		$planInfo = db('plans')->where('id',$orderInfo['plan_id'])->find(); //查询套餐信息
		$userInfo = $user->where('id',$uid)->find(); //查询用户信息
		//更新用户数据
		unset($userInfo['percent']); //删除用户数据的百分比
		if($userInfo['expiration'] > time()) //用户套餐还没有到期(续期)
		{
			$userInfo['expiration'] = $userInfo['expiration'] + $planInfo['cycle'];
			$successMsg = '续期成功!';
			
		}
		else
		{
			$userInfo['plan'] = $planId;
			$userInfo['plan_name'] = $planInfo['name'];
			$userInfo['maxtime'] = $planInfo['maxtime'];
			$userInfo['maxboot'] = $planInfo['maxboot'];
			$userInfo['maxnum'] = $planInfo['maxnum'];
			$userInfo['remainder'] = $planInfo['maxnum'];
			$userInfo['vip'] = $planInfo['vip'];
			$userInfo['expiration'] = time() + $planInfo['cycle'];
			$successMsg = '购买成功!';
		}
		$user->where('id',$userInfo['id'])->update($userInfo);
		//更新订单数据
		$order->where('id',$orderInfo['id'])->update(['pay_status'=>1,'pay_time'=>time()]);
		$this->success($successMsg,'index/user/index');
		eixt;
	}
}