<?php

namespace App\Http\Models\common;

class Constant
{
	const ERR_API_NOT_EXISTS_NO = 100;
	const ERR_API_NOT_EXISTS_MSG = '接口不存在'; 
	const ERR_SUCCESS_NO = 200;
	const ERR_SUCCESS_MSG = '提交成功';
	const ERR_FAILED_NO = 400;
	const ERR_FAILED_MSG = '请求失败';
	const ERR_FAILED_DATA_MSG = '请求数据库失败';
	const ERR_TOKEN_NOT_EXISTS_NO = 401;
	const ERR_TOKEN_NOT_EXISTS_MSG = '登录已失效，请重新登录';
	const ERR_TOKEN_EXPIRE_NO = 402;
	const ERR_TOKEN_EXPIRE_MSG = '登录已过期，请重新登录';
	const ERR_FILED_NECESSARY_NO = 403;
	const ERR_FILED_NECESSARY_MSG = '参数不能为空';
	const ERR_PERMISSION_DENIED_NO = 404;
	const ERR_PERMISSION_DENIED_MSG = '没有权限';
	const ERR_ITEM_NOT_EXISTS_NO = 405;
	const ERR_ITEM_NOT_EXISTS_MSG = '相关信息不存在';
	const ERR_DATA_INCOMPLETE_NO = 406;
	const ERR_DATA_INCOMPLETE_MSG = '资料不完整';
	const ERR_CUNSTOMER_NOT_EXITSTS_NO = 407;
	const ERR_CUNSTOMER_NOT_EXITSTS_MSG = '客户不存在';
	const ERR_PASSWORD_LENGTH_NOT_ENOUGH_NO = 408;
	const ERR_PASSWORD_LENGTH_NOT_ENOUGH_MSG = '密码长度不够';
	const ERR_TOKEN_DISABLED_NO = 409;
	const ERR_PASSWORD_INCONFORMITY_NO = 410;
	const ERR_PASSWORD_INCONFORMITY_MSG = '两次输入密码不一致，请确认';
	const ERR_ACCOUNT_EXISTS_NO = 411;
	const ERR_ACCOUNT_EXISTS_MSG = '账号已有，请确认';
	const ERR_ACCOUNT_NOT_EXISTS_NO = 412;
	const ERR_ACCOUNT_NOT_EXISTS_MSG = '账号不存在';
	const ERR_PASSWORD_NO_CORRECT_NO = 413;
	const ERR_PASSWORD_NO_CORRECT_MSG = '密码不正确';
	const ERR_FILE_TOKEN_OVERDUE__NO = 414;
	const ERR_FILE_TOKEN_OVERDUE_MSG = '文件token已超期';
	const ERR_FILE_ANALYTIC_FAILED_NO = 415;
	const ERR_FILE_ANALYTIC_FAILED_MSG = '文件解析失败，请重试';
	const ERR_NONSUPPORT_FILE_FORMAT_NO = 416;
	const ERR_NONSUPPORT_FILE_FORMAT_MSG = '所传文件类型暂不支持！';
	const ERR_NON_FILE_NO = 417;
	const ERR_NON_FILE_MSG = '文件未上传！';
	const ERR_PICK_UP_NO = 418;
	const ERR_PICK_UP_MSG = '拾取任务失败';
	const ERR_GIVE_UP_NO = 419;
	const ERR_GIVE_UP_MSG = '丢弃任务失败';
	const ERR_COMPLETE_NO = 420;
	const ERR_COMPLETE_MSG = '完成任务失败';
	const ERR_PASSWORD_CANNOT_NUMERIC_NO = 421;
	const ERR_PASSWORD_CANNOT_NUMERIC_MSG = '密码不能为纯数字';
	const ERR_CANNOT_FIND_ROLE_NO = 422;
	const ERR_CANNOT_FIND_ROLE_MSG = '找不到对应角色';
	const ERR_MORE_THAN_LIMIT_NO = 423;
	const ERR_MORE_THAN_LIMIT_MSG = '融资申请总额超过申请额度';
	const ERR_ROLE_MEMBER_NOT_EMPTY_NO = 15006;
	const ERR_ROLE_MEMBER_NOT_EMPTY_MSG = '角色下用户数量大于0';
	const ERR_ROLE_USER_REPEAT_NO = 15007;
	const ERR_ROLE_USER_REPEAT_MSG = '用户重复添加';
}
