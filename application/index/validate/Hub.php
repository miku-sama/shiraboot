<?php
namespace app\index\validate;

use think\Validate;

class Hub extends Validate
{
	protected $rule = [
		'host' => 'require|min:7',
		'port' => 'require',
		'time' => 'require',
		'mode' => 'require',
		'vip' => 'require',
	];
	
	protected $message = [
        'host.require' => '请输入ip或url',
        'port.require' => '请输入端口号',
        'time.require' => '请输入时间',
        'mode.require' => '请选择模式',
        'vip.require' => '请选择节点类型',
    ];
	
	protected $scene = [
		'start' => ['host','port','time','mode','vip'],
	];
}