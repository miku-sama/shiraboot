<?php
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
	protected $rule = [
		'user' => 'require|min:6|max:18',
		'passwd' => 'require|min:6|max:32',
		'email' => 'require|email',
	];
	
	protected $message = [
        'user.require' => '请输入用户名',
        'user.min' => '正确的用户名为6~18个字符',
        'user.max' => '正确的用户名为6~18个字符',
        'passwd.require' => '请输入密码',
        'passwd.min' => '正确的密码为6~32个字符',
        'passwd.max' => '正确的密码为6~32个字符',
        'email' => '邮箱格式不正确',
        'email.require'  => '请输入邮箱',    
    ];
	
	protected $scene = [
		'register' => ['user','passwd','email'],
		'login' => ['user','passwd'],
		'repwd' => ['passwd'],
		'reemail' => ['email'],
	];

}