/*
Navicat MySQL Data Transfer

Source Server         : 3307
Source Server Version : 50719
Source Host           : 116.62.30.22:3307
Source Database       : anjie

Target Server Type    : MYSQL
Target Server Version : 50719
File Encoding         : 65001

Date: 2017-09-29 15:57:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for anjie_bank_mail
-- ----------------------------
DROP TABLE IF EXISTS `anjie_bank_mail`;
CREATE TABLE `anjie_bank_mail` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `status` varchar(2) DEFAULT NULL COMMENT '状态，0为成功，其他都是失败的',
  `url` varchar(2000) DEFAULT NULL COMMENT '请求地址',
  `param` varchar(2000) DEFAULT NULL COMMENT '传参',
  `response` varchar(2000) DEFAULT NULL COMMENT '响应结果',
  `transcode` varchar(50) DEFAULT NULL COMMENT '接口代码',
  `iretmsg` varchar(50) DEFAULT NULL COMMENT '返回信息',
  `work_id` varchar(50) DEFAULT NULL COMMENT '工作id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_category
-- ----------------------------
DROP TABLE IF EXISTS `anjie_category`;
CREATE TABLE `anjie_category` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `desc` varchar(50) DEFAULT '' COMMENT '描述',
  `is_menu` tinyint(4) DEFAULT '2' COMMENT '1是菜单，2不是',
  `menu_title` varchar(127) DEFAULT NULL COMMENT '作为菜单的显示名称',
  `sort` int(10) DEFAULT '0' COMMENT 'sort',
  `icon` varchar(100) DEFAULT NULL COMMENT 'icon',
  `status` varchar(2) DEFAULT '1' COMMENT '状态',
  `type` varchar(2) DEFAULT '1' COMMENT '1：林润审批系统，2：车商融',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=238 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_city
-- ----------------------------
DROP TABLE IF EXISTS `anjie_city`;
CREATE TABLE `anjie_city` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `cityname` varchar(50) NOT NULL COMMENT '城市名',
  `provinceid` int(11) NOT NULL COMMENT '省id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=392 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Table structure for anjie_file
-- ----------------------------
DROP TABLE IF EXISTS `anjie_file`;
CREATE TABLE `anjie_file` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `work_id` varchar(50) DEFAULT NULL COMMENT '业务ID',
  `file_class_id` varchar(5) DEFAULT NULL COMMENT '文件类型ID，对应Anjie_file_class表中的ID',
  `file_type` varchar(2) DEFAULT NULL COMMENT '文件类型，1为image，2为video',
  `file_type_name` varchar(10) DEFAULT NULL COMMENT '文件类型名：image、video',
  `file_path` varchar(80) DEFAULT NULL COMMENT '文件地址',
  `status` varchar(2) DEFAULT '1' COMMENT '状态。1为存在，2为已删除',
  `create_time` varchar(11) DEFAULT NULL COMMENT '创建时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `add_userid` varchar(22) DEFAULT NULL COMMENT '写入该图像的用户id',
  `delete_userid` varchar(22) DEFAULT NULL COMMENT '删除该图像的用户id',
  `file_id` varchar(50) DEFAULT NULL COMMENT '文件id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2216 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_file_class
-- ----------------------------
DROP TABLE IF EXISTS `anjie_file_class`;
CREATE TABLE `anjie_file_class` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `file_type` varchar(2) DEFAULT NULL COMMENT '文件类型：1为image 2为video',
  `file_name` varchar(30) DEFAULT NULL COMMENT '文件名，如：身份证',
  `file_e_name` varchar(30) DEFAULT NULL COMMENT '文件英文名，如：customer_identity_card',
  `file_type_name` varchar(10) DEFAULT NULL COMMENT '文件类型名：image、video',
  `status` varchar(2) DEFAULT '1' COMMENT '当前条目状态',
  `max_length` varchar(3) DEFAULT '100' COMMENT '该类型最大上传张数，默认100',
  `bank_catagory` varchar(20) DEFAULT NULL COMMENT '对应银行的图像种类',
  `min_length` varchar(3) DEFAULT '0' COMMENT '该类型最大上传张数，默认0',
  `jc_type` varchar(2) DEFAULT '1' COMMENT '1:风控审核，2：聚车贷',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_login
-- ----------------------------
DROP TABLE IF EXISTS `anjie_login`;
CREATE TABLE `anjie_login` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `account` varchar(11) DEFAULT NULL COMMENT '登录账号',
  `name` varchar(30) DEFAULT NULL COMMENT '登录者姓名',
  `login_ip` varchar(20) DEFAULT NULL COMMENT '登录IP',
  `login_time` varchar(11) DEFAULT NULL COMMENT '登录时间',
  `user_id` int(100) DEFAULT NULL COMMENT '对应anjie_users表中的id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3997 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_manaudit
-- ----------------------------
DROP TABLE IF EXISTS `anjie_manaudit`;
CREATE TABLE `anjie_manaudit` (
  `id` int(22) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `type` varchar(20) DEFAULT NULL COMMENT '类型',
  `product_name` varchar(50) DEFAULT NULL COMMENT '产品名称',
  `first_authority` varchar(50) DEFAULT NULL COMMENT '一级权限',
  `second_authority` varchar(50) DEFAULT NULL COMMENT '二级权限',
  `third_authority` varchar(50) DEFAULT NULL COMMENT '三级权限',
  `fourth_authority` varchar(50) DEFAULT NULL COMMENT '四级权限',
  `audit_type` varchar(2) NOT NULL COMMENT '审核类型，1为人工审核，2为用款审核',
  `sort` varchar(2) DEFAULT NULL COMMENT '排序',
  `status` varchar(2) DEFAULT '1' COMMENT '状态，为1时显示，为0时不显示',
  PRIMARY KEY (`id`,`audit_type`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_method
-- ----------------------------
DROP TABLE IF EXISTS `anjie_method`;
CREATE TABLE `anjie_method` (
  `method_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `method_name_cn` varchar(127) DEFAULT NULL COMMENT '名称_cn',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `cid` int(4) DEFAULT '0' COMMENT '分类',
  `status` tinyint(3) DEFAULT '1' COMMENT '接口状态',
  `create_time` int(11) DEFAULT '0' COMMENT '接口创建时间',
  `modify_time` int(11) DEFAULT '0' COMMENT '接口修改时间',
  `sort` int(10) DEFAULT '0' COMMENT '排序',
  `path` varchar(100) DEFAULT NULL COMMENT '访问路径',
  `is_bar` varchar(255) DEFAULT '0' COMMENT '是否是左边菜单栏的栏目,0不是，1是',
  `type` varchar(2) DEFAULT '1' COMMENT '1：林润审批系统，2：车商融',
  PRIMARY KEY (`method_id`)
) ENGINE=MyISAM AUTO_INCREMENT=280 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_origin
-- ----------------------------
DROP TABLE IF EXISTS `anjie_origin`;
CREATE TABLE `anjie_origin` (
  `id` int(22) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `merchant_class` varchar(20) DEFAULT NULL COMMENT '商户类别',
  `merchant_name` varchar(20) DEFAULT NULL COMMENT '商户名称',
  `merchant_class_id` int(20) DEFAULT NULL COMMENT '1为杭标',
  `merchant_number` varchar(30) DEFAULT NULL COMMENT '商户编号',
  `merchant_class_number` varchar(10) DEFAULT NULL COMMENT '商户类别编号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_privilege
-- ----------------------------
DROP TABLE IF EXISTS `anjie_privilege`;
CREATE TABLE `anjie_privilege` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `requestname` varchar(127) DEFAULT NULL COMMENT '请求名',
  `method_id` int(10) DEFAULT NULL COMMENT '方法id，对应anjie_method里面的method_id',
  `status` int(3) DEFAULT '1' COMMENT '接口状态',
  `create_time` int(11) DEFAULT '0' COMMENT '接口创建时间',
  `modify_time` int(11) DEFAULT '0' COMMENT '接口修改时间',
  `path` varchar(100) DEFAULT NULL,
  `not_check` int(2) DEFAULT '2' COMMENT '是否永远可以访问，为1时不需要检查，为2时需要检查',
  `type` varchar(2) DEFAULT '1' COMMENT '1：林润审批系统，2：车商融',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=178 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_product
-- ----------------------------
DROP TABLE IF EXISTS `anjie_product`;
CREATE TABLE `anjie_product` (
  `id` int(22) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `product_class` varchar(11) DEFAULT NULL COMMENT '产品类别',
  `product_name` varchar(11) DEFAULT NULL COMMENT '产品名称',
  `product_classs_id` int(11) DEFAULT NULL COMMENT '产品类别ID,1为新车，2为二手车',
  `product_number` varchar(22) DEFAULT NULL COMMENT '产品编号',
  `product_class_number` varchar(10) DEFAULT NULL COMMENT '产品类别编号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_province
-- ----------------------------
DROP TABLE IF EXISTS `anjie_province`;
CREATE TABLE `anjie_province` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `provincename` varchar(20) DEFAULT NULL COMMENT '省名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_sms_message
-- ----------------------------
DROP TABLE IF EXISTS `anjie_sms_message`;
CREATE TABLE `anjie_sms_message` (
  `id` int(22) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `account` varchar(11) DEFAULT NULL COMMENT '账号（手机号）',
  `type` varchar(2) DEFAULT NULL COMMENT '短信类别：1为注册，2为重置密码',
  `sms_code` varchar(6) DEFAULT NULL COMMENT '验证码',
  `over_time` varchar(11) DEFAULT NULL COMMENT '过期时间',
  `create_time` varchar(11) DEFAULT NULL COMMENT '创建时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `content` varchar(88) DEFAULT NULL COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=106 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_task
-- ----------------------------
DROP TABLE IF EXISTS `anjie_task`;
CREATE TABLE `anjie_task` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` varchar(11) DEFAULT NULL COMMENT '用户id',
  `role_id` varchar(11) DEFAULT NULL COMMENT '角色id',
  `process_id` varchar(11) DEFAULT NULL COMMENT '进程id',
  `process_instance_id` varchar(11) DEFAULT NULL COMMENT '任务实例id',
  `item_id` varchar(11) DEFAULT NULL COMMENT '任务id',
  `item_instance_id` varchar(11) DEFAULT NULL COMMENT '任务实例id',
  `task_instance_id` varchar(11) DEFAULT NULL COMMENT '任务实例id',
  `task_title` varchar(88) DEFAULT NULL COMMENT '任务名',
  `process_title` varchar(88) DEFAULT NULL COMMENT '流程名',
  `work_id` varchar(11) DEFAULT NULL COMMENT '业务id',
  `task_description` varchar(88) DEFAULT NULL COMMENT '当前任务描述',
  `create_time` varchar(11) DEFAULT NULL COMMENT '入库时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `status` varchar(2) DEFAULT NULL COMMENT '状态，1为认领了这个任务，2为完成了这个任务，3为放弃了这个任务，4为待认领',
  `task_status` varchar(2) DEFAULT NULL COMMENT '任务状态，1为通过，2为拒绝，3为待补件',
  `msg` varchar(100) DEFAULT '' COMMENT '其他的显示项',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1893 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_task_item
-- ----------------------------
DROP TABLE IF EXISTS `anjie_task_item`;
CREATE TABLE `anjie_task_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `title` varchar(30) DEFAULT NULL COMMENT '文字',
  `v1_item_id` varchar(10) NOT NULL COMMENT 'v1_process_item里面的id',
  `inquire` varchar(2) DEFAULT NULL COMMENT '征信结果',
  `from` varchar(2) DEFAULT NULL COMMENT '来源信息',
  `basic` varchar(2) DEFAULT NULL COMMENT '基本信息',
  `spouse` varchar(2) DEFAULT NULL COMMENT '配偶信息',
  `contact` varchar(2) DEFAULT NULL COMMENT '联系人信息',
  `bondsman` varchar(2) DEFAULT NULL COMMENT '中间商信息',
  `goods` varchar(2) DEFAULT NULL COMMENT '商品信息',
  `cost` varchar(2) DEFAULT NULL COMMENT '费用信息',
  `input_des` varchar(2) DEFAULT NULL COMMENT ' 备注信息',
  `role_id` varchar(10) NOT NULL COMMENT '角色id',
  `partition` varchar(2) NOT NULL COMMENT '是否分区的字段，1：分区，2：不分区',
  `button_title` varchar(50) DEFAULT '' COMMENT 'button的标题',
  `button_class` varchar(20) DEFAULT '' COMMENT 'button的class',
  PRIMARY KEY (`id`,`v1_item_id`,`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_users
-- ----------------------------
DROP TABLE IF EXISTS `anjie_users`;
CREATE TABLE `anjie_users` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `account` varchar(11) DEFAULT NULL COMMENT '登录账户（手机号）',
  `business_area` varchar(30) DEFAULT NULL COMMENT '业务区域',
  `name` varchar(30) DEFAULT NULL COMMENT '姓名',
  `passwd` varchar(32) DEFAULT NULL COMMENT '密码（md5之后的值）',
  `head_portrait` varchar(100) DEFAULT NULL COMMENT '头像',
  `is_valid` int(20) DEFAULT '1' COMMENT '是否有效',
  `last_logintime` int(11) DEFAULT NULL COMMENT '上次登录时间',
  `creat_time` int(11) DEFAULT NULL COMMENT '记录创建时间',
  `modify_time` int(11) DEFAULT NULL COMMENT '记录修改时间',
  `province` varchar(30) DEFAULT NULL COMMENT '省',
  `city` varchar(30) DEFAULT NULL COMMENT '市',
  `area_add` varchar(50) DEFAULT NULL COMMENT '补充的地区',
  `visit_lat` varchar(20) DEFAULT NULL COMMENT '家访人员所在纬度',
  `visit_lng` varchar(20) DEFAULT NULL COMMENT '家访人员所在经度',
  `visit_location` varchar(100) DEFAULT NULL COMMENT '家访员所在位置',
  `employee_number` varchar(50) DEFAULT NULL COMMENT '员工编号',
  `town` varchar(30) DEFAULT NULL COMMENT '区',
  `app_role` varchar(2) DEFAULT NULL COMMENT 'app端选择出来的角色，1为销售组，2为家访组',
  `type` varchar(2) DEFAULT '2' COMMENT '2:林润审批，3：聚车贷',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9184 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_visit
-- ----------------------------
DROP TABLE IF EXISTS `anjie_visit`;
CREATE TABLE `anjie_visit` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `work_id` varchar(50) DEFAULT NULL COMMENT '工作id',
  `visit_task_instance_id` varchar(20) DEFAULT NULL COMMENT '家访任务实例id',
  `supplement_task_instance_id` varchar(20) DEFAULT NULL COMMENT '补件任务实例id',
  `to_user_id` varchar(20) DEFAULT NULL COMMENT '被分配人的任务id',
  `from_user_id` varchar(20) DEFAULT '0' COMMENT '分配人的userid',
  `has_assign` varchar(2) DEFAULT '2' COMMENT '默认2未分配',
  `has_pickup` varchar(2) DEFAULT '2' COMMENT '默认2未认领',
  `supplement_status` varchar(2) DEFAULT '1' COMMENT '补件的状态，1不需要补件，2需要补件，3已补件',
  `visit_status` varchar(2) DEFAULT '2' COMMENT '2未家访，3已家访，1已分派所以不需要家访，4拒件',
  `product_class_number` varchar(2) DEFAULT NULL COMMENT '产品类别编号,XC:新车，ES:二手车',
  `type` varchar(2) DEFAULT NULL COMMENT '类型，1为家访，2为补件',
  `create_time` varchar(11) DEFAULT NULL COMMENT '创建时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `visit_date` varchar(8) DEFAULT NULL COMMENT '家访日期Ymd形式',
  `pick_up_userid` varchar(10) DEFAULT NULL COMMENT '认领的人的user_id',
  `pick_up_time` varchar(11) DEFAULT '0' COMMENT '认领时间',
  `credit_city` varchar(30) DEFAULT NULL COMMENT '征信申请业务员所在城市',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1526 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_visit_back
-- ----------------------------
DROP TABLE IF EXISTS `anjie_visit_back`;
CREATE TABLE `anjie_visit_back` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `work_id` varchar(50) DEFAULT NULL COMMENT '工作id',
  `to_user_id` varchar(10) DEFAULT NULL COMMENT '退回者的用户id',
  `description` varchar(100) DEFAULT NULL COMMENT '家访员退回原因',
  `create_time` varchar(11) DEFAULT NULL COMMENT '记录插入时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '记录修改时间',
  `type` varchar(2) DEFAULT NULL COMMENT '1为退回，2为拒件',
  `from_user_id` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_visit_message
-- ----------------------------
DROP TABLE IF EXISTS `anjie_visit_message`;
CREATE TABLE `anjie_visit_message` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `has_read` varchar(2) DEFAULT '2' COMMENT '是否已读，默认未读2,   1为已读，2为未读',
  `user_id` varchar(50) DEFAULT NULL COMMENT '用户id',
  `task_instance_id` varchar(50) DEFAULT NULL COMMENT '任务实例id',
  `create_time` varchar(11) DEFAULT NULL COMMENT '任务创建时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '任务修改时间',
  `work_id` varchar(50) DEFAULT NULL COMMENT '工作id',
  `type` varchar(2) DEFAULT NULL COMMENT '1为家访任务，2为补件任务',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1544 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for anjie_work
-- ----------------------------
DROP TABLE IF EXISTS `anjie_work`;
CREATE TABLE `anjie_work` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `customer_name` varchar(50) DEFAULT NULL COMMENT '客户姓名',
  `customer_telephone` varchar(11) DEFAULT NULL COMMENT '客户手机号码',
  `customer_sex` varchar(5) DEFAULT '0' COMMENT '客户性别',
  `customer_certificate_number` varchar(18) DEFAULT NULL COMMENT '身份证号码',
  `receiver_name` varchar(16) DEFAULT NULL COMMENT '受理人姓名',
  `receiver_telephone` varchar(16) DEFAULT NULL COMMENT '受理人电话',
  `merchant_id` varchar(5) DEFAULT NULL COMMENT '来源id',
  `product_id` varchar(5) DEFAULT NULL COMMENT '产品id',
  `merchant_name` varchar(20) DEFAULT NULL COMMENT '来源名称',
  `product_name` varchar(20) DEFAULT NULL COMMENT '产品名称',
  `status` varchar(1) DEFAULT '1' COMMENT '本条记录的状态，1为存在，0为逻辑删除',
  `create_time` varchar(11) DEFAULT NULL COMMENT '创建时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `merchant_no` varchar(50) DEFAULT NULL COMMENT '商机编号,anjie_origin.merchant_class_no+date(Ymd) + 00001',
  `product_no` varchar(50) DEFAULT NULL COMMENT '产品编号,anjie_product.product_number+00001',
  `request_no` varchar(50) DEFAULT NULL COMMENT '申请编号,anjie_product.product_class_no+四位随机数+0001',
  `product_class_number` varchar(20) DEFAULT '' COMMENT '产品类别编号,XC:新车，ES:二手车',
  `merchant_class_number` varchar(20) DEFAULT '' COMMENT '来源类型编号，HB:杭标',
  `constract_no` varchar(50) DEFAULT '' COMMENT '合同编号',
  `inquire_result` varchar(30) DEFAULT '' COMMENT '征信结果：正常、不正常',
  `inquire_description` varchar(100) DEFAULT '' COMMENT '征信备注',
  `visit_description` varchar(100) DEFAULT NULL COMMENT '拜访总结',
  `visit_status` varchar(2) DEFAULT '0' COMMENT '家访状态,0为未家访，1为通过，2为退回',
  `visit_date` varchar(16) DEFAULT '' COMMENT '家访日期',
  `visit_arrive_time` varchar(11) DEFAULT NULL COMMENT '家访人员抵达时间',
  `visit_leave_time` varchar(11) DEFAULT '' COMMENT '家访人员离开时间',
  `visit_arrive_lat` decimal(10,5) DEFAULT '0.00000' COMMENT '家访人员到达时所在纬度',
  `visit_arrive_lng` decimal(10,5) DEFAULT '0.00000' COMMENT '家访人员到达时所在经度',
  `visit_leave_lat` decimal(10,5) DEFAULT '0.00000' COMMENT '家访人员离开时所在纬度',
  `visit_leave_lng` decimal(10,5) DEFAULT '0.00000' COMMENT '家访人员离开时所在经度',
  `customer_address` varchar(100) DEFAULT '' COMMENT '客户的地址',
  `customer_province` varchar(30) DEFAULT NULL COMMENT '客户地址的省级',
  `customer_city` varchar(30) DEFAULT NULL COMMENT '客户地址的市级',
  `customer_town` varchar(30) DEFAULT NULL COMMENT '客户地址的县级',
  `customer_address_add` varchar(60) DEFAULT '' COMMENT '客户的地址多加的那部分',
  `visit_back_description` varchar(100) DEFAULT NULL COMMENT '家访退回意见',
  `loan_date` varchar(4) DEFAULT NULL COMMENT '贷款期数:(月)：3个月、6个月、12个月、24个月、36个月',
  `customer_marital_status` varchar(2) DEFAULT NULL COMMENT '婚姻状况，1为已婚，2为未婚',
  `customer_educational_status` varchar(4) DEFAULT NULL COMMENT '1:小学及以下，2：初中，3职高， 4：高中及以下，5：大学专科，6：大学本科，7：硕士研究生及以上',
  `customer_qq_number` varchar(16) DEFAULT NULL COMMENT 'qq号码',
  `customer_email_address` varchar(20) DEFAULT NULL COMMENT '电子邮箱',
  `customer_nationality` varchar(50) DEFAULT '' COMMENT '国籍',
  `customer_has_child` varchar(2) DEFAULT NULL COMMENT '是否有子女 1：有，2：无   ',
  `customer_has_local_house_property` varchar(2) DEFAULT NULL COMMENT '有无本地房产，1:有，2：没有',
  `loan_use` varchar(50) DEFAULT NULL COMMENT '贷款用途',
  `bound_automatic_repayment` varchar(2) DEFAULT NULL COMMENT '绑定自动还款:1绑定，2不绑定',
  `bondsman_name` varchar(40) DEFAULT NULL COMMENT '担保人姓名',
  `middleman` varchar(40) DEFAULT NULL COMMENT '中间商',
  `loan_bank` varchar(50) DEFAULT NULL COMMENT '贷款银行',
  `deposit_bank` varchar(50) DEFAULT NULL COMMENT '开户银行',
  `bank_account_name` varchar(50) DEFAULT NULL COMMENT '银行户名',
  `card_number` varchar(20) DEFAULT NULL COMMENT '打款卡号',
  `car_brand` varchar(50) DEFAULT NULL COMMENT '车辆品牌',
  `car_type` varchar(50) DEFAULT NULL COMMENT '车辆型号',
  `car_price` varchar(50) DEFAULT NULL COMMENT '车辆总价',
  `first_pay` varchar(50) DEFAULT NULL COMMENT '首付',
  `bill_price` varchar(50) DEFAULT NULL COMMENT '发票价格',
  `car_use` varchar(2) DEFAULT NULL COMMENT '1：自用；2：他用',
  `another_use_instruction` varchar(100) DEFAULT NULL COMMENT '他用说明',
  `first_license_date` varchar(40) DEFAULT NULL COMMENT '初次上牌日期',
  `license_way` varchar(2) DEFAULT NULL COMMENT '上牌方式:1、私牌；2、公牌',
  `evaluate_price` varchar(40) DEFAULT NULL COMMENT '评估价格',
  `evaluate_date` varchar(16) DEFAULT NULL COMMENT '评估日期',
  `evaluate_man` varchar(16) DEFAULT NULL COMMENT '评估人',
  `hire_type` varchar(4) DEFAULT NULL COMMENT '雇佣类型  1:受雇人士；2:自雇人士；3:应届毕业生；4:企业主',
  `customer_company_name` varchar(60) DEFAULT NULL COMMENT '单位名称',
  `customer_department` varchar(60) DEFAULT NULL COMMENT '部门',
  `customer_position` varchar(60) DEFAULT NULL COMMENT '职位',
  `customer_company_postcode` varchar(30) DEFAULT NULL COMMENT '单位邮编',
  `customer_company_area_code` varchar(10) DEFAULT NULL COMMENT '单位电话（区号）',
  `customer_company_phone_number` varchar(50) DEFAULT NULL COMMENT '单位电话（号码）',
  `customer_monthly_income` varchar(50) DEFAULT NULL COMMENT '当月收入（税后）',
  `customer_hiredate_year` varchar(20) DEFAULT NULL COMMENT '现单位入职时间（年）',
  `customer_hiredate_month` varchar(20) DEFAULT NULL COMMENT '现单位入职时间（月）',
  `customer_industry_nature` varchar(4) DEFAULT NULL COMMENT '行业性质1、制造业；2、批发/零售/贸易；3、金融业；4、能源；5、网路/信息服务/电子商务；6、酒店/旅游/餐饮；7、水利/环境/公共设施管理业；8、房地产/建筑业；9、政府机构；10、交通运输/仓储/物流；12、法律；13、商业咨询/顾问服务；14、卫生/社会保障/福利业；15、文化/体育/娱乐业；16、媒体/公关/出版业；17、教育/培训/科研；18、其他'',',
  `customer_economic_style` varchar(4) DEFAULT NULL COMMENT '经济类型   1:政府机关/事业单位；2:国有事业；3:个人经营/自由职业；4:民营企业；5:中外合资/中外合作/外商独资；6:其他',
  `company_address` varchar(100) DEFAULT NULL COMMENT '公司地址',
  `company_province` varchar(16) DEFAULT NULL COMMENT '单位所在省',
  `company_city` varchar(16) DEFAULT NULL COMMENT '单位所在市',
  `company_town` varchar(16) DEFAULT NULL COMMENT '单位所在区',
  `company_address_add` varchar(30) DEFAULT NULL COMMENT '公司地址多余的部分',
  `customer_main_income_source` varchar(4) DEFAULT NULL COMMENT '主要收入来源  1、工薪（工商银行代发工资）；2、工薪（他行代发工资）；3、现金；4、租金收入；5、经营收入；6、其他',
  `customer_income_description` varchar(100) DEFAULT NULL COMMENT '收入说明',
  `customer_card_address` varchar(60) DEFAULT NULL COMMENT '送卡地址  1:住宅地址；2:单位地址',
  `spouse_name` varchar(30) DEFAULT NULL COMMENT '配偶姓名',
  `spouse_telephone` varchar(11) DEFAULT NULL COMMENT '配偶手机号码',
  `spouse_certificate_number` varchar(20) DEFAULT NULL COMMENT '配偶的证件号码',
  `spouse_company_name` varchar(50) DEFAULT NULL COMMENT '配偶单位名称',
  `spouse_company_telephone` varchar(50) DEFAULT NULL COMMENT '配偶单位电话',
  `spouse_company_province` varchar(20) DEFAULT NULL COMMENT '配偶公司所在省',
  `spouse_company_city` varchar(20) DEFAULT NULL COMMENT '配偶公司所在市',
  `spouse_company_town` varchar(20) DEFAULT NULL COMMENT '配偶公司所在区',
  `spouse_company_address_add` varchar(50) DEFAULT '' COMMENT '配偶公司所在地址多加的部分',
  `spouse_monthly_average` varchar(30) DEFAULT NULL COMMENT '月均收入',
  `current_item_id` varchar(10) DEFAULT NULL COMMENT 'v1_process_item里面的id',
  `item_status` varchar(2) DEFAULT '1' COMMENT '状态。1为审核中，2为已完成, 3已拒件',
  `loan_prize` varchar(30) DEFAULT NULL COMMENT '贷款申请金额',
  `customer_certificate_valid_date` varchar(16) DEFAULT NULL COMMENT '证件有效期,截止日期',
  `customer_birthdate` varchar(16) DEFAULT NULL COMMENT '客户出生日期',
  `spouse_company_address` varchar(50) DEFAULT NULL COMMENT '配偶公司地址',
  `contacts_man_name` varchar(30) DEFAULT NULL COMMENT '联系人姓名',
  `contacts_man_relationship` varchar(2) DEFAULT NULL COMMENT '联系人关系1、父母，2、配偶，3、亲戚，4、其他',
  `contacts_man_telephone` varchar(11) DEFAULT NULL COMMENT '联系人手机号码',
  `contacts_man_phone_number` varchar(11) DEFAULT NULL COMMENT '联系人固定号码',
  `inputrequest_description` varchar(60) DEFAULT NULL COMMENT '申请录入的备注',
  `inquire_status` varchar(2) DEFAULT NULL COMMENT '征信报告状态 1:通过，2：拒件',
  `inputrequest_status` varchar(2) DEFAULT NULL COMMENT '申请录入状态：1:通过，2：拒件',
  `artificial_status` varchar(2) DEFAULT NULL COMMENT '人工审批1：通过，2：拒件，3：待补件',
  `finance_status` varchar(2) DEFAULT NULL COMMENT '财务打款状态，1：通过',
  `returnmoney_status` varchar(2) DEFAULT NULL COMMENT '回款确认状态 ，1：通过',
  `courier_status` varchar(2) DEFAULT NULL COMMENT '1：通过',
  `copytask_status` varchar(2) DEFAULT NULL COMMENT '抄单登记 1：通过',
  `gps_status` varchar(2) DEFAULT NULL COMMENT '车辆GPS登记状态 1：通过',
  `mortgage_status` varchar(2) DEFAULT NULL COMMENT '抵押登记状态 1：通过',
  `call_status` varchar(2) DEFAULT NULL COMMENT '电核状态，1：通过，2、拒绝，3：补件',
  `artificial_description` varchar(100) DEFAULT NULL COMMENT '人工审批备注',
  `call_description` varchar(100) DEFAULT NULL COMMENT '电核备注',
  `call_refuse_reason` varchar(100) DEFAULT NULL COMMENT '电核拒绝原因 (逗号分割)1、客户否认申请，2、非本人签名，3、申请人主动取消申请，4、黑名单，5、人行征信有不良记录，6、申请人不配合调查，7、公安网信息有误，8、无法联系申请人，9、其他',
  `visiter_supplement` varchar(30) DEFAULT NULL COMMENT '家访补件（ (逗号分割)）1、身份证，2、收入证明,3、征信授权书',
  `salesman_supplement` varchar(30) DEFAULT NULL COMMENT '销售补件 (逗号分割)1、身份证，2、收入证明,3、征信授权书',
  `constract_prize` varchar(30) DEFAULT NULL COMMENT '合同金额',
  `remittance_prize` varchar(30) DEFAULT NULL COMMENT '打款金额',
  `remittance_man` varchar(50) DEFAULT NULL COMMENT '打款人',
  `remittance_time` varchar(30) DEFAULT NULL COMMENT '打款时间',
  `remittance_card` varchar(40) DEFAULT NULL COMMENT '收款账户',
  `finance_description` varchar(100) DEFAULT NULL COMMENT '财务打款备注',
  `return_prize` varchar(30) DEFAULT NULL COMMENT '回款金额',
  `return_time` varchar(11) DEFAULT NULL COMMENT '回款时间',
  `return_card` varchar(30) DEFAULT NULL COMMENT '回款账户',
  `return_confirm_time` varchar(11) DEFAULT NULL COMMENT '回款确认时间',
  `return_description` varchar(50) DEFAULT NULL COMMENT '回款备注',
  `courier_man` varchar(30) DEFAULT NULL COMMENT '寄件人',
  `courier_business` varchar(30) DEFAULT NULL COMMENT '快递商',
  `courier_number` varchar(30) DEFAULT NULL COMMENT '快递单号',
  `courier_time` varchar(11) DEFAULT NULL COMMENT '寄件时间',
  `courier_description` varchar(50) DEFAULT NULL COMMENT '寄件备注',
  `copytask_courier_man` varchar(30) DEFAULT NULL COMMENT '抄单寄件人',
  `copytask_courier_business` varchar(50) DEFAULT NULL COMMENT '抄单快递商',
  `copytask_courier_number` varchar(30) DEFAULT NULL COMMENT '抄单快递单号',
  `copytask_courier_time` varchar(11) DEFAULT NULL COMMENT '抄单寄件时间',
  `copytask_courier_description` varchar(50) DEFAULT NULL COMMENT '抄单寄件备注',
  `license_number` varchar(40) DEFAULT NULL COMMENT '车牌号',
  `gps_number` varchar(40) DEFAULT NULL COMMENT 'GPS编号',
  `install_man` varchar(40) DEFAULT NULL COMMENT '安装人',
  `install_time` varchar(20) DEFAULT NULL COMMENT '安装时间',
  `gps_description` varchar(100) DEFAULT NULL COMMENT 'gps登记备注',
  `is_mortgage` varchar(6) DEFAULT NULL COMMENT '是否办理抵押,1是，2不是',
  `mandate_number` varchar(30) DEFAULT NULL COMMENT '委托书编号',
  `transactor` varchar(40) DEFAULT NULL COMMENT '办理人',
  `transact_time` varchar(11) DEFAULT NULL COMMENT '办理时间',
  `mortgage_description` varchar(50) DEFAULT NULL COMMENT '抵押备注',
  `salessupplement_status` varchar(2) DEFAULT '2' COMMENT '补件状态,1需要补件，2不需要补件',
  `salesman_id` varchar(30) DEFAULT NULL COMMENT '销售人员的id',
  `visitor_id` varchar(30) DEFAULT '' COMMENT '家访人员的id',
  `customer_age` varchar(30) DEFAULT NULL COMMENT '客户年龄',
  `customer_has_bondsman` varchar(2) DEFAULT NULL COMMENT '是否有担保人，1为有担保人，2为无担保人',
  `car_vehicle_identification_number` varchar(40) DEFAULT NULL COMMENT '车架号',
  `first_pay_ratio` varchar(30) DEFAULT NULL COMMENT '首付比例',
  `loan_rate` varchar(20) DEFAULT NULL COMMENT '贷款利率',
  `has_insurance` varchar(2) DEFAULT NULL COMMENT '有无保险，1为有，2为没有',
  `insurance_company` varchar(40) DEFAULT NULL COMMENT '保险公司',
  `commercial_insurance` varchar(40) DEFAULT NULL COMMENT '商业险',
  `compulsory_insurance` varchar(40) DEFAULT NULL COMMENT '交强险',
  `vehicle_vessel_tax` varchar(40) DEFAULT NULL COMMENT '车船税',
  `gross_premium` varchar(40) DEFAULT NULL COMMENT '保费总额',
  `total_expense` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '费用合计',
  `bondsman_certificate_number` varchar(40) DEFAULT NULL COMMENT '担保人身份证号',
  `bondsman_telephone` varchar(20) DEFAULT NULL COMMENT '担保人电话号码',
  `bondsman_company_name` varchar(60) DEFAULT NULL COMMENT '担保人公司名称',
  `bondsman_company_telephone` varchar(20) DEFAULT NULL COMMENT '担保人公司电话',
  `bondsman_company_address` varchar(60) DEFAULT NULL COMMENT '担保人公司地址',
  `hukou` varchar(100) DEFAULT NULL COMMENT '户口所在地',
  `artificial_refuse_reason` varchar(50) DEFAULT NULL COMMENT '人工审核拒绝原因',
  `visit_arrive_address` varchar(100) DEFAULT NULL COMMENT '家访抵达地址',
  `visit_leave_address` varchar(100) DEFAULT NULL COMMENT '家访离开地址',
  `contacts_man_certificate_number` varchar(50) DEFAULT NULL COMMENT '联系人身份证号码',
  `artificialtwo_status` varchar(2) DEFAULT NULL COMMENT '人工审批2：通过，2：拒件，3：待补件',
  `artificialtwo_refuse_reason` varchar(100) DEFAULT NULL COMMENT '人工二审拒绝理由',
  `artificialtwo_description` varchar(100) DEFAULT '' COMMENT '二审备注',
  `loan_principal` varchar(40) DEFAULT NULL COMMENT '本金',
  `finance_name` varchar(50) DEFAULT NULL COMMENT '打款户名',
  `finance_driving` varchar(50) DEFAULT NULL COMMENT '打给车行',
  `finance_account` varchar(50) DEFAULT NULL COMMENT '账号',
  `finance_deposit_bank` varchar(50) DEFAULT NULL COMMENT '开户行',
  `finance_amount` varchar(40) DEFAULT NULL COMMENT '打款金额',
  `finance_date` varchar(16) DEFAULT NULL COMMENT '打款日期',
  `finance_apply_description` varchar(50) DEFAULT NULL COMMENT '申请打款备注',
  `applyremittance_status` varchar(2) DEFAULT NULL COMMENT '申请打款状态',
  `gps_number2` varchar(30) DEFAULT NULL COMMENT 'GPS编号2',
  `install_position` varchar(60) DEFAULT NULL COMMENT '安装位置',
  `credit_city` varchar(60) DEFAULT NULL COMMENT '征信申请的城市',
  `moneyaudit_status` varchar(2) DEFAULT NULL COMMENT '打款审核的状态',
  `artificial_supplement_user_id` int(50) DEFAULT NULL COMMENT '审核中，打回待补件的审核人员的user_id，方便补完件时，回到该审核人员',
  `fee` varchar(20) DEFAULT NULL COMMENT '手续费=贷款金额*利率/100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=678 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for api2_category
-- ----------------------------
DROP TABLE IF EXISTS `api2_category`;
CREATE TABLE `api2_category` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cate_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'cate的分类',
  `cate_name` varchar(60) NOT NULL COMMENT '类别名称',
  `desc` varchar(50) DEFAULT '' COMMENT '描述',
  `is_menu` tinyint(4) NOT NULL DEFAULT '2' COMMENT '1是菜单，2不是',
  `menu_title` varchar(127) DEFAULT NULL COMMENT '作为菜单的显示名称',
  `sort` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=336 DEFAULT CHARSET=utf8 COMMENT='接口_V2_分类表';

-- ----------------------------
-- Table structure for api2_field
-- ----------------------------
DROP TABLE IF EXISTS `api2_field`;
CREATE TABLE `api2_field` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` smallint(5) unsigned DEFAULT '0' COMMENT '接口(method) ID',
  `obj_id` smallint(6) DEFAULT '0' COMMENT '系统输入 | 应用输入 | 接口返回 | 字段(对象) 名称',
  `field_name` varchar(30) NOT NULL DEFAULT '' COMMENT '字段 (对象) 类型名称',
  `is_necessary` tinyint(3) unsigned DEFAULT '0' COMMENT '是否必须',
  `example` varchar(200) DEFAULT NULL COMMENT '示例值',
  `default_value` varchar(15) DEFAULT '' COMMENT '默认值',
  `description` varchar(1000) DEFAULT '' COMMENT '描述',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`field_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1173 DEFAULT CHARSET=utf8 COMMENT='接口_V2_输入输出(对象)字段';

-- ----------------------------
-- Table structure for api2_log
-- ----------------------------
DROP TABLE IF EXISTS `api2_log`;
CREATE TABLE `api2_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(22) DEFAULT NULL COMMENT '用户ID',
  `username` varchar(255) DEFAULT NULL COMMENT '用户登录名',
  `action` varchar(40) NOT NULL COMMENT 'API名称',
  `url` varchar(2000) DEFAULT NULL,
  `error_no` int(11) DEFAULT NULL,
  `msg` mediumtext COMMENT '日志信息',
  `script_time` bigint(11) DEFAULT NULL COMMENT 'API执行时间',
  `create_time` varchar(11) NOT NULL COMMENT '日志时间',
  `work_id` varchar(60) DEFAULT NULL COMMENT '工作id',
  `customer_certificate_number` varchar(18) DEFAULT NULL COMMENT '身份证号码',
  `merchant_no` varchar(30) DEFAULT NULL COMMENT '商户编号',
  `ip` varchar(30) DEFAULT NULL COMMENT '操作的ip地址',
  `path` varchar(60) DEFAULT NULL COMMENT '请求地址,对应到anjie_privilege',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15005 DEFAULT CHARSET=utf8 COMMENT='API2日志';

-- ----------------------------
-- Table structure for api2_method
-- ----------------------------
DROP TABLE IF EXISTS `api2_method`;
CREATE TABLE `api2_method` (
  `method_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `method_name_en` varchar(127) NOT NULL COMMENT '名称_en',
  `method_name_cn` varchar(127) NOT NULL COMMENT '名称_cn',
  `system_input_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '系统级输入',
  `application_input_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '应用级输入',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  `response_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '返回ID',
  `cid` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '分类',
  `post_method` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '提交方式 (0:all 1:post 2:get)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '接口状态',
  `check_permission` tinyint(4) NOT NULL DEFAULT '2' COMMENT '1需要检查权限，2不需要检查权限',
  `create_time` int(11) DEFAULT '0' COMMENT '接口创建时间',
  `modify_time` int(11) DEFAULT '0' COMMENT '接口修改时间',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`method_id`)
) ENGINE=MyISAM AUTO_INCREMENT=322 DEFAULT CHARSET=utf8 COMMENT='接口_V2_方法表';

-- ----------------------------
-- Table structure for api2_token
-- ----------------------------
DROP TABLE IF EXISTS `api2_token`;
CREATE TABLE `api2_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1有效；2无效',
  `token` varchar(50) NOT NULL,
  `over_time` int(4) DEFAULT NULL,
  `refresh_token` varchar(50) DEFAULT NULL,
  `refresh_time` int(4) DEFAULT NULL,
  `device_id` varchar(4) NOT NULL,
  `device` varchar(127) DEFAULT NULL COMMENT '登录的设备信息',
  `from` varchar(10) NOT NULL DEFAULT '' COMMENT '登录来源，分phone,pad,web',
  `cache` mediumtext,
  `ip` varchar(32) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `modify_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_token` (`token`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4229 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for bill_file
-- ----------------------------
DROP TABLE IF EXISTS `bill_file`;
CREATE TABLE `bill_file` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `bill_id` varchar(50) DEFAULT NULL COMMENT 'jcr_verify里的id',
  `file_id` varchar(50) DEFAULT NULL COMMENT '文件id',
  `file_type_name` varchar(10) DEFAULT NULL COMMENT '文件类型名：image、video',
  `file_path` varchar(80) DEFAULT NULL COMMENT '文件地址',
  `status` varchar(2) DEFAULT '1' COMMENT '状态。1为存在，2为已删除，3暂存',
  `file_class_id` varchar(10) DEFAULT NULL COMMENT '文件类型ID，对应Anjie_file_class表中的ID',
  `file_type` varchar(2) DEFAULT NULL COMMENT '文件类型，1为image，2为video',
  `create_time` varchar(11) DEFAULT NULL COMMENT '创建时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `add_userid` varchar(50) DEFAULT NULL COMMENT '写入的userid，对应jcr_users里面的id',
  `delete_userid` varchar(50) DEFAULT NULL COMMENT '删除的userid，对应jcr_users里面的id',
  `ifcar99_path` varchar(100) DEFAULT NULL COMMENT '金融平台返回的图片路径',
  `filename` varchar(100) DEFAULT NULL COMMENT '文件名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=635 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for car_brands
-- ----------------------------
DROP TABLE IF EXISTS `car_brands`;
CREATE TABLE `car_brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brands_en` varchar(255) DEFAULT NULL,
  `brands_cn` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for car_config
-- ----------------------------
DROP TABLE IF EXISTS `car_config`;
CREATE TABLE `car_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vendor` varchar(255) DEFAULT NULL COMMENT '厂商',
  `level` varchar(255) DEFAULT NULL COMMENT '级别',
  `engine` varchar(255) DEFAULT NULL COMMENT '发动机',
  `transmission_case` varchar(255) DEFAULT NULL,
  `mechanism` varchar(255) DEFAULT NULL,
  `LWH` varchar(255) DEFAULT NULL,
  `wheelbase` varchar(255) DEFAULT NULL,
  `luggage_space` varchar(255) DEFAULT NULL,
  `quality` varchar(255) DEFAULT NULL,
  `displacement` varchar(255) DEFAULT NULL,
  `Inlet_form` varchar(255) DEFAULT NULL,
  `cylinder` varchar(255) DEFAULT NULL,
  `horsepower` varchar(255) DEFAULT NULL,
  `torque` varchar(255) DEFAULT NULL,
  `fuel_type` varchar(255) DEFAULT NULL,
  `fuel_supply_mode` varchar(255) DEFAULT NULL,
  `fuel_label` varchar(255) DEFAULT NULL,
  `emission_standard` varchar(255) DEFAULT NULL,
  `drive_mode` varchar(255) DEFAULT NULL,
  `power_type` varchar(255) DEFAULT NULL,
  `front_suspension_type` varchar(255) DEFAULT NULL,
  `rear_suspension_type` varchar(255) DEFAULT NULL,
  `front_brake_type` varchar(255) DEFAULT NULL,
  `rear_brake_type` varchar(255) DEFAULT NULL,
  `parking_brake_type` varchar(255) DEFAULT NULL,
  `front_tyre_specification` varchar(255) DEFAULT NULL,
  `rear_tire_specification` varchar(255) DEFAULT NULL,
  `master_and_copilot_airbag` varchar(255) DEFAULT NULL,
  `front_row_side_airbag` varchar(255) DEFAULT NULL,
  `front_and_rear_airbag` varchar(255) DEFAULT NULL,
  `front_row_head_airbag` varchar(255) DEFAULT NULL,
  `tire_pressure_monitoring` varchar(255) DEFAULT NULL,
  `car_lock` varchar(255) DEFAULT NULL,
  `child_seat_interface` varchar(255) DEFAULT NULL,
  `keyless_start` varchar(255) DEFAULT NULL,
  `ABS` varchar(255) DEFAULT NULL,
  `ESP` varchar(255) DEFAULT NULL,
  `power_sunroof` varchar(255) DEFAULT NULL,
  `panoramic_sunroof` varchar(255) DEFAULT NULL,
  `drl` varchar(255) DEFAULT NULL,
  `steering_auxiliary_light` varchar(255) DEFAULT NULL,
  `automatic_headlamp` varchar(255) DEFAULT NULL,
  `front_fog_lamp` varchar(255) DEFAULT NULL,
  `front_rear_power_window` varchar(255) DEFAULT NULL,
  `electric_adjustment_of_rearview_mirror` varchar(255) DEFAULT NULL,
  `rearview_mirror_heating` varchar(255) DEFAULT NULL,
  `seat_material` varchar(255) DEFAULT NULL,
  `front_rear_seat_heating` varchar(255) DEFAULT NULL,
  `front_rear_seat_ ventilation` varchar(255) DEFAULT NULL,
  `multifunctional_steering_wheel` varchar(255) DEFAULT NULL,
  `cruise_control` varchar(255) DEFAULT NULL,
  `reversing_radar` varchar(255) DEFAULT NULL,
  `reverse_image_system` varchar(255) DEFAULT NULL,
  `Air-conditioning` varchar(255) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2653 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for car_files
-- ----------------------------
DROP TABLE IF EXISTS `car_files`;
CREATE TABLE `car_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `file` varchar(255) DEFAULT NULL,
  `carId` int(11) DEFAULT NULL,
  `get` tinyint(4) DEFAULT '0',
  `new_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68874 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for car_model
-- ----------------------------
DROP TABLE IF EXISTS `car_model`;
CREATE TABLE `car_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '车系名称',
  `acronym` varchar(255) NOT NULL COMMENT '首字母缩写',
  `factory` varchar(255) DEFAULT NULL COMMENT '工厂',
  `brand_acronym` varchar(255) DEFAULT NULL COMMENT '品牌首字母缩写',
  `icon` varchar(255) DEFAULT NULL COMMENT '图片路径',
  `brand_id` tinyint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1524 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for car_recommend
-- ----------------------------
DROP TABLE IF EXISTS `car_recommend`;
CREATE TABLE `car_recommend` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recommend_id` int(11) NOT NULL,
  `addtime` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for car_source
-- ----------------------------
DROP TABLE IF EXISTS `car_source`;
CREATE TABLE `car_source` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `photo` varchar(255) DEFAULT NULL,
  `brandName` varchar(255) DEFAULT NULL,
  `modelName` varchar(255) DEFAULT NULL,
  `styleName` varchar(255) DEFAULT NULL,
  `firstRegDate` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `kmNum` int(11) DEFAULT NULL,
  `cityName` varchar(255) DEFAULT NULL,
  `sellStyle` varchar(255) DEFAULT NULL,
  `originalPrice` decimal(10,2) DEFAULT NULL,
  `gearbox` varchar(11) DEFAULT NULL,
  `carSourceNo` varchar(11) DEFAULT NULL,
  `brandAcronym` varchar(255) DEFAULT NULL,
  `modelAcronym` varchar(255) DEFAULT NULL,
  `cityAcronym` varchar(255) DEFAULT NULL,
  `cc` varchar(255) DEFAULT NULL COMMENT '排量',
  `color` varchar(255) DEFAULT NULL COMMENT '车身颜色',
  `city` varchar(255) DEFAULT NULL,
  `photos` text,
  `content` text,
  `old_photos` text,
  `brandId` int(11) DEFAULT NULL,
  `modelId` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `levelId` tinyint(2) DEFAULT NULL,
  `emission_standard` tinyint(2) DEFAULT '0',
  `less_mileage` tinyint(2) DEFAULT '0',
  `is_new` tinyint(2) DEFAULT NULL,
  `styleId` int(11) DEFAULT NULL,
  `is_del` tinyint(1) DEFAULT '1',
  `search` int(11) DEFAULT '0',
  `new_photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=481743 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for csr_file
-- ----------------------------
DROP TABLE IF EXISTS `csr_file`;
CREATE TABLE `csr_file` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `csr_id` varchar(50) DEFAULT NULL COMMENT 'jcr_csr里的id',
  `file_id` varchar(50) DEFAULT NULL COMMENT '文件id',
  `file_type_name` varchar(10) DEFAULT NULL COMMENT '文件类型名：image、video',
  `file_path` varchar(80) DEFAULT NULL COMMENT '文件地址',
  `status` varchar(2) DEFAULT '1' COMMENT '状态。1为存在，2为已删除，3暂存',
  `file_class_id` varchar(10) DEFAULT NULL COMMENT '文件类型ID，对应Anjie_file_class表中的ID',
  `file_type` varchar(2) DEFAULT NULL COMMENT '文件类型，1为image，2为video',
  `create_time` varchar(11) DEFAULT NULL COMMENT '创建时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `add_userid` varchar(50) DEFAULT NULL COMMENT '写入的userid，对应jcr_users里面的id',
  `delete_userid` varchar(50) DEFAULT NULL COMMENT '删除的userid，对应jcr_users里面的id',
  `ifcar99_path` varchar(100) DEFAULT NULL COMMENT '金融平台返回的路径',
  `filename` varchar(100) DEFAULT NULL COMMENT '文件名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=354 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for file_upload
-- ----------------------------
DROP TABLE IF EXISTS `file_upload`;
CREATE TABLE `file_upload` (
  `id` int(200) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `file_token` varchar(100) DEFAULT NULL COMMENT '文件token',
  `token_expired_in` varchar(100) DEFAULT NULL COMMENT 'token的有效期',
  `path` varchar(100) DEFAULT NULL COMMENT '本地服务器访问文件的地址',
  `sync_to_upyun_status` varchar(2) DEFAULT '0' COMMENT '传到Upyun服务器上的状态,0为未上传，1为上传完成，-1为文件不存在，2为上传失败',
  `upyun_uploads_offset` varchar(50) DEFAULT '0' COMMENT '已经传到upyun的序号',
  `upyun_uuid` varchar(50) DEFAULT NULL COMMENT 'upyun上传的唯一标识',
  `sync_to_upyun` tinyint(4) DEFAULT '2' COMMENT '1同步至upyun，2不同步',
  `user_id` int(11) DEFAULT '0' COMMENT '文件拥有者ID',
  `create_time` int(11) DEFAULT '0',
  `size` int(11) DEFAULT '0' COMMENT '文件大小',
  `suffix` varchar(15) DEFAULT '' COMMENT '文件后缀',
  `file_id` varchar(255) DEFAULT NULL COMMENT '文件id',
  `type` varchar(31) DEFAULT NULL COMMENT '文件类别，video，image等等',
  `is_end` varchar(2) DEFAULT '0' COMMENT '移动端的文件上传是否完成',
  `sync_to_bank` varchar(2) DEFAULT '2' COMMENT '1同步至银行服务器，2不同步',
  `sync_to_bank_status` varchar(2) DEFAULT '0' COMMENT '传到银行服务器上的状态,0为未上传，1为上传完成，-1为文件不存在，2为上传失败',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4393 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jcr_bill
-- ----------------------------
DROP TABLE IF EXISTS `jcr_bill`;
CREATE TABLE `jcr_bill` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `csr_id` int(100) DEFAULT NULL COMMENT '车商融id',
  `carbrand` varchar(50) DEFAULT NULL COMMENT '车辆品牌',
  `cartype` varchar(2) DEFAULT NULL COMMENT '1：新车，2：二手车',
  `money` varchar(50) DEFAULT '' COMMENT '融资金额',
  `create_time` varchar(11) DEFAULT NULL COMMENT '入库时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `billrequest_status` varchar(2) DEFAULT NULL COMMENT '标的申请状态，1：已申请',
  `finance_bill_id` varchar(100) DEFAULT NULL COMMENT '聚车金融返回的标的id',
  `status` varchar(30) DEFAULT '0' COMMENT '审核成功 VERIFY_SUCCESS 审核失败    VERIFY_FAILED 上标成功    ONLINE_SUCCESS 用户投资    USER_TENDER 放款成功    PAY_SUCCESS  还款提醒    REPAY_NOTICE  还款成功    REPAY_SUCCESS',
  `finance_amount` varchar(30) DEFAULT '0' COMMENT '已融资金额',
  `total_finance` varchar(30) DEFAULT '0' COMMENT '融资总金额',
  `has_loan` varchar(10) DEFAULT '2' COMMENT '1:已放款。2：未放款',
  `repay_time` varchar(11) DEFAULT NULL COMMENT '还款时间',
  `borrow_title` varchar(50) DEFAULT NULL COMMENT '标的编号',
  `has_transfer` varchar(2) DEFAULT '2' COMMENT '1:已过户，2：未过户',
  `has_repay` varchar(2) DEFAULT '2' COMMENT '1:已还款，2：未还款',
  `repay_account_yes` varchar(50) DEFAULT NULL COMMENT '已还款金额',
  `repay_last_time` varchar(50) DEFAULT '' COMMENT '最后还款日',
  `repay_type` varchar(5) DEFAULT '' COMMENT '1：提前还款，2：到期还款，3：逾期还款',
  `repay_end_time` varchar(50) DEFAULT '' COMMENT '最后还款日',
  `repay_account` varchar(50) DEFAULT '' COMMENT '应还款金额',
  `is_delete` varchar(2) DEFAULT '2' COMMENT '1：已删除，2：未删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=374 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jcr_csr
-- ----------------------------
DROP TABLE IF EXISTS `jcr_csr`;
CREATE TABLE `jcr_csr` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` varchar(50) DEFAULT NULL COMMENT '用户id',
  `money_request` varchar(50) DEFAULT '' COMMENT '融资金额',
  `car_stock_request` varchar(30) DEFAULT '' COMMENT '库存车辆',
  `deadline_request` varchar(40) DEFAULT '' COMMENT '融资期限',
  `rate_request` varchar(40) DEFAULT '' COMMENT '融资利率',
  `money` varchar(50) DEFAULT '',
  `car_stock` varchar(30) DEFAULT '' COMMENT '库存车辆',
  `deadline` varchar(40) DEFAULT '' COMMENT '融资期限',
  `rate` varchar(40) DEFAULT '' COMMENT '融资利率',
  `create_time` varchar(11) DEFAULT '' COMMENT '入库时间',
  `modify_time` varchar(11) DEFAULT '' COMMENT '修改时间',
  `status` varchar(2) DEFAULT '1' COMMENT '本条记录的状态，1为存在，0为逻辑删除',
  `csr_no` varchar(50) DEFAULT '' COMMENT '融资单号',
  `crsrequest_status` varchar(20) DEFAULT '0' COMMENT '车商融申请结果：1通过，2拒绝',
  `item_status` varchar(2) DEFAULT '1' COMMENT '状态。1为审核中，2为已完成, 3已拒件',
  `csrrequestverfiy_time` varchar(11) DEFAULT NULL COMMENT '融资申请审核时间',
  `has_bill` varchar(2) DEFAULT '2' COMMENT '1:已上标，2：未上标',
  `has_loan` varchar(2) DEFAULT '3' COMMENT '已放款，1：已完全放款，2：未完全放款，3：没有放款',
  `billrequest_time` varchar(11) DEFAULT NULL COMMENT '标的申请时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=254 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jcr_file
-- ----------------------------
DROP TABLE IF EXISTS `jcr_file`;
CREATE TABLE `jcr_file` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `verify_id` varchar(50) DEFAULT NULL COMMENT 'jcr_verify里的id',
  `file_id` varchar(50) DEFAULT NULL COMMENT '文件id',
  `file_type_name` varchar(10) DEFAULT NULL COMMENT '文件类型名：image、video',
  `file_path` varchar(80) DEFAULT NULL COMMENT '文件地址',
  `status` varchar(2) DEFAULT '1' COMMENT '状态。1为存在，2为已删除，3暂存',
  `file_class_id` varchar(10) DEFAULT NULL COMMENT '文件类型ID，对应Anjie_file_class表中的ID',
  `file_type` varchar(2) DEFAULT NULL COMMENT '文件类型，1为image，2为video',
  `create_time` varchar(11) DEFAULT NULL COMMENT '创建时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `add_userid` varchar(50) DEFAULT NULL COMMENT '写入的userid，对应jcr_users里面的id',
  `delete_userid` varchar(50) DEFAULT NULL COMMENT '删除的userid，对应jcr_users里面的id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=503 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jcr_suggestions
-- ----------------------------
DROP TABLE IF EXISTS `jcr_suggestions`;
CREATE TABLE `jcr_suggestions` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `suggestions` varchar(1024) DEFAULT '' COMMENT '建议',
  `user_id` varchar(100) DEFAULT NULL COMMENT '用户id',
  `create_time` varchar(11) DEFAULT NULL COMMENT '创建时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jcr_task
-- ----------------------------
DROP TABLE IF EXISTS `jcr_task`;
CREATE TABLE `jcr_task` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` varchar(11) DEFAULT NULL COMMENT '用户id',
  `role_id` varchar(11) DEFAULT NULL COMMENT '角色id',
  `process_id` varchar(11) DEFAULT NULL COMMENT '进程id',
  `process_instance_id` varchar(11) DEFAULT NULL COMMENT '任务实例id',
  `item_id` varchar(11) DEFAULT NULL COMMENT '任务id',
  `item_instance_id` varchar(11) DEFAULT NULL COMMENT '任务实例id',
  `task_instance_id` varchar(11) DEFAULT NULL COMMENT '任务实例id',
  `task_title` varchar(88) DEFAULT NULL COMMENT '任务名',
  `process_title` varchar(88) DEFAULT NULL COMMENT '流程名',
  `csr_id` varchar(11) DEFAULT NULL COMMENT '业务id',
  `task_description` varchar(88) DEFAULT NULL COMMENT '当前任务描述',
  `create_time` varchar(11) DEFAULT NULL COMMENT '入库时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `status` varchar(2) DEFAULT NULL COMMENT '状态，1为认领了这个任务，2为完成了这个任务，3为放弃了这个任务，4为待认领',
  `task_status` varchar(2) DEFAULT NULL COMMENT '任务状态，1为通过，2为拒绝，3为待补件',
  `msg` varchar(100) DEFAULT '' COMMENT '其他的显示项',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3373 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jcr_users
-- ----------------------------
DROP TABLE IF EXISTS `jcr_users`;
CREATE TABLE `jcr_users` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `token` varchar(100) NOT NULL COMMENT '用于登录的token',
  `loginname` varchar(50) DEFAULT NULL COMMENT '登录的账号',
  `from` varchar(40) DEFAULT NULL COMMENT 'web,phone,pad等',
  `device` varchar(50) DEFAULT NULL COMMENT '设备名称',
  `user_id` varchar(100) DEFAULT NULL COMMENT '用户id',
  `realname` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT '' COMMENT '姓名',
  `head_portrait` varchar(100) DEFAULT '' COMMENT '头像',
  `verify_status` varchar(2) DEFAULT '2' COMMENT '1:已认证，2：未认证，3：认证中，4：认证失败',
  `create_time` varchar(11) DEFAULT NULL COMMENT '入库时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `over_time` varchar(11) DEFAULT NULL COMMENT 'token超时时间',
  `bankaccount_status` varchar(2) DEFAULT '2' COMMENT '1:开通了银行存管，2：没有开通银行存管',
  `accountId` varchar(50) DEFAULT '' COMMENT '电子账号',
  `acqRes` varchar(200) DEFAULT '',
  `id_No` varchar(50) DEFAULT '' COMMENT '身份证号码',
  `card_No` varchar(50) DEFAULT '' COMMENT '银行卡号',
  `channel` varchar(30) DEFAULT '' COMMENT '000001手机APP 000002网页 000003微信',
  `finance_account_sub_id` varchar(100) DEFAULT NULL COMMENT '融资子账户id',
  `has_transfer` varchar(10) DEFAULT '2' COMMENT '是否已过户1：已过户，2：未过户',
  PRIMARY KEY (`id`,`token`)
) ENGINE=InnoDB AUTO_INCREMENT=265 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jcr_verify
-- ----------------------------
DROP TABLE IF EXISTS `jcr_verify`;
CREATE TABLE `jcr_verify` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` varchar(100) DEFAULT NULL COMMENT 'jcr_users里面的id',
  `name` varchar(50) DEFAULT NULL COMMENT '客户姓名',
  `sex` varchar(4) DEFAULT NULL COMMENT '男 女',
  `age` varchar(20) DEFAULT NULL,
  `loginname` varchar(20) DEFAULT NULL COMMENT '手机号',
  `certificate_number` varchar(30) DEFAULT NULL COMMENT '身份证号码',
  `verify_type` varchar(2) DEFAULT NULL COMMENT '认证类型  1：个体认证，2：商户认证',
  `verify_status` varchar(2) DEFAULT '2' COMMENT '1:通过，2：未认证，3：认证中，4：拒绝，5：暂存',
  `shopname` varchar(50) DEFAULT NULL COMMENT '店铺名称',
  `area` varchar(50) DEFAULT NULL COMMENT '所在地区',
  `address` varchar(100) DEFAULT NULL COMMENT '详细地址',
  `business_license_number` varchar(60) DEFAULT NULL COMMENT '营业执照号',
  `company_name` varchar(100) DEFAULT NULL COMMENT '公司名称',
  `create_time` varchar(11) DEFAULT NULL COMMENT '入库时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `is_delete` varchar(2) DEFAULT '2' COMMENT '1:逻辑删除，2：没删除',
  `card_No` varchar(50) DEFAULT NULL COMMENT '银行卡号',
  `verify_time` varchar(11) DEFAULT NULL COMMENT '认证时间',
  `foundtime` varchar(40) DEFAULT '' COMMENT '成立时间',
  `main_business` varchar(100) DEFAULT '' COMMENT '主营业务',
  `credit_score` varchar(10) DEFAULT '' COMMENT '信用评分',
  `manage_score` varchar(10) DEFAULT '' COMMENT '经营评分',
  `assets_score` varchar(10) DEFAULT '' COMMENT '资产评分',
  `debt_score` varchar(10) DEFAULT '' COMMENT '负债评分',
  `conducive_score` varchar(10) DEFAULT '' COMMENT '增信评分',
  `has_score` varchar(2) DEFAULT '2' COMMENT '1:有，2：没有',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=274 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jcr_version
-- ----------------------------
DROP TABLE IF EXISTS `jcr_version`;
CREATE TABLE `jcr_version` (
  `id` bigint(30) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `version` varchar(30) DEFAULT '' COMMENT '版本号',
  `force_update` varchar(2) DEFAULT '2' COMMENT '1:需要强制更新。2：不需要强制更新',
  `remark` varchar(1000) DEFAULT NULL COMMENT '更新内容',
  `download_url` varchar(200) DEFAULT '' COMMENT '下载地址',
  `is_delete` varchar(2) DEFAULT '2' COMMENT '1:已删除。2：未删除',
  `origin` varchar(30) DEFAULT 'ios' COMMENT 'ios  android',
  `sort` varchar(20) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sequence
-- ----------------------------
DROP TABLE IF EXISTS `sequence`;
CREATE TABLE `sequence` (
  `name` varchar(50) NOT NULL,
  `fileid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `increment` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sequence_fseqno
-- ----------------------------
DROP TABLE IF EXISTS `sequence_fseqno`;
CREATE TABLE `sequence_fseqno` (
  `id` int(100) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `name` varchar(20) DEFAULT NULL COMMENT '自增序列名字',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=451 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for suggestion_file
-- ----------------------------
DROP TABLE IF EXISTS `suggestion_file`;
CREATE TABLE `suggestion_file` (
  `id` int(100) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `suggestion_id` varchar(50) DEFAULT NULL COMMENT 'jcr_suggestion里的id',
  `file_id` varchar(50) DEFAULT NULL COMMENT '文件id',
  `file_type_name` varchar(10) DEFAULT NULL COMMENT '文件类型名：image、video',
  `file_path` varchar(80) DEFAULT NULL COMMENT '文件地址',
  `status` varchar(2) DEFAULT '1' COMMENT '状态。1为存在，2为已删除，3暂存',
  `file_class_id` varchar(10) DEFAULT NULL COMMENT '文件类型ID，对应Anjie_file_class表中的ID',
  `file_type` varchar(2) DEFAULT NULL COMMENT '文件类型，1为image，2为video',
  `create_time` varchar(11) DEFAULT NULL COMMENT '创建时间',
  `modify_time` varchar(11) DEFAULT NULL COMMENT '修改时间',
  `add_userid` varchar(50) DEFAULT NULL COMMENT '写入的userid，对应jcr_users里面的id',
  `delete_userid` varchar(50) DEFAULT NULL COMMENT '删除的userid，对应jcr_users里面的id',
  `ifcar99_path` varchar(100) DEFAULT NULL COMMENT '金融平台返回的图片路径',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_address_city
-- ----------------------------
DROP TABLE IF EXISTS `t_address_city`;
CREATE TABLE `t_address_city` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `code` char(6) NOT NULL COMMENT '城市编码',
  `name` varchar(40) NOT NULL COMMENT '城市名称',
  `provinceCode` char(6) NOT NULL COMMENT '所属省份编码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=346 DEFAULT CHARSET=utf8 COMMENT='城市信息表';

-- ----------------------------
-- Table structure for t_address_province
-- ----------------------------
DROP TABLE IF EXISTS `t_address_province`;
CREATE TABLE `t_address_province` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `code` char(6) NOT NULL COMMENT '省份编码',
  `name` varchar(40) NOT NULL COMMENT '省份名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='省份信息表';

-- ----------------------------
-- Table structure for t_address_town
-- ----------------------------
DROP TABLE IF EXISTS `t_address_town`;
CREATE TABLE `t_address_town` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `code` char(6) NOT NULL COMMENT '区县编码',
  `name` varchar(40) NOT NULL COMMENT '区县名称',
  `cityCode` char(6) NOT NULL COMMENT '所属城市编码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3145 DEFAULT CHARSET=utf8 COMMENT='区县信息表';

-- ----------------------------
-- Table structure for v1_queue_sms
-- ----------------------------
DROP TABLE IF EXISTS `v1_queue_sms`;
CREATE TABLE `v1_queue_sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(11) NOT NULL,
  `mobile` varchar(32) NOT NULL,
  `content` varchar(1023) NOT NULL COMMENT '充值返回结果',
  `result_query` text COMMENT '充值查询结果',
  `status` tinyint(4) NOT NULL DEFAULT '5' COMMENT '1发送成功，2发送失败，3发送中，5初始状态',
  `total_query_times` int(11) NOT NULL DEFAULT '0' COMMENT '查询订单状态的次数',
  `send_time` int(11) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `modify_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for v1_role
-- ----------------------------
DROP TABLE IF EXISTS `v1_role`;
CREATE TABLE `v1_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '角色名称',
  `code` varchar(255) NOT NULL DEFAULT '' COMMENT '角色编码，大写英文字母，设定后不建议修改',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '上级角色ID',
  `has_child` tinyint(4) NOT NULL DEFAULT '2' COMMENT '1有子节点，2没有',
  `desc` varchar(255) DEFAULT NULL COMMENT '描述',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=144 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for v1_role_privilege
-- ----------------------------
DROP TABLE IF EXISTS `v1_role_privilege`;
CREATE TABLE `v1_role_privilege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `module_id` int(11) NOT NULL,
  `module` varchar(255) CHARACTER SET ucs2 NOT NULL COMMENT '模块名',
  `method_id` int(11) NOT NULL,
  `method` varchar(31) NOT NULL COMMENT '模块下的方法名',
  `status` tinyint(4) NOT NULL COMMENT '1生效2失效',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `privilege_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4507 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for v1_user_role
-- ----------------------------
DROP TABLE IF EXISTS `v1_user_role`;
CREATE TABLE `v1_user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=231 DEFAULT CHARSET=utf8;
