
INSERT INTO `__PREFIX__exam_cate` (`id`, `kind`, `level`, `name`, `parent_id`, `sort`, `remark`, `deletetime`) VALUES
(1, 'QUESTION', '1', '消防', 0, 1, '', NULL),
(2, 'ROOM', '1', '消防知识考场', 0, 1, '', NULL),
(3, 'PAPER', '1', '消防知识试卷', 0, 1, '', NULL);

INSERT INTO `__PREFIX__exam_config_info` (`id`, `ad_config`, `system_config`, `wx_config`) VALUES (1, NULL, NULL, NULL);

INSERT INTO `__PREFIX__exam_notice` (`id`, `name`, `contents`, `weigh`, `status`, `createtime`, `updatetime`) VALUES
(1, '测试公告很长很长很长很长很长很长很长的内容', '', 1, 'NORMAL', 1654088122, 1654088122),
(2, '测试公告很短的内容', '', 2, 'NORMAL', 1654088131, 1654088131);

INSERT INTO `__PREFIX__exam_paper` (`id`, `cate_id`, `mode`, `title`, `configs`, `quantity`, `total_score`, `pass_score`, `limit_time`, `join_count`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES
    (1, 3, 'RANDOM', '消防知识考卷', '{"cate_ids":"1","all":{"total_total":5,"quantity":5,"total_score":100},"judge":{"difficulty":{"easy":{"count":0,"score":0,"total_score":0},"general":{"count":0,"score":0,"total_score":0},"hard":{"count":0,"score":0,"total_score":0}},"count":0,"score":0,"total_score":0,"use_difficulty":false},"single":{"difficulty":{"easy":{"count":0,"score":0,"total_score":0},"general":{"count":0,"score":0,"total_score":0},"hard":{"count":0,"score":0,"total_score":0}},"count":5,"score":20,"total_score":100,"use_difficulty":false},"multi":{"difficulty":{"easy":{"count":0,"score":0,"total_score":0},"general":{"count":0,"score":0,"total_score":0},"hard":{"count":0,"score":0,"total_score":0}},"count":0,"score":0,"total_score":0,"use_difficulty":false}}', 5, 100, 60, 3600, 1, 'NORMAL', 1654085409, 1654088972, NULL);

INSERT INTO `__PREFIX__exam_question` (`id`, `cate_id`, `kind`, `title`, `explain`, `difficulty`, `options_json`, `options_img`, `answer`, `status`, `createtime`, `updatetime`, `deletetime`) VALUES
(1, 1, 'SINGLE', '消防工作应当坚持（ ）的原则。', '', 'GENERAL', '{"A":"隐患险于明火，防范胜于救灾","B":"预防为主，防消结合","C":"专门机关与群众相结合","D":"政府统一领导、部门依法监管、单位全面负责、公民积极参与"}', NULL, 'D', 'NORMAL', 1653487790, 1653487790, NULL),
(2, 1, 'SINGLE', '（ ）应当根据经济和社会发展的需要，建立各种形式的消防组织，加强消防组织建设，增强扑救火灾的能力。', '', 'GENERAL', '{"A":"各级消防机构","B":"城市人民政府","C":"县以上人民政府","D":"各级人民政府"}', NULL, 'D', 'NORMAL', 1653487790, 1653487790, NULL),
(3, 1, 'SINGLE', '碳水化合物是指 ', '', 'GENERAL', '{"A":"二氧化碳","B":"糖","C":"蛋白质","D":"酒精"}', NULL, 'A', 'NORMAL', 1653487790, 1653487790, NULL),
(4, 1, 'SINGLE', '（ ）级以上地方各级人民政府消防机构应当将发生火灾可能性较大以及一旦发生火灾可能造成人身重大伤亡或者财产重大损失的单位，确定为本行政区域内的消防安全重点单位，报本级人民政府备案。', '', 'GENERAL', '{"A":"省","B":"市","C":"县","D":"乡"}', NULL, 'C', 'NORMAL', 1653487790, 1653487790, NULL),
(5, 1, 'SINGLE', '专职消防队建立以后，应当报（ ）消防机构验收。', '', 'GENERAL', '{"A":"本地","B":"省级人民政府","C":"市级人民政府","D":"当地"}', NULL, 'D', 'NORMAL', 1653487790, 1653487790, NULL);

INSERT INTO `__PREFIX__exam_room` (`id`, `name`, `contents`, `cate_id`, `paper_id`, `people_count`, `start_time`, `end_time`, `weigh`, `status`, `signup_mode`, `password`, `is_makeup`, `makeup_count`, `is_rank`, `signup_count`, `grade_count`, `pass_count`, `pass_rate`, `createtime`, `updatetime`, `deletetime`) VALUES
(1, '消防知识考场', '这是消防知识考场的说明', 2, 1, 0, 1654088194, 1656593815, 1, 'NORMAL', 'NORMAL', '', 0, 0, 0, 0, 0, 0, 0.00, 1654088231, 1654089042, NULL);



