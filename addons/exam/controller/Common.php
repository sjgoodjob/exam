<?php

namespace addons\exam\controller;

use addons\exam\enum\CommonStatus;
use addons\exam\enum\GeneralStatus;
use addons\exam\enum\UserScoreType;
use addons\exam\library\ScoreService;
use addons\exam\model\CateModel;
use addons\exam\model\CateUserLogModel;
use addons\exam\model\NewsModel;
use addons\exam\model\PaperModel;
use addons\exam\model\RoomGradeModel;
use addons\exam\model\RoomModel;
use addons\exam\model\UserModel;
use app\admin\model\exam\ConfigInfoModel;
use app\admin\model\exam\DiyIndexButtonModel;
use app\admin\model\exam\DiyTabbarModel;
use app\admin\model\exam\FriendAppsModel;
use app\admin\model\exam\NoticeModel;
use app\common\exception\UploadException;
use app\common\library\Upload;
use think\Config;
use addons\exam\model\SlideModel;

/**
 * 公共接口
 */
class Common extends Base
{
    protected $noNeedLogin = ['index', 'login', 'friendApps'];
    protected $noNeedRight = ['*'];

    /**
     * 读取配置
     */
    public function index()
    {
        if (!$config = ConfigInfoModel::getOne()) {
            $this->error('系统未初始化配置');
        }

        // 加载配置
        $data['system'] = json_decode($config['system_config'], true);
        $data['page']   = json_decode($config['page_config'], true);
        $data['ad']     = json_decode($config['ad_config'], true);
        $data['score']  = json_decode($config['score_config'], true);
        $member_config  = json_decode($config['member_config'], true);
        $data['member'] = $member_config;
        if (!empty($data['system']['banner'])) {
            $banners = explode(',', $data['system']['banner']);
            foreach ($banners as &$banner) {
                $banner = cdnurl($banner, true);
            }
            $data['system']['banner'] = implode(',', $banners);
        }

        // 登录方式默认值
        if (empty($data['system']['login_channel'])) {
            $data['system']['login_channel'] = 'wechat_and_account';
        }

        // 轮播图
        $data['slides'] = SlideModel::where('status', CommonStatus::NORMAL)->order('weigh', 'desc')->select();
        $data['slides'] = $data['slides'] ?: [];

        // 加载首页数据
        $data['notices'] = NoticeModel::where('status', CommonStatus::NORMAL)->order('weigh desc')->field('id,name')->limit(5)->select();
        // $data['notice']  = NoticeModel::where('status', CommonStatus::NORMAL)->order('weigh desc')->column('name');
        $data['news']   = NewsModel::where('status', CommonStatus::NORMAL)
            ->order('weigh desc')
            ->field('id,name,images,createtime')
            ->limit(5)
            ->select();
        $data['papers'] = $this->indexPaperList();
        $data['rooms']  = $this->indexRoomList();
        // $data['open_cates'] = $this->indexOpenCateList();
        // $data['wait_open_cates'] = $this->indexWaitOpenCateList($data['open_cates']);

        // 加载用户信息
        if ($this->auth->getUser()) {
            $user         = $this->auth->getUser()->visible(['id', 'avatar', 'nickname', 'status', 'createtime', 'logintime'])->toArray();
            $user['info'] = UserModel::getInfo($this->auth->id);
            $data['user'] = $user;

            // 隐藏广告且是会员
            if (($member_config['member_show_ad'] ?? 1) == 0 && UserModel::isMember($this->auth->id)) {
                // 把所有流量主广告ID置空
                $data['ad'] = [];
            }
        }

        // 登录得积分
        $data['point']   = [
            'get_point' => ScoreService::getScore($this->auth->id, UserScoreType::LOGIN),
            'type'      => UserScoreType::getDescription(UserScoreType::LOGIN),
        ];
        $data['version'] = APP_VERSION;

        // cdn域名
        $data['cdn_url'] = cdnurl('', true);

        // 自定义tabbar配置
        $tabbar = DiyTabbarModel::where('status', GeneralStatus::NORMAL)
            ->field('name,icon,path')
            ->order('weigh asc')
            ->limit(5)
            ->select();
        if ($tabbar) {
            $tabbar = collection($tabbar)->toArray();
            foreach ($tabbar as &$item) {
                $item['icon'] = "tn-icon-{$item['icon']}";
            }
            $data['tabbar'] = $tabbar;
        }

        if (!empty($data['page']['page_index_style'])) {
            // 自定义首页按钮
            $diy_index_buttons = DiyIndexButtonModel::where('status', GeneralStatus::NORMAL)
                ->where('page_style', $data['page']['page_index_style'])
                ->field('name,type,icon,color,bg_color,image,path')
                ->order('weigh asc')
                ->select();
            if ($diy_index_buttons) {
                $diy_index_buttons = collection($diy_index_buttons)->toArray();
                foreach ($diy_index_buttons as &$item) {
                    if ($item['type'] == 'image') {
                        $item['image'] = cdnurl($item['image'], true);
                    } else if ($item['type'] == 'icon') {
                        $item['icon'] = "tn-icon-{$item['icon']}";
                    }
                }
                $data['diy_index_button'] = $diy_index_buttons;
            }
        }

        $this->success('请求成功', $data);
    }

    /**
     * 上传文件
     */
    public function upload()
    {
        Config::set('default_return_type', 'json');
        //必须设定cdnurl为空,否则cdnurl函数计算错误
        Config::set('upload.cdnurl', '');
        $chunkid = $this->request->post("chunkid");
        if ($chunkid) {
            if (!Config::get('upload.chunking')) {
                $this->error(__('Chunk file disabled'));
            }
            $action     = $this->request->post("action");
            $chunkindex = $this->request->post("chunkindex/d");
            $chunkcount = $this->request->post("chunkcount/d");
            $filename   = $this->request->post("filename");
            $method     = $this->request->method(true);
            if ($action == 'merge') {
                $attachment = null;
                //合并分片文件
                try {
                    $upload     = new Upload();
                    $attachment = $upload->merge($chunkid, $chunkcount, $filename);
                } catch (UploadException $e) {
                    $this->error($e->getMessage());
                }
                $this->success(__('Uploaded successful'), ['url' => $attachment->url, 'fullurl' => cdnurl($attachment->url, true)]);
            } elseif ($method == 'clean') {
                //删除冗余的分片文件
                try {
                    $upload = new Upload();
                    $upload->clean($chunkid);
                } catch (UploadException $e) {
                    $this->error($e->getMessage());
                }
                $this->success();
            } else {
                //上传分片文件
                //默认普通上传文件
                $file = $this->request->file('file');
                try {
                    $upload = new Upload($file);
                    $upload->chunk($chunkid, $chunkindex, $chunkcount);
                } catch (UploadException $e) {
                    $this->error($e->getMessage());
                }
                $this->success();
            }
        } else {
            $attachment = null;
            //默认普通上传文件
            $file = $this->request->file('file');
            try {
                $upload     = new Upload($file);
                $attachment = $upload->upload();
            } catch (UploadException $e) {
                $this->error($e->getMessage());
            }

            $this->success(__('Uploaded successful'), ['url' => $attachment->url, 'fullurl' => cdnurl($attachment->url, true)]);
        }
    }

    /**
     * 首页试卷列表
     *
     * @return array
     */
    protected function indexPaperList()
    {
        $now        = time();
        $subject_id = input('subject_id/d', 0);
        // $cate_ids   = [];
        // if ($subject_id) {
        //     $cate_ids = CateModel::where('subject_id', $subject_id)->column('id');
        // }

        $query = PaperModel::where('status', CommonStatus::NORMAL)
            ->where('is_only_room', 0)
            ->whereRaw("((start_time = 0 and end_time = 0) or (start_time < {$now} and end_time > {$now}))");
        if ($subject_id) {
            $query->where('subject_id', $subject_id);
            // if ($cate_ids) {
            //     $query->where('cate_id', 'in', $cate_ids);
            // } else {
            //     $query->where('cate_id', 0);
            // }
        }

        $papers = $query->order('join_count', 'desc')->limit(6)->select();
        foreach ($papers as &$paper) {
            // 试卷参与人员
            $users = PaperModel::getJoinUsers($paper['id'], -4);
            // 参与人员头像
            $user_avatars = [];
            foreach ($users as $user) {
                $user_avatars[] = [
                    'src' => $user['avatar'],
                ];
            }
            $paper['users'] = $user_avatars;
        }

        return $papers;
    }

    /**
     * 首页考场列表
     *
     * @return array
     */
    protected function indexRoomList()
    {
        $subject_id = input('subject_id/d', 0);

        $query = RoomModel::where('status', CommonStatus::NORMAL);
        if ($subject_id) {
            $query->where('subject_id', $subject_id);
        }

        $rooms = $query->order('grade_count', 'desc')->limit(6)->select();
        foreach ($rooms as &$room) {
            // 考场考试人员
            $users = RoomGradeModel::getJoinUsers($room['id'], -4);
            // 参与人员头像
            $user_avatars = [];
            foreach ($users as $user) {
                $user_avatars[] = [
                    'src' => $user['avatar'],
                ];
            }
            $room['users'] = $user_avatars;
        }

        return $rooms;
    }

    /**
     * 首页已开通题库列表
     *
     * @return array
     */
    protected function indexOpenCateList()
    {
        if (!$this->auth->getUser()) {
            return [];
        }

        $cates = CateUserLogModel::with(['cate'])
            ->where('user_id', $this->auth->id)
            ->where(function ($query) {
                $query->where('expire_time', '>', time())
                    ->whereOr('expire_time', 0);
            })
            ->limit(10)
            ->select();

        return $cates;
    }

    /**
     * 首页待开通题库列表
     *
     * @return array
     */
    protected function indexWaitOpenCateList($open_cates)
    {
        if (!$this->auth->getUser()) {
            return [];
        }

        $open_cate_ids = array_column($open_cates, 'cate_id');

        $query = CateModel::with([])
            ->where('status', GeneralStatus::NORMAL)
            ->where('is_free', 0);

        if ($open_cate_ids) {
            $query->whereNotIn('id', $open_cate_ids);
        }

        $cates = $query->field('id,name,parent_id')->limit(20)->select();
        $cates = $cates ? collection($cates)->toArray() : [];
        $list  = [];
        foreach ($cates as $cate) {
            $child_count = CateModel::where('parent_id', $cate['id'])->count();
            if (!$child_count) {
                $list[] = $cate;
            }
        }

        return $list;
    }

    /**
     * 友情小程序
     */
    public function friendApps()
    {
        $apps = FriendAppsModel::where('status', 1)->order('weigh desc')->select();
        $this->success('请求成功', $apps);
    }
}
