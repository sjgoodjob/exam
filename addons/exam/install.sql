
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_cate` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `subject_id` INT(11) NOT NULL DEFAULT '0' COMMENT '所属科目',
    `kind` ENUM('QUESTION','PAPER','ROOM') NOT NULL DEFAULT 'QUESTION' COMMENT '种类' COLLATE 'utf8mb4_general_ci',
    `level` ENUM('1','2','3') NOT NULL DEFAULT '1' COMMENT '类型' COLLATE 'utf8mb4_general_ci',
    `name` VARCHAR(50) NOT NULL COMMENT '名称' COLLATE 'utf8mb4_general_ci',
    `icon` VARCHAR(200) NULL DEFAULT '' COMMENT '图标' COLLATE 'utf8mb4_general_ci',
    `parent_id` INT(11) NOT NULL DEFAULT '0' COMMENT '父级',
    `sort` INT(11) NOT NULL DEFAULT '1' COMMENT '排序',
    `remark` VARCHAR(1000) NULL DEFAULT NULL COMMENT '简介' COLLATE 'utf8mb4_general_ci',
    `uses` ENUM('ALL','ONLY_MEMBER') NOT NULL DEFAULT 'ALL' COMMENT '可用群体:ALL=所有用户,ONLY_MEMBER=仅会员用户' COLLATE 'utf8mb4_general_ci',
    `is_free` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '是否免费:0=需要付费或激活,1=免费' COLLATE 'utf8mb4_general_ci',
    `price` DECIMAL(11,2) NOT NULL COMMENT '开通价格',
    `days` INT(11) NULL DEFAULT '0' COMMENT '付费开通有效天数',
    `status` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '状态:0=禁用,1=启用' COLLATE 'utf8_general_ci',
    `deletetime` BIGINT(16) NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `parent_id` (`parent_id`) USING BTREE,
    INDEX `kind` (`kind`) USING BTREE,
    INDEX `is_free` (`is_free`) USING BTREE,
    INDEX `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='试题分类';


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_config_info` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ad_config` mediumtext COLLATE utf8mb4_general_ci COMMENT '广告位配置',
    `system_config` mediumtext COLLATE utf8mb4_general_ci COMMENT '系统配置',
    `wx_config` mediumtext COLLATE utf8mb4_general_ci COMMENT '微信配置',
    `page_config` mediumtext COLLATE utf8mb4_general_ci COMMENT '页面配置',
    PRIMARY KEY (`id`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='参数配置';


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_grade` (
     `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `cate_id` int(11) unsigned NOT NULL COMMENT '所属分类',
    `user_id` int(11) unsigned NOT NULL COMMENT '考试用户',
    `paper_id` int(11) unsigned NOT NULL COMMENT '所属试卷',
    `mode` ENUM('RANDOM','FIX') NOT NULL DEFAULT 'RANDOM' COMMENT '选题模式' COLLATE 'utf8mb4_general_ci',
    `score` tinyint(3) unsigned NOT NULL COMMENT '考试分数',
    `system_score` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系统得分',
    `manual_score` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '人工判分',
    `is_pass` tinyint(3) unsigned NOT NULL COMMENT '是否及格',
    `pass_score` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '及格线',
    `total_score` int(11) unsigned NOT NULL COMMENT '总分数',
    `total_count` int(11) unsigned NOT NULL COMMENT '总题数',
    `right_count` int(11) unsigned NOT NULL COMMENT '答对数',
    `error_count` int(11) unsigned NOT NULL COMMENT '答错数',
    `grade_time` BIGINT(16) unsigned NOT NULL COMMENT '考试用时',
    `date` CHAR(10) NOT NULL DEFAULT '' COMMENT '考试日期',
    `question_ids` VARCHAR(2000) NULL DEFAULT '' COMMENT '试卷ID集合' COLLATE 'utf8mb4_general_ci',
    `error_ids` VARCHAR(2000) NULL DEFAULT '' COMMENT '错题ID集合' COLLATE 'utf8mb4_general_ci',
    `user_answers` TEXT NULL DEFAULT NULL COMMENT '用户答案集合' COLLATE 'utf8mb4_general_ci',
    `configs` TEXT NULL DEFAULT NULL COMMENT '试卷选题配置' COLLATE 'utf8mb4_general_ci',
    `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `user_id` (`user_id`),
    KEY `paper_id` (`paper_id`),
    KEY `work_type_id` (`cate_id`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='考试成绩';


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_notice` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
    `contents` varchar(2000) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '内容',
    `weigh` int(11) NOT NULL DEFAULT '1' COMMENT '排序',
    `status` enum('NORMAL','HIDDEN') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NORMAL' COMMENT '状态',
    `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='系统公告';


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_paper` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `cate_id` int(11) unsigned NOT NULL COMMENT '试卷分类',
    `subject_id` INT(11) NOT NULL DEFAULT '0' COMMENT '所属科目',
    `mode` enum('RANDOM','FIX') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'RANDOM' COMMENT '选题模式',
    `title` varchar(3000) COLLATE utf8mb4_general_ci NOT NULL COMMENT '试卷名称',
    `configs` varchar(3000) COLLATE utf8mb4_general_ci NOT NULL COMMENT '选题配置',
    `cover_image` VARCHAR(200) NULL DEFAULT '' COMMENT '封面图片',
    `quantity` int(10) unsigned NOT NULL COMMENT '题目数量',
    `total_score` int(10) unsigned NOT NULL COMMENT '试卷总分',
    `pass_score` int(10) unsigned NOT NULL COMMENT '及格线',
    `limit_time` BIGINT(16) unsigned NOT NULL COMMENT '考试限时',
    `join_count` int(10) NOT NULL DEFAULT '0' COMMENT '参与人次',
    `day_limit_count` INT(10) NOT NULL DEFAULT '0' COMMENT '每日限制考试次数',
    `status` enum('NORMAL','HIDDEN') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NORMAL' COMMENT '状态',
    `uses` ENUM('ALL','ONLY_MEMBER','ONLY_PAY') NOT NULL DEFAULT 'ALL' COMMENT '可用群体:ALL=所有用户,ONLY_MEMBER=仅会员用户,ONLY_PAY=仅支付用户',
    `price` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '费用',
    `member_price` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '会员费用',
    `pay_effect_days` int(10) NOT NULL DEFAULT '0' COMMENT '付费有效天数',
    `start_time` BIGINT(16) DEFAULT NULL COMMENT '开始时间',
    `end_time` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '过期时间',
    `is_only_room` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '仅用于考场',
    `is_prevent_switch_screen` TINYINT(4) NULL DEFAULT '0' COMMENT '是否防切屏',
    `switch_screen_count` TINYINT(4) NULL DEFAULT '0' COMMENT '允许切屏次数',
    `switch_screen_second` TINYINT(4) NULL DEFAULT '0' COMMENT '切屏认定秒数',
    `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '修改时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `cate_id` (`cate_id`,`status`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='试卷';


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_paper_question` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `paper_id` int(11) unsigned NOT NULL COMMENT '所属试卷',
    `question_id` int(11) unsigned NOT NULL COMMENT '试题',
    `score` int(10) unsigned NOT NULL COMMENT '分数',
    `sort` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '排序',
    `answer_config` TEXT NULL DEFAULT NULL COMMENT '正确答案配置' COLLATE 'utf8mb4_general_ci',
    `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '修改时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `paper_id` (`paper_id`),
    KEY `question_id` (`question_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='试卷试题';


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_question` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `cate_id` int(11) unsigned NOT NULL COMMENT '分类',
    `kind` enum('JUDGE','SINGLE','MULTI','FILL','SHORT','MATERIAL') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'JUDGE' COMMENT '试题类型',
    `title` varchar(5000) COLLATE utf8mb4_general_ci NOT NULL COMMENT '题目',
    `explain` TEXT NULL DEFAULT NULL COLLATE utf8mb4_general_ci COMMENT '解析',
    `difficulty` enum('EASY','GENERAL','HARD') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'GENERAL' COMMENT '难度',
    `options_json` text COLLATE utf8mb4_general_ci NOT NULL COMMENT '选项',
    `options_img` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '选项图片',
    `options_extend` TEXT NULL DEFAULT NULL COMMENT '选项扩展' COLLATE 'utf8mb4_general_ci',
    `answer` TEXT NOT NULL COMMENT '正确答案' COLLATE 'utf8mb4_general_ci',
    `status` enum('NORMAL','HIDDEN') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NORMAL' COMMENT '状态',
    `is_material_child` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '属于材料题子题：0=否，1=是',
    `material_question_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属材料题',
    `material_score` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '材料子题分数',
    `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '修改时间',
    `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`),
    KEY `kind` (`kind`,`status`) USING BTREE,
    KEY `cate_id` (`cate_id`,`kind`,`status`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='试题';


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_question_collect` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(11) unsigned NOT NULL COMMENT '用户',
    `question_id` int(11) unsigned NOT NULL COMMENT '试题',
    `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `question_id` (`question_id`) USING BTREE,
    KEY `user_id` (`user_id`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='题目收藏';


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_question_wrong` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL COMMENT '用户',
    `question_id` INT(11) UNSIGNED NOT NULL COMMENT '试题',
    `user_answer` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '用户答案' COLLATE 'utf8mb4_general_ci',
    `kind` ENUM('PAPER','ROOM','TRAINING') NULL DEFAULT 'PAPER' COMMENT '来源：PAPER=试卷，ROOM=考场，TRAINING=练题' COLLATE 'utf8mb4_general_ci',
    `cate_id` INT(11) UNSIGNED NULL DEFAULT '0' COMMENT '所属题库',
    `paper_id` INT(11) UNSIGNED NULL DEFAULT '0' COMMENT '来源试卷',
    `room_id` INT(11) UNSIGNED NULL DEFAULT '0' COMMENT '来源考场',
    `createtime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `question_id` (`question_id`) USING BTREE,
    INDEX `kind` (`kind`) USING BTREE,
    INDEX `user_id` (`user_id`, `kind`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='错题记录';


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_room` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '考场标题' COLLATE 'utf8mb4_general_ci',
    `contents` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '考场说明' COLLATE 'utf8mb4_general_ci',
    `cover_image` VARCHAR(200) NULL DEFAULT '' COMMENT '封面图片' COLLATE 'utf8mb4_general_ci',
    `cate_id` INT(11) NOT NULL COMMENT '考场分类',
    `subject_id` INT(11) NOT NULL DEFAULT '0' COMMENT '所属科目',
    `paper_id` INT(11) NOT NULL COMMENT '考试试卷',
    `people_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '限制考场人数',
    `start_time` BIGINT(16) DEFAULT NULL COMMENT '考试开始时间',
    `end_time` BIGINT(16) DEFAULT NULL COMMENT '考试结束时间',
    `weigh` INT(11) NOT NULL DEFAULT '1' COMMENT '排序',
    `status` ENUM('NORMAL','HIDDEN') NOT NULL DEFAULT 'NORMAL' COMMENT '状态' COLLATE 'utf8mb4_general_ci',
    `signup_mode` ENUM('NORMAL','PASSWORD','AUDIT') NOT NULL DEFAULT 'NORMAL' COMMENT '报名方式:NORMAL=普通模式，PASSWORD=密码模式，AUDIT=审核模式' COLLATE 'utf8mb4_general_ci',
    `password` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '考场密码' COLLATE 'utf8mb4_general_ci',
    `is_makeup` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '是否允许补考:0=关闭，1=开启',
    `makeup_count` TINYINT(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT '补考次数',
    `is_rank` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '是否已排名',
    `signup_count` INT(11) NOT NULL DEFAULT '0' COMMENT '报考人数',
    `grade_count` INT(11) NOT NULL DEFAULT '0' COMMENT '考试人数',
    `pass_count` INT(11) NOT NULL DEFAULT '0' COMMENT '及格人数',
    `pass_rate` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '及格率',
    `cert_config_id` INT(11) NULL DEFAULT '0' COMMENT '证书生成配置',
    `is_create_qrcode_h5` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '是否生成考场H5二维码',
    `qrcode_h5` VARCHAR(200) NULL DEFAULT NULL COMMENT '考场H5二维码' COLLATE 'utf8mb4_general_ci',
    `createtime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
    `deletetime` BIGINT(16) NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `paper_id` (`paper_id`),
    KEY `status` (`status`) USING BTREE,
    KEY `cate_id` (`status`,`cate_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='考试考场';


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_room_grade` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL COMMENT '考试用户',
    `cate_id` INT(11) UNSIGNED NOT NULL COMMENT '所属分类',
    `room_id` INT(11) UNSIGNED NOT NULL COMMENT '所属考场',
    `paper_id` INT(11) UNSIGNED NOT NULL COMMENT '所属试卷',
    `school_id` INT(11) UNSIGNED NULL DEFAULT '0' COMMENT '所属学校',
    `class_name` VARCHAR(30) NULL DEFAULT '' COMMENT '班级' COLLATE 'utf8mb4_general_ci',
    `mode` ENUM('RANDOM','FIX') NOT NULL DEFAULT 'RANDOM' COMMENT '选题模式' COLLATE 'utf8mb4_general_ci',
    `score` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '考试分数',
    `system_score` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系统得分',
    `manual_score` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '人工判分',
    `is_pass` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '是否及格:0=不及格，1=及格',
    `is_makeup` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '是否是补考:0=否，1=是',
    `pass_score` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '及格线',
    `total_score` INT(11) NOT NULL DEFAULT '0' COMMENT '总分数',
    `total_count` INT(11) NOT NULL DEFAULT '0' COMMENT '总题数',
    `right_count` INT(11) NOT NULL DEFAULT '0' COMMENT '答对数',
    `error_count` INT(11) NOT NULL DEFAULT '0' COMMENT '答错数',
    `rank` INT(11) NOT NULL DEFAULT '0' COMMENT '本次考试排名',
    `is_pre` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '是否为预载入数据',
    `grade_time` BIGINT(16) UNSIGNED NOT NULL COMMENT '考试用时',
    `question_ids` TEXT NULL DEFAULT NULL COMMENT '试卷ID集合' COLLATE 'utf8mb4_general_ci',
    `error_ids` TEXT NULL DEFAULT NULL COMMENT '错题ID集合' COLLATE 'utf8mb4_general_ci',
    `user_answers` TEXT NULL DEFAULT NULL COMMENT '用户答案集合' COLLATE 'utf8mb4_general_ci',
    `configs` TEXT NULL DEFAULT NULL COMMENT '试卷选题配置' COLLATE 'utf8mb4_general_ci',
    `createtime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `user_id` (`user_id`) USING BTREE,
    KEY `paper_id` (`paper_id`) USING BTREE,
    KEY `cate_id` (`cate_id`) USING BTREE,
    KEY `FK2_exam_room_grade_with_exam_room` (`room_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='考场考试成绩';


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_room_signup` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `room_id` int(11) unsigned NOT NULL COMMENT '所属考场',
    `user_id` int(11) unsigned NOT NULL COMMENT '报名用户',
    `real_name` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
    `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '手机号码',
    `message` varchar(200) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '审核说明',
    `status` enum('0','1','2') COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '状态:0=未审核，1=报名成功，2=报名被拒绝',
    `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
    `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `room_id` (`room_id`,`status`),
    KEY `user_id` (`user_id`,`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='考场报名';

-- 1.0.2版本，新增考卷每日限制考试次数
ALTER TABLE `__PREFIX__exam_paper` ADD COLUMN `day_limit_count` INT(10) NOT NULL DEFAULT '0' COMMENT '每日限制考试次数' AFTER `join_count`;
ALTER TABLE `__PREFIX__exam_grade` ADD COLUMN `date` CHAR(10) NOT NULL DEFAULT '' COMMENT '考试日期' AFTER `grade_time`;

-- 1.0.4版本，新增系统配置 - 页面配置
ALTER TABLE `__PREFIX__exam_config_info` ADD COLUMN `page_config` MEDIUMTEXT NULL DEFAULT NULL COMMENT '页面配置' COLLATE 'utf8mb4_general_ci' AFTER `wx_config`;

-- 1.0.5版本 - 高级版

-- 新增系统配置 - 会员配置、积分配置
ALTER TABLE `__PREFIX__exam_config_info` ADD COLUMN `member_config` MEDIUMTEXT NULL DEFAULT NULL COMMENT '会员配置' COLLATE 'utf8mb4_general_ci' AFTER `page_config`;
ALTER TABLE `__PREFIX__exam_config_info` ADD COLUMN `score_config` MEDIUMTEXT NULL DEFAULT NULL COMMENT '积分配置' COLLATE 'utf8mb4_general_ci' AFTER `member_config`;

-- 试卷新增可用群体及付费信息
ALTER TABLE `__PREFIX__exam_paper` add uses enum ('ALL', 'ONLY_MEMBER', 'ONLY_PAY') default 'ALL' not null comment '可用群体:ALL=所有用户,ONLY_MEMBER=仅会员用户,ONLY_PAY=仅支付用户' after status;
ALTER TABLE `__PREFIX__exam_paper` add price decimal(10, 2) default 0.00 not null comment '费用' after uses;
ALTER TABLE `__PREFIX__exam_paper` add member_price decimal(10, 2) default 0.00 not null comment '会员费用' after price;
create index uses on `__PREFIX__exam_paper` (uses, status);

-- 会员配置表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_member_config` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `name` VARCHAR(50) NOT NULL COMMENT '名称' COLLATE 'utf8mb4_general_ci',
    `price` DECIMAL(11,2) UNSIGNED NOT NULL COMMENT '价格',
    `days` INT(10) UNSIGNED NOT NULL COMMENT '天数',
    `tag` VARCHAR(50) NULL DEFAULT NULL COMMENT '标签' COLLATE 'utf8mb4_general_ci',
    `uses` ENUM('all','cate') NOT NULL DEFAULT 'all' COMMENT '可用范围:0=所有题库,1=部分题库' COLLATE 'utf8_general_ci',
    `cate_ids` TEXT NULL DEFAULT NULL COMMENT '可用题库' COLLATE 'utf8_general_ci',
    `paper_uses` ENUM('all','part_cate') NOT NULL DEFAULT 'all' COMMENT '可用范围:all=所有试卷,part_cate=部分试卷分类' COLLATE 'utf8_general_ci',
    `paper_cate_ids` TEXT NULL DEFAULT NULL COMMENT '可用试卷分类' COLLATE 'utf8_general_ci',
    `desc` VARCHAR(50) NULL DEFAULT NULL COMMENT '说明' COLLATE 'utf8mb4_general_ci',
    `status` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '状态:0=关闭,1=开启' COLLATE 'utf8_general_ci',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '更新时间',
    `deletetime` BIGINT(16) NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='会员开通配置';

-- 会员开通订单表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_member_order` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `user_id` INT(10) NOT NULL COMMENT '支付用户',
    `order_no` VARCHAR(50) NOT NULL COMMENT '订单编号' COLLATE 'utf8_general_ci',
    `member_config_id` INT(10) NOT NULL COMMENT '开通会员类型',
    `uses` ENUM('all','cate','subject') NULL DEFAULT 'all' COMMENT '可用范围:0=所有题库,1=部分题库' COLLATE 'utf8_general_ci',
    `cate_ids` TEXT NULL DEFAULT NULL COMMENT '可用题库' COLLATE 'utf8_general_ci',
    `days` INT(10) NOT NULL COMMENT '会员有效天数',
    `amount` DECIMAL(11,2) NOT NULL COMMENT '订单金额',
    `status` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT '是否已支付:0=否,1=是' COLLATE 'utf8_general_ci',
    `pay_money` DECIMAL(11,2) NULL DEFAULT NULL COMMENT '支付金额',
    `pay_time` BIGINT(16) NULL DEFAULT NULL COMMENT '支付时间',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `order_no` (`order_no`) USING BTREE,
    INDEX `user_id` (`user_id`, `status`) USING BTREE,
    INDEX `type` (`member_config_id`, `status`) USING BTREE
    ) ENGINE=InnoDB COLLATE='utf8_general_ci' COMMENT='开通会员订单';

-- 用户信息表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_user_info` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `type` ENUM('NORMAL','VIP_MONTH','VIP_YEAR','VIP_LIFE') NOT NULL DEFAULT 'NORMAL' COMMENT '用户类型:NORMAL=普通用户,VIP_MONTH=月卡会员,VIP_YEAR=年卡会员,VIP_LIFE=终身会员' COLLATE 'utf8mb4_general_ci',
    `member_config_id` INT(10) NOT NULL COMMENT '开通会员类型',
    `user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
    `score` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '积分',
    `score_inc` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '累计获得积分',
    `score_dec` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '累计支出积分',
    `expire_time` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '过期时间',
    `only_subject_ids` VARCHAR(1000) NULL DEFAULT NULL COMMENT '可用科目',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `user_id` (`user_id`) USING BTREE,
    INDEX `type` (`type`) USING BTREE,
    INDEX `member_config_id` (`member_config_id`, `expire_time`) USING BTREE
    ) COMMENT='用户信息' COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;

-- 用户积分变动表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_user_score_log` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会员ID',
    `kind` ENUM('INC','DEC') NOT NULL COMMENT '种类:INC=增加,DEC=减少' COLLATE 'utf8mb4_general_ci',
    `score` INT(10) NOT NULL DEFAULT '0' COMMENT '变更积分',
    `before` INT(10) NOT NULL DEFAULT '0' COMMENT '变更前积分',
    `after` INT(10) NOT NULL DEFAULT '0' COMMENT '变更后积分',
    `type` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '变更类型' COLLATE 'utf8mb4_general_ci',
    `memo` VARCHAR(255) NULL DEFAULT '' COMMENT '备注' COLLATE 'utf8mb4_general_ci',
    `date` CHAR(10) NOT NULL DEFAULT '' COMMENT '日期' COLLATE 'utf8mb4_general_ci',
    `changeable_id` INT(10) NOT NULL DEFAULT '0' COMMENT '关联ID',
    `changeable_type` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '关联模型' COLLATE 'utf8mb4_general_ci',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `date` (`date`, `user_id`, `type`) USING BTREE
    ) COMMENT='用户积分变动表' COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;

-- 试卷考试支付订单表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_paper_order` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `user_id` INT(10) NOT NULL COMMENT '支付用户',
    `order_no` VARCHAR(50) NOT NULL COMMENT '订单编号' COLLATE 'utf8_general_ci',
    `paper_id` INT(10) NOT NULL COMMENT '试卷',
    `amount` DECIMAL(11,2) NOT NULL COMMENT '订单金额',
    `status` ENUM('0','1','2') NOT NULL DEFAULT '0' COMMENT '状态:0=未支付,1=已支付未使用,2=已使用' COLLATE 'utf8_general_ci',
    `pay_money` DECIMAL(11,2) NULL DEFAULT NULL COMMENT '支付金额',
    `pay_time` BIGINT(16) NULL DEFAULT NULL COMMENT '支付时间',
    `expire_time` BIGINT(16) NULL DEFAULT NULL COMMENT '过期时间',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `order_no` (`order_no`) USING BTREE,
    INDEX `user_id` (`user_id`, `status`) USING BTREE,
    INDEX `type` (`paper_id`, `status`) USING BTREE
    ) COMMENT='试卷考试支付订单' COLLATE='utf8_general_ci' ENGINE=InnoDB;

-- 支付记录表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_pay_log` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `user_id` INT(10) NOT NULL DEFAULT '0' COMMENT '支付用户',
    `openid` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '支付用户openid' COLLATE 'utf8_general_ci',
    `mch_id` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '微信商户号' COLLATE 'utf8_general_ci',
    `app_id` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '小程序ID' COLLATE 'utf8_general_ci',
    `out_trade_no` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '业务单号' COLLATE 'utf8_general_ci',
    `pay_money` DECIMAL(11,2) NULL DEFAULT '0.00' COMMENT '支付金额',
    `transaction_id` VARCHAR(50) NOT NULL COMMENT '微信支付流水号' COLLATE 'utf8_general_ci',
    `status` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT '是否已支付:0=否,1=是' COLLATE 'utf8_general_ci',
    `response` TEXT NULL DEFAULT NULL COMMENT '支付响应' COLLATE 'utf8_general_ci',
    `payable_id` INT(10) NOT NULL DEFAULT '0' COMMENT '关联ID',
    `payable_type` VARCHAR(191) NOT NULL DEFAULT '' COMMENT '关联模型' COLLATE 'utf8mb4_general_ci',
    `error_message` VARCHAR(1000) NULL DEFAULT '' COMMENT '错误说明' COLLATE 'utf8_general_ci',
    `error_time` BIGINT(16) NULL DEFAULT NULL COMMENT '错误发生时间',
    `pay_time` BIGINT(16) NULL DEFAULT NULL COMMENT '支付时间',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `unique_transaction_id` (`transaction_id`) USING BTREE,
    INDEX `user_id` (`user_id`, `status`) USING BTREE,
    INDEX `transaction_id` (`transaction_id`) USING BTREE,
    INDEX `order_no` (`out_trade_no`) USING BTREE,
    INDEX `openid` (`openid`) USING BTREE
    ) COMMENT='支付记录' COLLATE='utf8_general_ci' ENGINE=InnoDB;



-- 1.0.6版本

-- 积分商品表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_score_good` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(191) NOT NULL COMMENT '商品标题' COLLATE 'utf8mb4_general_ci',
    `price` INT(11) UNSIGNED NOT NULL COMMENT '兑换积分',
    `origin_price` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '商品原价',
    `stocks` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '库存',
    `limit` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '限购数',
    `sell_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '已兑数量',
    `description` TEXT(65535) NULL DEFAULT NULL COMMENT '商品详情' COLLATE 'utf8mb4_general_ci',
    `notes` TEXT(65535) NULL DEFAULT NULL COMMENT '兑换说明' COLLATE 'utf8mb4_general_ci',
    `images` VARCHAR(800) NOT NULL COMMENT '图片集合' COLLATE 'utf8mb4_general_ci',
    `first_image` VARCHAR(191) NULL DEFAULT '' COMMENT '商品首图' COLLATE 'utf8mb4_general_ci',
    `weigh` INT(11) NOT NULL DEFAULT '1' COMMENT '商品排序',
    `status` ENUM('0','10','20') NOT NULL DEFAULT '0' COMMENT '状态:0=下架，10=上架，20=已售罄' COLLATE 'utf8_general_ci',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '更新时间',
    `deletetime` BIGINT(16) NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `status` (`status`) USING BTREE,
    INDEX `name` (`name`) USING BTREE
    ) COMMENT='积分商品' COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;

-- 积分商品兑换单表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_score_good_order` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_no` VARCHAR(50) NOT NULL COMMENT '订单编号' COLLATE 'utf8mb4_general_ci',
    `user_id` INT(11) NOT NULL COMMENT '下单用户',
    `user_name` VARCHAR(20) NULL DEFAULT NULL COMMENT '用户姓名' COLLATE 'utf8mb4_general_ci',
    `phone` VARCHAR(20) NULL DEFAULT NULL COMMENT '手机号码' COLLATE 'utf8mb4_general_ci',
    `address` VARCHAR(255) NULL DEFAULT NULL COMMENT '详细地址' COLLATE 'utf8mb4_general_ci',
    `good_id` INT(11) NOT NULL COMMENT '兑换商品',
    `name` VARCHAR(191) NOT NULL COMMENT '商品标题' COLLATE 'utf8mb4_general_ci',
    `first_image` VARCHAR(191) NOT NULL COMMENT '商品首图' COLLATE 'utf8mb4_general_ci',
    `quantity` INT(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '商品数量',
    `price` INT(11) UNSIGNED NOT NULL COMMENT '兑换积分',
    `amount` INT(11) UNSIGNED NOT NULL COMMENT '总兑换积分',
    `status` ENUM('0','10','20','30') NOT NULL DEFAULT '0' COMMENT '状态:0=未支付，10=已支付，20=已发货，30=已完成' COLLATE 'utf8_general_ci',
    `admin_remark` VARCHAR(500) NULL DEFAULT NULL COMMENT '后台说明' COLLATE 'utf8mb4_general_ci',
    `ship_remark` VARCHAR(500) NULL DEFAULT NULL COMMENT '发货说明' COLLATE 'utf8mb4_general_ci',
    `ship_no` VARCHAR(50) NULL DEFAULT NULL COMMENT '发货单号' COLLATE 'utf8mb4_general_ci',
    `pay_time` BIGINT(16) NULL DEFAULT NULL COMMENT '支付时间',
    `ship_time` BIGINT(16) NULL DEFAULT NULL COMMENT '发货时间',
    `complete_time` BIGINT(16) NULL DEFAULT NULL COMMENT '完成时间',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `good_id` (`good_id`, `status`) USING BTREE,
    INDEX `user_id` (`user_id`, `status`) USING BTREE,
    INDEX `status` (`status`) USING BTREE,
    INDEX `order_no` (`order_no`) USING BTREE,
    INDEX `name` (`name`) USING BTREE
    ) COMMENT='积分商品兑换单' COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;

-- 加大选项图片字段长度
ALTER TABLE `__PREFIX__exam_question` CHANGE COLUMN `options_img` `options_img` VARCHAR(1000) NULL DEFAULT NULL COMMENT '选项图片' COLLATE 'utf8mb4_general_ci' AFTER `options_json`;


-- 1.0.7 版本
-- 解决积分商品状态值不对应无法设置售罄问题
ALTER TABLE `__PREFIX__exam_score_good` CHANGE COLUMN `status` `status` ENUM('0','10','20') NOT NULL DEFAULT '0' COMMENT '状态:0=下架，10=上架，20=已售罄' COLLATE 'utf8_general_ci';

-- 1.0.9 版本
-- 新增分类可用群体
ALTER TABLE `__PREFIX__exam_cate` ADD COLUMN `uses` ENUM('ALL','ONLY_MEMBER') NOT NULL DEFAULT 'ALL' COMMENT '可用群体:ALL=所有用户,ONLY_MEMBER=仅会员用户' COLLATE 'utf8mb4_general_ci' AFTER `remark`;

-- 1.0.10 版本
-- 新增会员激活码
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_member_code` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_config_id` INT(10) NOT NULL COMMENT '开通会员类型',
    `code` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '激活码' COLLATE 'utf8mb4_general_ci',
    `status` ENUM('0','1','2') NOT NULL DEFAULT '0' COMMENT '状态:0=未激活,1=已激活,1=失效' COLLATE 'utf8_general_ci',
    `user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '激活用户',
    `remark` VARCHAR(500) NULL DEFAULT '' COMMENT '备注' COLLATE 'utf8mb4_general_ci',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '修改时间',
    `activate_time` BIGINT(16) NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `user_id` (`user_id`) USING BTREE,
    INDEX `member_config_id` (`member_config_id`) USING BTREE,
    INDEX `code` (`code`, `status`) USING BTREE
    ) COMMENT='会员激活码' COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;

-- 1.0.11
-- 试卷加开始、过期时间，仅用于考场字段
ALTER TABLE `__PREFIX__exam_paper` ADD COLUMN `end_time` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '过期时间' AFTER `member_price`;
ALTER TABLE `__PREFIX__exam_paper`
    ADD COLUMN `start_time` BIGINT(16) DEFAULT NULL COMMENT '开始时间' AFTER `member_price`,
	ADD COLUMN `is_only_room` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '仅用于考场' AFTER `end_time`,
	ADD INDEX `is_only_room` (`is_only_room`),
	ADD INDEX `start_time` (`start_time`);

-- 新增激活时间
ALTER TABLE `__PREFIX__exam_member_code` ADD COLUMN `activate_time` BIGINT(16) NULL DEFAULT NULL AFTER `updatetime`;

-- 试题新增【填空题】类型
ALTER TABLE `__PREFIX__exam_question` ADD COLUMN `options_extend` VARCHAR(1000) NULL DEFAULT NULL COMMENT '选项扩展' COLLATE 'utf8mb4_general_ci' AFTER `options_img`;
-- ALTER TABLE `__PREFIX__exam_question` CHANGE COLUMN `kind` `kind` ENUM('JUDGE','SINGLE','MULTI','FILL') NOT NULL DEFAULT 'JUDGE' COMMENT '试题类型' COLLATE 'utf8mb4_general_ci';
ALTER TABLE `__PREFIX__exam_question` CHANGE COLUMN `answer` `answer` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '正确答案' COLLATE 'utf8mb4_general_ci';

-- 1.0.12
-- 考卷成绩新增答题信息
ALTER TABLE `__PREFIX__exam_grade`
    ADD COLUMN `question_ids` VARCHAR(2000) NULL DEFAULT '' COMMENT '试卷ID集合' COLLATE 'utf8mb4_general_ci' AFTER `date`,
	ADD COLUMN `error_ids` VARCHAR(2000) NULL DEFAULT '' COMMENT '错题ID集合' COLLATE 'utf8mb4_general_ci' AFTER `question_ids`,
	ADD COLUMN `user_answers` TEXT NULL DEFAULT NULL COMMENT '用户答案集合' COLLATE 'utf8mb4_general_ci' AFTER `error_ids`;

-- 考场成绩新增答题信息
ALTER TABLE `__PREFIX__exam_room_grade`
    ADD COLUMN `question_ids` VARCHAR(2000) NULL DEFAULT '' COMMENT '试卷ID集合' COLLATE 'utf8mb4_general_ci' AFTER `grade_time`,
	ADD COLUMN `error_ids` VARCHAR(2000) NULL DEFAULT '' COMMENT '错题ID集合' COLLATE 'utf8mb4_general_ci' AFTER `question_ids`,
	ADD COLUMN `user_answers` TEXT NULL DEFAULT NULL COMMENT '用户答案集合' COLLATE 'utf8mb4_general_ci' AFTER `error_ids`;

-- 1.0.13
-- 考卷成绩新增考卷选题配置
ALTER TABLE `__PREFIX__exam_grade`
    ADD COLUMN `configs` VARCHAR(1000) NULL COMMENT '试卷选题配置' COLLATE 'utf8mb4_general_ci' AFTER `user_answers`;

-- 考场成绩新增考卷选题配置
ALTER TABLE `__PREFIX__exam_room_grade`
    ADD COLUMN `configs` VARCHAR(1000) NULL COMMENT '试卷选题配置' COLLATE 'utf8mb4_general_ci' AFTER `user_answers`;


-- 1.1.0
-- 错题新增用户答案
ALTER TABLE `__PREFIX__exam_question_wrong`
    ADD COLUMN `user_answer` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '用户答案' COLLATE 'utf8mb4_general_ci' AFTER `question_id`;

ALTER TABLE `__PREFIX__exam_grade`
    ADD COLUMN `mode` ENUM('RANDOM','FIX') NOT NULL DEFAULT 'RANDOM' COMMENT '选题模式' COLLATE 'utf8mb4_general_ci' AFTER `paper_id`;

ALTER TABLE `__PREFIX__exam_room_grade`
    ADD COLUMN `mode` ENUM('RANDOM','FIX') NOT NULL DEFAULT 'RANDOM' COMMENT '选题模式' COLLATE 'utf8mb4_general_ci' AFTER `paper_id`;

-- 1.2.1
-- 新增公告前端跳转信息
ALTER TABLE `__PREFIX__exam_notice`
    ADD COLUMN `front_info` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '前端跳转信息' AFTER `status`;

-- 1.3.0
-- 新增试题类型【简答题】、【材料题】
ALTER TABLE `__PREFIX__exam_question`
    CHANGE COLUMN `kind` `kind` ENUM('JUDGE','SINGLE','MULTI','FILL','SHORT','MATERIAL') NOT NULL DEFAULT 'JUDGE' COMMENT '试题类型' COLLATE 'utf8mb4_general_ci' AFTER `cate_id`;
ALTER TABLE `__PREFIX__exam_question`
    CHANGE COLUMN `title` `title` VARCHAR(5000) NOT NULL COMMENT '题目' COLLATE 'utf8mb4_general_ci' AFTER `kind`;
ALTER TABLE `__PREFIX__exam_question`
    CHANGE COLUMN `answer` `answer` TEXT NOT NULL COMMENT '正确答案' COLLATE 'utf8mb4_general_ci' AFTER `options_extend`;
ALTER TABLE `__PREFIX__exam_question`
    CHANGE COLUMN `options_extend` `options_extend` TEXT NULL COMMENT '选项扩展' COLLATE 'utf8mb4_general_ci' AFTER `options_img`;

-- 试卷标题、配置字段扩展长度
ALTER TABLE `__PREFIX__exam_paper`
    CHANGE COLUMN `title` `title` VARCHAR(5000) NOT NULL COMMENT '试卷名称' COLLATE 'utf8mb4_general_ci' AFTER `mode`,
    CHANGE COLUMN `configs` `configs` VARCHAR(3000) NOT NULL COMMENT '选题配置' COLLATE 'utf8mb4_general_ci' AFTER `title`;

-- 试卷固定选题新增试题答案
ALTER TABLE `__PREFIX__exam_paper_question`
    ADD COLUMN `answer_config` TEXT NULL COMMENT '正确答案配置' COLLATE 'utf8mb4_general_ci' AFTER `sort`;

-- 考试成绩新增系统得分、人工判分
ALTER TABLE `__PREFIX__exam_grade`
    ADD COLUMN `system_score` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系统得分' AFTER `score`,
	ADD COLUMN `manual_score` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '人工判分' AFTER `system_score`;

-- 考场成绩新增系统得分、人工判分
ALTER TABLE `__PREFIX__exam_room_grade`
    ADD COLUMN `system_score` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系统得分' AFTER `score`,
	ADD COLUMN `manual_score` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '人工判分' AFTER `system_score`;

-- 错题记录新增来源
ALTER TABLE `__PREFIX__exam_question_wrong`
    ADD COLUMN `kind` ENUM('PAPER','ROOM','TRAINING') NULL DEFAULT 'PAPER' COMMENT '来源：PAPER=试卷，ROOM=考场，TRAINING=练题' AFTER `user_answer`,
DROP INDEX `user_id`,
	ADD INDEX `user_id` (`user_id`, `kind`) USING BTREE;

-- 新增材料题关联表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_material_question` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_question_id` INT(11) UNSIGNED NOT NULL COMMENT '材料题主题目',
    `question_id` INT(11) UNSIGNED NOT NULL COMMENT '材料题子题目',
    `score` INT(11) UNSIGNED NOT NULL COMMENT '分数',
    `weigh` INT(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '排序',
    `answer` TEXT NULL DEFAULT NULL COMMENT '正确答案配置' COLLATE 'utf8mb4_general_ci',
    `createtime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `from_question_id` (`parent_question_id`, `question_id`) USING BTREE
    ) COMMENT='材料题关联表'
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;

-- 试卷成绩手动判题记录表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_manual_grade_log` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '操作管理员',
    `user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '考试用户',
    `paper_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '试卷ID',
    `grade_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '考试成绩ID',
    `question_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '试题ID',
    `before_score` INT(11) UNSIGNED NOT NULL COMMENT '修改前分数',
    `after_score` INT(11) UNSIGNED NOT NULL COMMENT '修改后分数',
    `status` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT '状态：0=未生效，1=已生效' COLLATE 'utf8mb4_general_ci',
    `createtime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `admin_id` (`admin_id`) USING BTREE,
    INDEX `user_id` (`user_id`) USING BTREE,
    INDEX `status` (`status`) USING BTREE,
    INDEX `grade_id` (`grade_id`, `status`) USING BTREE,
    INDEX `paper_id` (`paper_id`, `status`) USING BTREE,
    INDEX `question_id` (`question_id`) USING BTREE
    ) COMMENT='试卷成绩手动判题记录表'
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;

-- 考场成绩手动判题记录表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_manual_room_grade_log` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '操作管理员',
    `user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '考试用户',
    `paper_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '试卷ID',
    `room_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '考场ID',
    `grade_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '考试成绩ID',
    `question_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '试题ID',
    `before_score` INT(11) UNSIGNED NOT NULL COMMENT '修改前分数',
    `after_score` INT(11) UNSIGNED NOT NULL COMMENT '修改后分数',
    `status` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT '状态：0=未生效，1=已生效' COLLATE 'utf8mb4_general_ci',
    `createtime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `admin_id` (`admin_id`) USING BTREE,
    INDEX `user_id` (`user_id`) USING BTREE,
    INDEX `status` (`status`) USING BTREE,
    INDEX `grade_id` (`grade_id`, `status`) USING BTREE,
    INDEX `paper_id` (`paper_id`, `status`) USING BTREE,
    INDEX `question_id` (`question_id`) USING BTREE,
    INDEX `room_id` (`room_id`) USING BTREE
    ) COMMENT='考场成绩手动判题记录表'
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;

-- 新增题库分类是否免费字段
ALTER TABLE `__PREFIX__exam_cate`
    ADD COLUMN `is_free` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '是否免费:0=需要付费或激活,1=免费' AFTER `uses`,
    ADD COLUMN `price` DECIMAL(11,2) NOT NULL COMMENT '开通价格' AFTER `is_free`,
	ADD COLUMN `status` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '状态:0=禁用,1=启用' COLLATE 'utf8_general_ci' AFTER `price`,
	ADD INDEX `status` (`status`),
    ADD INDEX `is_free` (`is_free`);
ALTER TABLE `__PREFIX__exam_cate`
    ADD COLUMN `days` INT NULL DEFAULT 0 COMMENT '付费开通有效天数' AFTER `price`;

-- 新增题库激活码表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_cate_code` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cate_id` VARCHAR(5000) NOT NULL DEFAULT '' COMMENT '题库ID' COLLATE 'utf8mb4_general_ci',
    `code` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '激活码' COLLATE 'utf8mb4_general_ci',
    `status` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT '状态:0=未激活,1=已激活' COLLATE 'utf8_general_ci',
    `user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '激活用户',
    `days` INT(11) NOT NULL DEFAULT '0' COMMENT '有效天数',
    `remark` VARCHAR(500) NULL DEFAULT '' COMMENT '备注' COLLATE 'utf8_general_ci',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '修改时间',
    `activate_time` BIGINT(16) NULL DEFAULT NULL COMMENT '激活时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `user_id` (`user_id`) USING BTREE,
    INDEX `code` (`code`, `status`) USING BTREE
    )
    COMMENT='题库激活码'
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;

-- 新增题库支付订单表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_cate_order` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `user_id` INT(10) NOT NULL COMMENT '支付用户',
    `order_no` VARCHAR(50) NOT NULL COMMENT '订单编号' COLLATE 'utf8_general_ci',
    `cate_id` INT(10) NOT NULL COMMENT '试卷',
    `amount` DECIMAL(11,2) NOT NULL COMMENT '订单金额',
    `days` INT(11) NOT NULL DEFAULT '0' COMMENT '有效天数',
    `status` ENUM('0','1','2') NOT NULL DEFAULT '0' COMMENT '状态:0=未支付,1=已支付未使用,2=已使用' COLLATE 'utf8_general_ci',
    `pay_money` DECIMAL(11,2) NULL DEFAULT NULL COMMENT '支付金额',
    `pay_time` BIGINT(16) NULL DEFAULT NULL COMMENT '支付时间',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `order_no` (`order_no`) USING BTREE,
    INDEX `user_id` (`user_id`, `status`) USING BTREE,
    INDEX `type` (`cate_id`, `status`) USING BTREE
    ) COMMENT='题库支付订单'
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB;

-- 新增题库用户激活记录表
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_cate_user_log` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cate_id` INT(10) NOT NULL COMMENT '题库ID',
    `user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '激活用户',
    `type` ENUM('PAY','CODE') NOT NULL COMMENT '状态:PAY=支付订单,CODE=激活码' COLLATE 'utf8_general_ci',
    `expire_time` BIGINT(16) NULL DEFAULT NULL COMMENT '过期时间',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `code` (`type`) USING BTREE,
    INDEX `user_id` (`user_id`, `cate_id`, `expire_time`) USING BTREE,
    INDEX `cate_id` (`cate_id`) USING BTREE
    ) COMMENT='用户激活题库记录'
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;

-- 1.4.0
-- 成绩记录表扩展配置长度
ALTER TABLE `__PREFIX__exam_grade`
    CHANGE COLUMN `configs` `configs` TEXT NULL COMMENT '试卷选题配置' COLLATE 'utf8mb4_general_ci' AFTER `user_answers`;
ALTER TABLE `__PREFIX__exam_room_grade`
    CHANGE COLUMN `configs` `configs` TEXT NULL COMMENT '试卷选题配置' COLLATE 'utf8mb4_general_ci' AFTER `user_answers`;

-- 材料题关联表新增分数字段
ALTER TABLE `__PREFIX__exam_material_question`
    ADD COLUMN `score` INT(11) UNSIGNED NOT NULL COMMENT '分数' AFTER `question_id`,
    ADD COLUMN `weigh` INT(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '排序' AFTER `score`,
	ADD COLUMN `answer` TEXT NULL DEFAULT NULL COMMENT '正确答案配置' COLLATE 'utf8mb4_general_ci' AFTER `weigh`;

-- 题目新增材料题子题字段
ALTER TABLE `__PREFIX__exam_question`
    ADD COLUMN `is_material_child` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '属于材料题子题：0=否，1=是' AFTER `status`,
DROP INDEX `kind`,
	ADD INDEX `kind` (`kind`, `status`, `is_material_child`) USING BTREE,
DROP INDEX `cate_id`,
	ADD INDEX `cate_id` (`cate_id`, `kind`, `status`, `is_material_child`) USING BTREE,
	ADD INDEX `is_material_child` (`is_material_child`),
    ADD COLUMN `material_question_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属材料题' AFTER `is_material_child`,
	ADD INDEX `material_question_id` (`material_question_id`),
    ADD COLUMN `material_score` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '材料子题分数' AFTER `material_question_id`;

-- 1.4.5
-- 试题新增题目视频字段
ALTER TABLE `__PREFIX__exam_question`
    ADD COLUMN `title_video` VARCHAR(200) NULL COMMENT '题目视频' AFTER `title`,
	ADD COLUMN `explain_video` VARCHAR(200) NULL COMMENT '解析视频' AFTER `explain`;

-- 错题记录用户答案字段扩展长度（某些旧版本没有更新表结构）
ALTER TABLE `__PREFIX__exam_question_wrong`
    CHANGE COLUMN `user_answer` `user_answer` TEXT NOT NULL COMMENT '用户答案' COLLATE 'utf8mb4_general_ci';
ALTER TABLE `__PREFIX__exam_room_grade`
    CHANGE COLUMN `configs` `configs` TEXT NULL COMMENT '试卷选题配置' COLLATE 'utf8mb4_general_ci' AFTER `user_answers`,
    CHANGE COLUMN `user_answers` `user_answers` TEXT NULL COMMENT '用户答案集合' COLLATE 'utf8mb4_general_ci' AFTER `error_ids`;
ALTER TABLE `__PREFIX__exam_grade`
    CHANGE COLUMN `configs` `configs` TEXT NULL COMMENT '试卷选题配置' COLLATE 'utf8mb4_general_ci' AFTER `user_answers`,
    CHANGE COLUMN `user_answers` `user_answers` TEXT NULL COMMENT '用户答案集合' COLLATE 'utf8mb4_general_ci' AFTER `error_ids`;

-- 1.5.2
CREATE TABLE IF NOT EXISTS `__PREFIX__exam_correction_type` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '类型名称' COLLATE 'utf8_general_ci',
    `createtime` BIGINT(20) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(20) NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE
    ) COMMENT='纠错反馈类型' COLLATE='utf8_general_ci' ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `__PREFIX__exam_correction_question` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '反馈人',
    `question_id` INT(11) NOT NULL DEFAULT '0' COMMENT '反馈题目',
    `type_ids` INT(11) NOT NULL DEFAULT '0' COMMENT '纠错类型',
    `type_names` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '类型名称' COLLATE 'utf8_general_ci',
    `remark` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '其他说明' COLLATE 'utf8_general_ci',
    `status` ENUM('0','1','2') NOT NULL DEFAULT '0' COMMENT '状态:0=未处理,1=已处理,2=忽略' COLLATE 'utf8_general_ci',
    `message` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '处理说明' COLLATE 'utf8_general_ci',
    `createtime` BIGINT(20) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(20) NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `question_id` (`question_id`) USING BTREE,
    INDEX `user_id` (`user_id`) USING BTREE
    ) COMMENT='纠错反馈试题' COLLATE='utf8_general_ci' ENGINE=InnoDB;

-- 1.5.4

CREATE TABLE IF NOT EXISTS `__PREFIX__exam_news` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `images` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '图片集' COLLATE 'utf8mb4_general_ci',
    `cover_image` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '封面' COLLATE 'utf8mb4_general_ci',
    `name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '标题' COLLATE 'utf8mb4_general_ci',
    `contents` LONGTEXT NULL DEFAULT NULL COMMENT '内容' COLLATE 'utf8mb4_general_ci',
    `weigh` INT(11) NOT NULL DEFAULT '1' COMMENT '排序',
    `status` ENUM('NORMAL','HIDDEN') NOT NULL DEFAULT 'NORMAL' COMMENT '状态' COLLATE 'utf8mb4_general_ci',
    `front_info` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '前端跳转信息' COLLATE 'utf8mb4_general_ci',
    `createtime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE
    ) COMMENT='学习动态' COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;

-- 1.5.5

CREATE TABLE IF NOT EXISTS `__PREFIX__exam_cert_config` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL COMMENT '名称' COLLATE 'utf8mb4_general_ci',
    `remark` VARCHAR(1000) NULL DEFAULT NULL COMMENT '说明' COLLATE 'utf8mb4_general_ci',
    `status` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '状态:0=禁用,1=启用' COLLATE 'utf8_general_ci',
    `expire_day` INT(11) NULL DEFAULT NULL COMMENT '有效天数',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '修改时间',
    `deletetime` BIGINT(16) NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `status` (`status`) USING BTREE
    ) COMMENT='证书配置' COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `__PREFIX__exam_cert_template` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cert_config_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属证书配置',
    `name` VARCHAR(50) NOT NULL COMMENT '名称' COLLATE 'utf8mb4_general_ci',
    `min_score` INT(11) NOT NULL DEFAULT '0' COMMENT '证书最低分数',
    `image` VARCHAR(100) NOT NULL COMMENT '证书图片' COLLATE 'utf8mb4_general_ci',
    `field_config` TEXT NOT NULL COMMENT '字段配置' COLLATE 'utf8mb4_general_ci',
    `status` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '状态:0=禁用,1=正常' COLLATE 'utf8_general_ci',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '修改时间',
    `deletetime` BIGINT(16) NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `status` (`status`, `cert_config_id`) USING BTREE
    ) COMMENT='证书模板' COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `__PREFIX__exam_cert` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cert_config_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '证书配置',
    `cert_template_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '证书模板',
    `user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属用户',
    `paper_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属试卷',
    `room_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属考场',
    `name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '证书名称' COLLATE 'utf8mb4_general_ci',
    `user_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '姓名' COLLATE 'utf8mb4_general_ci',
    `score` INT(11) NOT NULL DEFAULT '0' COMMENT '考试分数',
    `image` VARCHAR(100) NOT NULL COMMENT '证书图片' COLLATE 'utf8mb4_general_ci',
    `status` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '状态:0=失效,1=正常' COLLATE 'utf8_general_ci',
    `source` ENUM('paper','room','manual') NOT NULL DEFAULT 'paper' COMMENT '来源:paper=试卷考试,room=考场考试,manual=手动创建' COLLATE 'utf8mb4_general_ci',
    `expire_time` BIGINT(16) NULL DEFAULT NULL COMMENT '过期时间',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '修改时间',
    `deletetime` BIGINT(16) NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `status` (`status`, `cert_config_id`, `cert_template_id`, `user_id`, `paper_id`, `room_id`) USING BTREE
    ) COMMENT='证书管理' COLLATE='utf8mb4_general_ci';

CREATE TABLE IF NOT EXISTS `__PREFIX__exam_school` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '学校名称' COLLATE 'utf8mb4_general_ci',
    `weigh` INT(11) NOT NULL DEFAULT '1' COMMENT '排序',
    `status` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '状态:0=禁用，1=启用' COLLATE 'utf8mb4_general_ci',
    `createtime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE
    ) COMMENT='学校管理' COLLATE='utf8mb4_general_ci';

ALTER TABLE `__PREFIX__exam_paper`
    ADD COLUMN `cert_config_id` INT(11) NULL DEFAULT 0 COMMENT '证书生成配置' AFTER `is_only_room`;

ALTER TABLE `__PREFIX__exam_room`
    ADD COLUMN `cert_config_id` INT(11) NULL DEFAULT 0 COMMENT '证书生成配置' AFTER `pass_rate`;

ALTER TABLE `__PREFIX__exam_room_signup`
    ADD COLUMN `school_id` INT(11) UNSIGNED NULL DEFAULT 0 COMMENT '所属学校' AFTER `room_id`,
	ADD COLUMN `class_name` VARCHAR(30) NULL DEFAULT '' COMMENT '班级' AFTER `user_id`;

ALTER TABLE `__PREFIX__exam_room_grade`
    ADD COLUMN `school_id` INT(11) UNSIGNED NULL DEFAULT '0' COMMENT '所属学校' AFTER `paper_id`,
	ADD COLUMN `class_name` VARCHAR(30) NULL DEFAULT '' COMMENT '班级' AFTER `school_id`,
	ADD INDEX `school_id` (`school_id`, `class_name`);


ALTER TABLE `__PREFIX__exam_cate`
    ADD COLUMN `is_look` TINYINT NOT NULL DEFAULT 1 COMMENT '是否在看题页面显示' AFTER `status`,
	ADD COLUMN `is_train` TINYINT NOT NULL DEFAULT 1 COMMENT '是否在练题页面显示' AFTER `is_look`,
    ADD COLUMN `status` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '状态:0=禁用,1=启用' COLLATE 'utf8_general_ci';


-- 1.5.6

ALTER TABLE `__PREFIX__exam_room`
    CHANGE COLUMN `signup_count` `signup_count` INT NOT NULL DEFAULT 0 COMMENT '报考人数' AFTER `is_rank`,
    CHANGE COLUMN `grade_count` `grade_count` INT NOT NULL DEFAULT 0 COMMENT '考试人数' AFTER `signup_count`,
    CHANGE COLUMN `pass_count` `pass_count` INT NOT NULL DEFAULT 0 COMMENT '及格人数' AFTER `grade_count`;

ALTER TABLE `__PREFIX__exam_room_grade`
    CHANGE COLUMN `total_score` `total_score` INT NOT NULL DEFAULT 0 COMMENT '总分数' AFTER `is_makeup`,
    CHANGE COLUMN `total_count` `total_count` INT NOT NULL DEFAULT 0 COMMENT '总题数' AFTER `total_score`,
    CHANGE COLUMN `right_count` `right_count` INT NOT NULL DEFAULT 0 COMMENT '答对数' AFTER `total_count`,
    CHANGE COLUMN `error_count` `error_count` INT NOT NULL DEFAULT 0 COMMENT '答错数' AFTER `right_count`,
    CHANGE COLUMN `rank` `rank` INT NOT NULL DEFAULT 0 COMMENT '本次考试排名' AFTER `error_count`;

ALTER TABLE `__PREFIX__exam_paper`
    ADD COLUMN `is_prevent_switch_screen` TINYINT(4) NULL DEFAULT '0' COMMENT '是否防切屏' AFTER `cert_config_id`,
	ADD COLUMN `switch_screen_count` TINYINT(4) NULL DEFAULT '0' COMMENT '允许切屏次数' AFTER `is_prevent_switch_screen`,
	ADD COLUMN `switch_screen_second` TINYINT(4) NULL DEFAULT '0' COMMENT '切屏认定秒数' AFTER `switch_screen_count`;


-- 1.5.8
ALTER TABLE `__PREFIX__exam_paper`
    CHANGE COLUMN `quantity` `quantity` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '题目数量' AFTER `configs`;

ALTER TABLE `__PREFIX__exam_room`
    ADD COLUMN `is_create_qrcode_h5` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '是否生成考场H5二维码' AFTER `cert_config_id`,
    ADD COLUMN `qrcode_h5` VARCHAR(200) NULL DEFAULT NULL COMMENT '考场二维码' AFTER `is_create_qrcode_h5`;

CREATE TABLE IF NOT EXISTS `__PREFIX__exam_friend_apps` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '名称' COLLATE 'utf8mb4_general_ci',
    `image` VARCHAR(200) NOT NULL COMMENT '图标' COLLATE 'utf8mb4_general_ci',
    `miniapp_code_image` VARCHAR(200) NULL DEFAULT NULL COMMENT '小程序码图片' COLLATE 'utf8mb4_general_ci',
    `wx_app_id` VARCHAR(50) NULL DEFAULT NULL COMMENT '小程序APPID' COLLATE 'utf8mb4_general_ci',
    `status` ENUM('1','0') NOT NULL DEFAULT '1' COMMENT '状态:0=禁用，1=启用' COLLATE 'utf8mb4_general_ci',
    `weigh` INT(11) NULL DEFAULT '1' COMMENT '排序',
    `createtime` BIGINT(16) NULL DEFAULT NULL,
    `updatetime` BIGINT(16) UNSIGNED NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `status` (`status`) USING BTREE
) COMMENT='友情小程序' COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;


-- 1.5.10
ALTER TABLE `__PREFIX__exam_paper`
    ADD COLUMN `cover_image` VARCHAR(200) NULL DEFAULT '' COMMENT '封面图片' AFTER `configs`;

ALTER TABLE `__PREFIX__exam_room`
    ADD COLUMN `cover_image` VARCHAR(200) NULL DEFAULT '' COMMENT '封面图片' AFTER `contents`;

-- 1.6.0
ALTER TABLE `__PREFIX__exam_cate`
    ADD COLUMN `subject_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属科目' AFTER `id`,
	ADD INDEX `subject_id` (`subject_id`);

ALTER TABLE `__PREFIX__exam_paper`
    ADD COLUMN `subject_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属科目' AFTER `cate_id`,
	ADD INDEX `subject_id` (`subject_id`, `status`);

ALTER TABLE `__PREFIX__exam_room`
    ADD COLUMN `subject_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属科目' AFTER `cate_id`,
	ADD INDEX `subject_id` (`subject_id`, `status`);


CREATE TABLE `__PREFIX__exam_subject` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `level` ENUM('1','2','3') NOT NULL DEFAULT '1' COMMENT '层级' COLLATE 'utf8mb4_general_ci',
    `name` VARCHAR(50) NOT NULL COMMENT '名称' COLLATE 'utf8mb4_general_ci',
    `icon` VARCHAR(200) NULL DEFAULT '' COMMENT '图标' COLLATE 'utf8mb4_general_ci',
    `parent_id` INT(11) NOT NULL DEFAULT '0' COMMENT '父级',
    `weigh` INT(11) NOT NULL DEFAULT '1' COMMENT '排序',
    `remark` VARCHAR(1000) NULL DEFAULT NULL COMMENT '简介' COLLATE 'utf8mb4_general_ci',
    `status` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '状态:0=禁用,1=启用' COLLATE 'utf8_general_ci',
    `createtime` BIGINT(16) NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) NULL DEFAULT NULL COMMENT '修改时间',
    `deletetime` BIGINT(16) NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `parent_id` (`parent_id`) USING BTREE,
    INDEX `status` (`status`) USING BTREE
) COMMENT='科目'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_diy_index_button` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '按钮名称' COLLATE 'utf8mb4_general_ci',
    `page_style` ENUM('color','color2','simple') NOT NULL DEFAULT 'color2' COMMENT '所属页面风格:color=多彩风格1，color2=多彩风格2，simple=精简风格' COLLATE 'utf8mb4_general_ci',
    `type` ENUM('icon','image') NOT NULL DEFAULT 'icon' COMMENT '图标展示方式:icon=图标,image=图片' COLLATE 'utf8mb4_general_ci',
    `icon` VARCHAR(30) NULL DEFAULT '' COMMENT '按钮图标' COLLATE 'utf8mb4_general_ci',
    `color` VARCHAR(50) NULL DEFAULT '' COMMENT '图标颜色' COLLATE 'utf8mb4_general_ci',
    `bg_color` VARCHAR(50) NULL DEFAULT '' COMMENT '背景颜色' COLLATE 'utf8mb4_general_ci',
    `image` VARCHAR(100) NULL DEFAULT '' COMMENT '按钮图片' COLLATE 'utf8mb4_general_ci',
    `path` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '跳转页面' COLLATE 'utf8mb4_general_ci',
    `weigh` INT(11) NOT NULL DEFAULT '1' COMMENT '排序',
    `status` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '状态:0=禁用，1=启用' COLLATE 'utf8mb4_general_ci',
    `createtime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE
) COMMENT='自定义首页按钮'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `__PREFIX__exam_diy_tabbar` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '按钮名称' COLLATE 'utf8mb4_general_ci',
    `icon` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '按钮图标' COLLATE 'utf8mb4_general_ci',
    `path` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '跳转页面' COLLATE 'utf8mb4_general_ci',
    `weigh` INT(11) NOT NULL DEFAULT '1' COMMENT '排序',
    `status` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '状态:0=禁用，1=启用' COLLATE 'utf8mb4_general_ci',
    `createtime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE
) COMMENT='自定义导航栏'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB;

ALTER TABLE `__PREFIX__exam_user_info`
    ADD COLUMN `only_subject_ids` VARCHAR(1000) NULL DEFAULT NULL COMMENT '可用科目' AFTER `expire_time`;

ALTER TABLE `__PREFIX__exam_member_code`
    ADD COLUMN `remark` VARCHAR(500) NULL DEFAULT '' COMMENT '备注' AFTER `user_id`;

ALTER TABLE `__PREFIX__exam_paper`
    CHANGE COLUMN `total_score` `total_score` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '试卷总分' AFTER `quantity`,
    CHANGE COLUMN `pass_score` `pass_score` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '及格线' AFTER `total_score`;

ALTER TABLE `__PREFIX__exam_grade`
    CHANGE COLUMN `score` `score` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '考试分数' AFTER `mode`,
    CHANGE COLUMN `system_score` `system_score` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '系统得分' AFTER `score`,
    CHANGE COLUMN `manual_score` `manual_score` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '人工判分' AFTER `system_score`,
    CHANGE COLUMN `total_score` `total_score` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '总分数' AFTER `is_pass`,
    CHANGE COLUMN `total_count` `total_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '总题数' AFTER `total_score`,
    CHANGE COLUMN `right_count` `right_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '答对数' AFTER `total_count`,
    CHANGE COLUMN `error_count` `error_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '答错数' AFTER `right_count`;

ALTER TABLE `__PREFIX__exam_room_grade`
    CHANGE COLUMN `score` `score` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '考试分数' AFTER `mode`,
    CHANGE COLUMN `system_score` `system_score` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '系统得分' AFTER `score`,
    CHANGE COLUMN `manual_score` `manual_score` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '人工判分' AFTER `system_score`,
    CHANGE COLUMN `question_ids` `question_ids` TEXT NULL COMMENT '试卷ID集合' COLLATE 'utf8mb4_general_ci' AFTER `grade_time`,
    CHANGE COLUMN `error_ids` `error_ids` TEXT NULL COMMENT '错题ID集合' COLLATE 'utf8mb4_general_ci' AFTER `question_ids`;

ALTER TABLE `__PREFIX__exam_member_config`
    ADD COLUMN `uses` ENUM('all','cate') NOT NULL DEFAULT 'all' COMMENT '可用范围:0=所有题库,1=部分题库' COLLATE 'utf8_general_ci' AFTER `tag`,
	ADD COLUMN `cate_ids` TEXT NULL DEFAULT NULL COMMENT '可用题库' COLLATE 'utf8_general_ci' AFTER `uses`;

ALTER TABLE `__PREFIX__exam_member_config`
    ADD COLUMN `paper_uses` ENUM('all','part_cate') NOT NULL DEFAULT 'all' COMMENT '可用范围:all=所有试卷,part_cate=部分试卷分类' COLLATE 'utf8_general_ci' AFTER `cate_ids`,
	ADD COLUMN `paper_cate_ids` TEXT NULL DEFAULT NULL COMMENT '可用试卷分类' COLLATE 'utf8_general_ci' AFTER `paper_uses`;

ALTER TABLE `__PREFIX__exam_member_order`
    ADD COLUMN `uses` ENUM('all','cate','subject') NULL DEFAULT 'all' COMMENT '可用范围:0=所有题库,1=部分题库' COLLATE 'utf8_general_ci' AFTER `member_config_id`,
	ADD COLUMN `cate_ids` TEXT NULL DEFAULT NULL COMMENT '可用题库' COLLATE 'utf8_general_ci' AFTER `uses`;

ALTER TABLE `__PREFIX__exam_question`
    CHANGE COLUMN `explain` `explain` TEXT NULL COMMENT '解析' COLLATE 'utf8_general_ci' AFTER `title_video`;

ALTER TABLE `__PREFIX__exam_grade`
    ADD COLUMN `pass_score` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '及格线' AFTER `is_pass`;

ALTER TABLE `__PREFIX__exam_room_grade`
    ADD COLUMN `pass_score` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '及格线' AFTER `is_makeup`;

-- 1.7.0 错题记录表新增来源试卷和考场字段
ALTER TABLE `__PREFIX__exam_question_wrong`
    ADD COLUMN `cate_id` INT(11) UNSIGNED NULL DEFAULT '0' COMMENT '所属题库' AFTER `kind`,
    ADD COLUMN `paper_id` INT(11) UNSIGNED NULL DEFAULT '0' COMMENT '来源试卷' AFTER `cate_id`,
	ADD COLUMN `room_id` INT(11) UNSIGNED NULL DEFAULT '0' COMMENT '来源考场' AFTER `paper_id`;

CREATE TABLE `__PREFIX__exam_slide` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `image` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '图片' COLLATE 'utf8mb4_general_ci',
    `weigh` INT(11) NOT NULL DEFAULT '1' COMMENT '排序',
    `status` ENUM('NORMAL','HIDDEN') NOT NULL DEFAULT 'NORMAL' COMMENT '状态' COLLATE 'utf8mb4_general_ci',
    `front_info` VARCHAR(1000) NULL DEFAULT '' COMMENT '前端跳转信息' COLLATE 'utf8mb4_general_ci',
    `createtime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
    `updatetime` BIGINT(16) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
    PRIMARY KEY (`id`) USING BTREE
) COMMENT='轮播图'
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB;

ALTER TABLE `__PREFIX__exam_paper`
    ADD COLUMN `pay_effect_days` INT NULL DEFAULT NULL COMMENT '付费有效天数' AFTER `member_price`;
ALTER TABLE `__PREFIX__exam_paper_order`
    ADD COLUMN `expire_time` BIGINT(16) NULL DEFAULT NULL COMMENT '过期时间' AFTER `pay_time`;
ALTER TABLE `__PREFIX__exam_friend_apps`
    ADD COLUMN `path` VARCHAR(100) NULL DEFAULT '/pages/index/index' COMMENT '小程序首页' AFTER `wx_app_id`;

-- 1.8.2
ALTER TABLE `__PREFIX__exam_cate_code`
    ADD COLUMN `remark` VARCHAR(500) NULL DEFAULT '' COMMENT '备注' COLLATE 'utf8mb4_unicode_ci' AFTER `days`;
