<template>
  <view class="questions">

    <!-- 头部信息 -->
    <view class="test-header" v-if="showCountDown">
      <tui-countdown :time="limit_time" borderColor="#FFF" color="#080808" :size="36" :colonSize="36"
                     @end="endOfTime"></tui-countdown>
    </view>

    <!-- 题目列表 -->
    <view class="card-shadow">
      <view class="topic-title">
        <view class="topic-title_left">
          <!-- 题型 -->
          <view class="text-kind">
            {{ kindText() }}
          </view>
          <!-- 收藏 -->
          <view class="test-favor" v-if="canCollect">
            <tui-icon name="like-fill" color="#f74d54" :size="18" @click="collectDel()"
                      v-if="list[swiperIndex-1] && list[swiperIndex-1].collected"></tui-icon>
            <tui-icon name="like" color="#aaa" :size="18" @click="collectAdd()" v-else></tui-icon>
            <view class="tui-fabulous"
                  :class="{ 'tui-fabulous__active': list[swiperIndex-1] && list[swiperIndex-1].collected }">
              <tui-icon name="like-fill" color="#f74d54"></tui-icon>
            </view>
          </view>
        </view>
        <!-- 题标 -->
        <view class="topic-title_right">
          <text class="title-index">{{ swiperIndex }}</text>
          /{{ total }}
        </view>
      </view>
      <!-- 防切屏提示 -->
      <view class="topic-title" style="font-size: 24rpx;" v-if="isPreventSwitchScreen">
        注意：当前考试开启了防切屏，切屏次数超过{{ switchScreenCount }}次考试将自动结束
      </view>

      <swiper :current="swiperIndex - 1" @change="swiperChange" class="questions-cont">
        <swiper-item v-for="(item, index) in list" :key="index" class="swiper-item">
          <view v-if="Math.abs(swiperIndex - 1 - index) <= 30">
            <block v-if="index == swiperIndex - 1">
              <!-- 材料题标题 -->
              <view class="material-title" v-if="item.material_title != undefined">
                <view class="material-title-tip">材料：</view>
                <view v-if="!item.show_full">
                  <!-- <rich-text :nodes="questionTitle(swiperIndex, item, 'material_title')"></rich-text> -->
                  <mp-html :content="questionTitle(swiperIndex, item, 'material_title')"></mp-html>
                  <button @click="showFullMaterialTitle(swiperIndex, item, true)">展开</button>
                </view>
                <view v-else>
                  <!-- <rich-text :nodes="questionTitle(swiperIndex, item, 'material_title')"></rich-text> -->
                  <mp-html :content="questionTitle(swiperIndex, item, 'material_title')"></mp-html>
                  <button @click="showFullMaterialTitle(swiperIndex, item, false)">收起</button>
                </view>
              </view>

              <!-- 题目视频 -->
              <view v-if="item.title_video_url != undefined && item.title_video" class="title-video">
                <video id="title-video" class="title-video" :src="item.title_video_url" :controls="true"></video>
              </view>

              <!-- 判断、单选、多选题 -->
              <view v-if="item.kind === 'JUDGE' || item.kind === 'SINGLE' || item.kind === 'MULTI'">
                <view class="test-main">
                  <view class="test-title">
                    <mp-html :content="item.title"></mp-html>
                  </view>
                  <view class="test-cont">
                    <view class="test-cont-item"
                          v-for="(optionItem, optionIndex) in item.options_json" :key="optionIndex"
                          @tap.stop="chooseItem(index, optionIndex, item.kind)"
                          :class="optionItem.classes ? optionItem.classes : ''">
                      <view class="key text-cut">
                        {{ optionItem.key }}
                      </view>
                      <view class="cont">
                        <view class="cont-text">
                          <block v-if="item.options_img" v-for="(optionImg, optionImgIndex) in item.options_img"
                                 :key="optionImgIndex">
                            <image class="image" v-if="optionImg.key == optionItem.key"
                                   @tap.stop="previewImage(optionImg.value)" :src="optionImg.value"
                                   mode="widthFix"></image>
                          </block>

                          <view v-if="!isNaN(optionItem.value)">
                            {{ optionItem.value }}
                          </view>
                          <view v-else>
                            <mp-html :content="optionItem.value"></mp-html>
                          </view>

                          <!-- <view>{{ optionItem.value ? optionItem.value : '' }}</view> -->
                        </view>
                        <view class="cont-icon">
                          <view v-if="optionItem.classes">
                            <text v-if="optionItem.classes.indexOf('true') != -1" class="cuIcon-check"></text>
                            <text
                              v-else-if="optionItem.classes.indexOf('true') == -1 && optionItem.classes.indexOf('active') != -1"
                              class="cuIcon-close"></text>
                          </view>
                        </view>
                      </view>
                    </view>
                  </view>
                </view>

                <view class="test-describe" v-if="showAnswer || item.show_answer">
                  <view class="describe-cont">
                    <view class="describe-item">
                      <view class="answer-row">
                        <view class="text-right-answer">正确答案：</view>
                        <view class="text-right-answer-val">{{ item.answer }}</view>
                      </view>
                    </view>

                    <view class="describe-item" v-if="mode == 'VIEW' && item.user_answer">
                      <view class="answer-row">
                        <view class="text-right-answer">用户答案：</view>
                        <view class="text-right-answer-val">{{ item.user_answer }}</view>
                      </view>
                    </view>

                    <view class="describe-item" v-if="pageType == 'WRONG' && item.source">
                      <view class="answer-row">
                        <view class="text-right-answer">错题来源：</view>
                        <view class="text-right-answer-val">{{ getSourceText(item.source) }}</view>
                      </view>
                    </view>
                  </view>
                </view>

                <view class="test-describe" v-if="showAnswer || item.show_answer">
                  <view class="describe-cont">
                    <view>
                      <view class="text-right-answer text-indent tn-margin-top-sm">答案解析：</view>
                      <view class="explain-content">
                        <mp-html :content="item.explain" class="explain-content-text" v-if="item.explain"></mp-html>
                        <text class="font-size-30" v-else>无</text>
                      </view>
                    </view>
                  </view>
                </view>

              </view>

              <!-- 填空题 -->
              <view v-else-if="item.kind === 'FILL'">
                <view class="test-main">
                  <view class="test-title test-title-fill">
                    <block v-for="(titleText, titleIndex) in item.title_data" :key="titleIndex">
                      <view class="test-title-fill-item">{{ titleText }}</view>
                      <view class="test-title-fill-item">
                        <!-- #ifdef MP-WEIXIN -->
                        <input type="text" placeholder="请输入答案" class="fill-input"
                               v-if="item.title_data && item.title_data.length - 1 != titleIndex"
                               :class="[item.answer && item.answer[titleIndex] && item.answer[titleIndex].class ? item.answer[titleIndex].class : '']"
                               :value="setFillInputValue(titleIndex)"
                               :disabled="mode === 'VIEW'"
                               @change="(e) => changeFillInput(e, titleIndex, swiperIndex)"
                        />
                        <!-- #endif -->

                        <!-- #ifdef H5 -->
                        <input type="text" placeholder="请输入答案" class="fill-input"
                               v-if="item.title_data && item.title_data.length - 1 != titleIndex"
                               :class="[item.answer && item.answer[titleIndex] && item.answer[titleIndex].class ? item.answer[titleIndex].class : '']"
                               :value="setFillInputValue(titleIndex)"
                               :disabled="mode === 'VIEW'"
                               @blur="(e) => changeFillInput(e, titleIndex, swiperIndex)"
                        />
                        <!-- #endif -->
                      </view>
                    </block>
                    <view class="test-title-fill-item">{{ getQuestionTitleScore(index, item) }}</view>
                  </view>
                  <view class="test-cont">
                    <!-- 确认答案按钮 -->
                    <view class="btn-confirm" v-if="mode == 'TRAINING' && item.is_answered === false">
                      <tui-button type="primary" shape="circle" @click="confirmFillAnswer" width="360rpx" height="60rpx"
                                  :size="26" margin="0 auto">确认答案
                      </tui-button>
                    </view>
                  </view>
                </view>

                <view class="test-describe" v-if="showAnswer || item.show_answer">
                  <view class="describe-cont">
                    <view class="describe-item">
                      <view class="answer-row">
                        <view class="text-right-answer">正确答案：</view>
                      </view>
                    </view>

                    <view class="describe-item pt-0">
                      <view class="answer-row">
                        <view class="text-right-answer-val">
                          <view class="text-right-answer-val-item" v-for="(answer, answerIndex) in item.answer" :key="answerIndex">
                            填空位{{ answerIndex + 1 }}：{{ answer.answers.join('，') }}
                          </view>
                        </view>
                      </view>
                    </view>

                    <view class="describe-item" v-if="mode == 'VIEW' && item.user_answer">
                      <view class="answer-row">
                        <view class="text-right-answer">用户答案：</view>
                      </view>
                    </view>

                    <view class="describe-item pt-0">
                      <view class="answer-row">
                        <view class="text-right-answer-val">
                          <view class="text-right-answer-val-item" v-for="(answer, answerIndex) in item.user_answer" :key="answerIndex">
                            填空位{{ answerIndex + 1 }}：{{ answer }}
                          </view>
                        </view>
                      </view>
                    </view>

                    <view class="describe-item" v-if="pageType == 'WRONG' && item.source">
                      <view class="answer-row">
                        <view class="text-right-answer">错题来源：</view>
                        <view class="text-right-answer-val">{{ getSourceText(item.source) }}</view>
                      </view>
                    </view>
                  </view>
                </view>

                <view class="test-describe" v-if="showAnswer || item.show_answer">
                  <view class="describe-cont">
                    <view>
                      <view class="text-right-answer text-indent tn-margin-top-sm">答案解析：</view>
                      <view class="explain-content">
                        <mp-html :content="item.explain" class="explain-content-text" v-if="item.explain"></mp-html>
                        <text class="font-size-30" v-else>无</text>
                      </view>
                    </view>
                  </view>
                </view>
              </view>

              <!-- 简答题 -->
              <view v-else-if="item.kind === 'SHORT'">
                <view class="test-main">
                  <view class="test-title">
                    <mp-html :content="item.title" style="font-size: 36rpx; font-weight: bold;"></mp-html>
                    <!-- <rich-text :nodes="questionTitle(swiperIndex, item)"
                               style="font-size: 36rpx; font-weight: bold;"></rich-text> -->
                  </view>
                  <view class="test-cont">
                    <!-- 答案输入框 -->
                    <textarea placeholder="在此输入答案" class="short-input"
                              :maxlength="-1"
                              :value="setShortInputValue(swiperIndex)"
                              :auto-blur="true"
                              @blur="(e) => changeShortInput(e, swiperIndex)"
                              @input="(e) => changeShortInput(e, swiperIndex)"
                    />

                    <!-- 确认答案按钮 -->
                    <view class="btn-confirm" v-if="mode == 'TRAINING'">
                      <tui-button type="primary"
                                  shape="circle"
                                  @click="confirmShortAnswer"
                                  width="360rpx"
                                  height="60rpx"
                                  :size="26"
                                  margin="0 auto">确认答案
                      </tui-button>
                    </view>
                  </view>
                </view>

                <!-- <view class="test-describe" v-if="showAnswer || item.show_answer">
                  <view class="describe-cont">
                    <view>
                      <view>
                        <view class="text-right-answer">正确答案：</view>
                        <text style="color: #5677fc;margin-left: 30rpx;" v-if="item.answer && item.answer.answer">{{item.answer.answer}}</text>
                      </view>
                      <view v-for="(keyword, keywordIndex) in item.answer.config" :key="keywordIndex">
                        <view :class="[keyword.class]">
                          关键词{{ keywordIndex + 1 }}：{{ keyword.answer }}({{ keyword.score }}分)
                        </view>
                      </view>
                    </view>
                  </view>
                </view> -->

                <view class="test-describe" v-if="showAnswer || item.show_answer">
                  <view class="describe-cont">
                    <view class="describe-item">
                      <view class="answer-row">
                        <view class="text-right-answer">正确答案：</view>
                      </view>
                    </view>

                    <view class="describe-item pt-0">
						<view class="answer-row">
							<view class="text-right-answer-val">
								<text style="color: #5677fc;margin-left: 30rpx;" v-if="item.answer && item.answer.answer">{{item.answer.answer}}</text>
							</view>
						</view>
                      <view class="answer-row">
                        <view class="text-right-answer-val">
                          <view class="text-right-answer-val-item" v-for="(keyword, keywordIndex) in item.answer.config" :key="keywordIndex">
                            <view :class="[keyword.class]">
                              关键词{{ keywordIndex + 1 }}：{{ keyword.answer }}({{ keyword.score }}分)
                            </view>
                          </view>
                        </view>
                      </view>
                    </view>

                    <view class="describe-item" v-if="mode == 'VIEW' && item.user_answer">
                      <view class="answer-row">
                        <view class="text-right-answer">用户答案：</view>
                      </view>
                    </view>

                    <view class="describe-item pt-0">
                      <view class="answer-row">
                        <view class="text-right-answer-val">
                          <view class="text-right-answer-val-item" v-for="(answer, answerIndex) in item.user_answer" :key="answerIndex">
                            {{ answer }}
                          </view>
                        </view>
                      </view>
                    </view>

                    <view class="describe-item" v-if="pageType == 'WRONG' && item.source">
                      <view class="answer-row">
                        <view class="text-right-answer">错题来源：</view>
                        <view class="text-right-answer-val">{{ getSourceText(item.source) }}</view>
                      </view>
                    </view>
                  </view>
                </view>

                <view class="test-describe" v-if="showAnswer || item.show_answer">
                  <view class="describe-cont">
                    <view>
                      <view class="text-right-answer text-indent tn-margin-top-sm">答案解析：</view>
                      <view class="explain-content">
                        <mp-html :content="item.explain" class="explain-content-text" v-if="item.explain"></mp-html>
                        <text class="font-size-30" v-else>无</text>
                      </view>
                    </view>
                  </view>
                </view>

              </view>

              <!-- 解析视频 -->
              <view class="explain-video-view"
                    v-if="(showAnswer || item.show_answer) && item.explain_video_url != undefined && item.explain_video">
                <video id="explain-video" class="explain-video" :src="item.explain_video_url" controls></video>
              </view>

            </block>
            <view style="height: 100rpx;"></view>
          </view>
        </swiper-item>
      </swiper>
    </view>

    <!-- 底部栏 -->
    <!-- :class="mode == 'EXAM' || mode == 'VIEW' ? (canDeleteWrong ? ['col-5'] : ['col-4']) : ['col-3']" -->
    <view class="fix-bottom grid text-center bg-white cu-list" :class="[getBottomBarClass()]">
      <view url="/pages/index/index" class="cu-item" @click="handleNumberPanel"><!-- v-if="mode != 'TRAINING'" -->
        <image src="/static/img/caidan.png"></image>
        <text>{{ swiperIndex }}/{{ total }}</text>
      </view>

      <view url="/pages/index/index" class="cu-item" @tap="prev">
        <image src="/static/img/left.png"></image>
        <text>上一题</text>
      </view>

      <view url="/pages/index/index" class="cu-item" @tap="next">
        <image src="/static/img/right.png"></image>
        <text>下一题</text>
      </view>

      <view url="/pages/index/index" class="cu-item" @tap="buttonClicked==true?submitShowModal():''"
            v-if="mode == 'EXAM'">
        <image src="/static/img/jiaojuan.png"></image>
        <text>交卷</text>
      </view>

      <view url="/pages/index/index" class="cu-item" @tap="buttonClicked==true?endTrainShowModal():''"
            v-if="mode == 'TRAINING'">
        <image src="/static/img/jiaojuan.png"></image>
        <text>结束练习</text>
      </view>

      <view url="/pages/index/index" class="cu-item" @tap="wrongDel()" v-if="mode == 'VIEW' && canDeleteWrong">
        <image src="/static/img/delete.png"></image>
        <text>删除</text>
      </view>

      <!-- <view url="/pages/index/index" class="cu-item" @tap="wrongClear()" v-if="mode == 'VIEW' && canDeleteWrong">
        <image src="/static/img/round_close.png"></image>
        <text>清空</text>
      </view> -->
    </view>

    <!-- 题标 -->
    <!-- <view class="fixed-bottom" :class="{ active: showNumberPanel }" @click.stop="handleNumberPanel">
      <view class="tibiao" @click.stop>
        <scroll-view scroll-y="true" class="tibiao-scroll">
          <view class="tibiao-item" v-for="(item, index) in total" :key="index" :class="swiperIndex - 1 == index ? 'selected' : ''"
            @click="changeQuestion(index)">
            {{ index + 1 }}
          </view>
        </scroll-view>
      </view>
    </view> -->

    <!-- 题标 -->
    <view class="cu-modal bottom-modal fixed-bottom" :class="showNumberPanel?'show':''" @click.stop="handleNumberPanel">
      <view class="cu-dialog tibiao" @click.stop>
        <scroll-view scroll-y="true" class="tibiao-scroll">
          <view class="tibiao-scroll-list">
            <!-- class="tibiao-item" -->
            <!-- :class="[getNumberPanelClass(index)]" -->
            <!-- :class="swiperIndex - 1 == index ? 'selected' : (list[index] && (list[index].check || list[index].user_answers) ? 'right' : '')" -->
            <view
              :class="['tibiao-item', getNumberPanelClass(index)]"
              v-for="(item, index) in total" :key="index" @click="changeQuestion(index)">
              {{ index + 1 }}
            </view>
          </view>
        </scroll-view>
      </view>
    </view>


    <!-- 删除错题对话框 -->
    <view class="cu-modal" :class="showDeleteDialog?'show':''">
      <view class="cu-dialog">
        <view class="cu-bar bg-white justify-end">
          <view class="content">提示</view>
          <view class="action" @tap="hideModal">
            <text class="cuIcon-close text-red"></text>
          </view>
        </view>
        <view class="padding-xl">
          {{ wrongDeleteType == 'single' ? '确定删除该错题吗？' : '确定清空所有错题吗？' }}
        </view>
        <view class="cu-bar bg-white justify-end">
          <view class="action">
            <button class="cu-btn line-primary text-primary" @tap="hideModal">取消</button>
            <button class="cu-btn bg-primary margin-left" @tap="confirmDelWrong">确定</button>
          </view>
        </view>
      </view>
    </view>

    <!-- 结束练习对话框 -->
    <tui-modal :show="showEndTrainDialog" @cancel="hideModal" :custom="true">
      <view class="tui-modal-custom">
        <view class="tui-prompt-title">练习结果</view>
        <view class="tui-modal-custom-text">
          <view class="">
            正确题数：
            <text class="text-green">{{ trainResult.right }}</text>
            题
          </view>
          <view class="">
            错误题数：
            <text class="text-red">{{ trainResult.error }}</text>
            题
          </view>
          <view class="">
            未答题数：
            <text class="text-yellow">{{ trainResult.unchecked }}</text>
            题
          </view>
        </view>

        <view class="tui-flex-box">
          <view class="tui-flex-botton-view">
            <tui-button margin="0 20rpx 0 0" height="72rpx" :size="28" shape="circle" @click="endTrain">确定退出
            </tui-button>
          </view>
          <view class="tui-flex-botton-view">
            <tui-button margin="0 20rpx 0 0" height="72rpx" :size="28" shape="circle" @click="hideModal" plain>取消
            </tui-button>
          </view>
        </view>
      </view>
    </tui-modal>

    <!-- 悬浮按钮 -->
    <tn-fab
      :btnList="fabBtnList"
      left="auto"
      :right="40"
      :bottom="180"
      :width="88"
      :height="88"
      :iconSize="45"
      :zIndex="998"
      backgroundColor="fab-bg-color"
      fontColor="#aaa"
      icon="up"
      animationType="up"
      :showMask="false"
      :customBtn="true"
      @click="clickFabItem"
    >
    </tn-fab>

    <!-- 纠错反馈弹窗 -->
    <tn-popup
      v-model="showCorrection"
      length="50%"
      mode="bottom"
      backgroundColor="#fff"
      :zIndex="999"
      :borderRadius="23"
      :closeBtn="true"
      :maskCloseable="false"
    >
      <view class="popup-content" :class="{'popup-content--center': mode === 'center'}">
        <view class="tn-border-solid-bottom margin-top padding-bottom text-center text-bold" style="font-size: 30rpx;">
          纠错反馈
        </view>

        <view class="padding">
          <tn-checkbox-group @change="correctionTypeChange" activeColor="#5677fc" :size="28">
            <tn-checkbox :labelSize="28" v-model="item.checked" v-for="(item, index) in correctionTypeslist"
                         :key="index" :name="item.name">{{ item.name }}
            </tn-checkbox>
          </tn-checkbox-group>
        </view>

        <view style="padding: 0rpx 20rpx;">
					<textarea v-model="correctionRemark"
                    :maxlength="500"
                    placeholder="其他错误,请描述您遇到的问题"
                    style="background-color: #f3f3f3; padding: 20rpx; width: 100%; height: 150rpx; font-size: 28rpx;">
					</textarea>
        </view>

        <view class="btn-confirm">
          <tui-button type="primary" shape="circle" @click="submitCorrection" width="600rpx" height="70rpx" :size="26"
                      margin="0 auto">提交
          </tui-button>
        </view>
      </view>
    </tn-popup>
  </view>
</template>

<script>
import correctionApi from "@/common/api/correction.js"
import questionApi from "@/common/api/question.js"

export default {
  name: "kz-question",
  props: {
    /**
     * 模式
     * EXAM：考试模式
     * VIEW：背题模式
     * TRAINING：练习模式
     */
    mode: {
      type: String,
      default: 'EXAM'
    },
    /**
     * 页面来源
     * PAPER：试卷考试
     * WRONG：错题
     * COLLECT：收藏
     */
    pageType: {
      type: String,
      default: 'PAPER'
    },
    // 试卷标题
    title: {
      type: String,
      default: '标题'
    },
    // 试题集
    questions: {
      type: Array,
      default: () => []
    },
    // 试题集总数（不传默认获取试题集长度）
    questionCount: {
      type: Number,
      default: 0
    },
    // 试题集每页条数
    pageCount: {
      type: Number,
      default: 20
    },
    // 试题集当前页
    currentPage: {
      type: Number,
      default: 1
    },
    // 考试时间（倒计时）
    limit_time: {
      type: Number,
      default: 0
    },
    // 考试配置
    configs: {
      type: Object,
      default: () => {
      }
    },
    // 允许收藏试题
    canCollect: {
      type: Boolean,
      default: true
    },
    // 允许删除错题
    canDeleteWrong: {
      type: Boolean,
      default: false
    },
    // 看题/练题的模式：normal=普通模式，memory=记忆模式，random=随机查询
    viewMode: {
      type: String,
      default: 'normal'
    },
    /**
     * 试卷选题模式
     * RANDOM：随机模式
     * FIX：固定模式
     */
    paperMode: {
      type: String,
      default: 'RANDOM'
    },
    // 防切屏
    isPreventSwitchScreen: {
      type: Boolean,
      default: false
    },
    // 切屏限制次数
    switchScreenCount: {
      type: Number,
      default: 0
    }
  },
  data () {
    return {
      // swiper当前下标
      swiperIndex: 1,
      // 总题数
      total: 0,
      // 试题集合
      list: [],
      // 题目面板
      showNumberPanel: false,
      // 防止交卷按钮点击多次
      buttonClicked: true,
      // 显示倒计时
      showCountDown: false,
      // 显示试题答案
      showAnswer: false,
      // 显示试题分数
      showQuestionScore: false,
      // 显示正确选择
      showRightChoose: false,
      // 显示错误选择
      showErrorChoose: false,
      // 显示结束练习对话框
      showEndTrainDialog: false,
      // 显示删除错题对话框
      showDeleteDialog: false,
      // 错题删除类型
      wrongDeleteType: 'single',
      // 答题开始时间
      startTime: 0,
      // 即将删除的错题ID
      wrongDelId: 0,
      // 练习结果
      trainResult: {
        right: 0,
        error: 0,
        unchecked: 0,
        rightIds: [],
        errorIds: []
      },
      // 记忆模式相关数据
      memoryData: {},
      // 加载试题次数（以此来判断是否是第一次进入）
      loadQuestionCount: 0,
      // 延迟加载已加载页码
      loadQuestionPage: [1],
      // 悬浮按钮列表
      fabBtnList: [
        {
          text: '纠错反馈',
          bgColor: '#fff',
          textColor: '#aaa',
          iconColor: '#aaa',
          icon: 'edit-form',
          iconSize: 45
        },
        {
          text: '清空错题',
          bgColor: '#fff',
          textColor: '#aaa',
          iconColor: '#aaa',
          icon: 'clear',
          iconSize: 45
        }
      ],
      // 纠错反馈弹窗
      showCorrection: false,
      correctionTypeslist: [],
      checkCorrectionTypeslist: [],
      correctionRemark: ''
    };
  },
  watch: {
    // 加载试题时
    questions: function () {
      let questionCount = this.questionCount ? this.questionCount : this.questions.length
      this.total = questionCount < this.questions.length ? this.questions.length : questionCount
      this.list = [...this.questions]

      // 材料题处理
      this.showFullMaterialTitle(0)
      // 填空题处理
      this.splitFillTitle(0)
      // 初始化模式配置
      this.initMode()
      // 记忆模式 - 跳转上次题标
      this.jumpMemoryQuestion()

    }
  },
  computed: {
    // 试题类型
    kindText () {
      return function () {
        return this.list[this.swiperIndex - 1] ? this.list[this.swiperIndex - 1].kind_text : '未知'
      }
    },
    // 试题标题
    questionTitle () {
      return function (index, item, field = 'title') {
        let html = item[field]
        // return this.truncatedText(html)
        html = this.utils.formatRichText(html)
        html += this.getQuestionTitleScore(index, item)

        if (field == 'material_title') {
          // console.log('material_title show_full', item.show_full)
          if (this.list[index] && !this.list[index].show_full) {
            // if (!item.show_full) {
            // if (!show_full) {
            html = this.truncatedText(html)
            // console.log('material_title', html)
          }
        }

        return html
      }
    },
    // 试题解析
    questionExplain () {
      return function (index, item) {
        return this.utils.formatRichText(item.explain)
      }
    },
    // 获取试题分数
    getQuestionTitleScore () {
      return function (index, item) {
        if (this.showQuestionScore) {
          return '(' + item.score + '分)'
          // return '(' + this.getSingleScore(item.kind, item.difficulty, item) + '分)'
        }

        return ''
      }
    },
    // 截取文本
    truncatedText () {
      return function (text, maxLength = 100) {
        console.log('lllllllllll', text)
        if (text.length > maxLength) {
          return text.slice(0, maxLength) + '...';
        }
        return text;
      }
    },
    // 设置填空题答案
    setFillInputValue () {
      return (titleIndex) => {
        let index = this.swiperIndex - 1
        if (this.mode == 'VIEW') {
          if (this.list[index].answer) {
            if (this.list[index].answer[titleIndex].answers != undefined) {
              return this.list[index].answer[titleIndex].answers[0]
            }
          }
        } else {
          if (this.list[index].user_answers) {
            return this.list[index].user_answers[titleIndex]
          }
        }

        return ''
      }
    },
    // 设置简答题答案
    setShortInputValue () {
      return () => {
        let index = this.swiperIndex - 1
        if (this.mode == 'VIEW') {
          if (this.list[index].answer) {
            if (this.list[index].answer.answer != undefined) {
              return this.list[index].answer.answer
            }
          }
        } else {
          if (this.list[index].user_answers) {
            return this.list[index].user_answers
          }
        }

        return ''
      }
    },
    // 设置底部栏样式
    getBottomBarClass () {
      return () => {
        // console.log('getBottomBarClass', this.mode)
        let classes = []
        if (this.mode == 'EXAM') {
          if (this.canDeleteWrong) {
            classes = ['col-5']
          } else {
            classes = ['col-4']
          }
        } else if (this.mode == 'TRAINING') {
          if (this.canDeleteWrong) {
            classes = ['col-5']
          } else {
            classes = ['col-4']
          }
        } else if (this.mode == 'VIEW') {
          if (this.canDeleteWrong) {
            classes = ['col-4']
          } else {
            classes = ['col-3']
          }
        } else {
          classes = ['col-3']
        }

        return classes
      }
    },
    // 设置答题卡样式
    getNumberPanelClass () {
      return (index) => {
        if (this.swiperIndex - 1 == index) {
          return ['selected']
        }

        let classes = []
        let question = this.list[index]
        // console.log('getNumberPanelClass question', question)
        if (question && question.kind) {
          switch (question.kind) {
            case 'JUDGE':
            case 'SINGLE':
            case 'MULTI':
              if (question.is_right) {
                if (question.is_right == 'right') {
                  return ['tibiao-right']
                } else {
                  return ['tibiao-error']
                }
              }
              if (question.check) {
                return ['tibiao-right']
              }
              break

            case 'FILL':
            case 'SHORT':
              if (question.user_answers) {
                if (question.is_right) {
                  if (question.is_right == 'right') {
                    return ['tibiao-right']
                  } else {
                    return ['tibiao-error']
                  }
                }
                return ['tibiao-right']
              }
              break

            default:
              return classes
          }
        }

        return classes
      }
    },
    // 获取错题来源文本
    getSourceText () {
      return (source) => {
        if (source == 'PAPER') {
          return '试卷考试'
        } else if (source == 'ROOM') {
          return '考场考试'
        } else if (source == 'TRAINING') {
          return '练题模式'
        }
      }
    }
  },
  methods: {
    // 初始化模式配置
    initMode (reset = false) {
      switch (this.mode) {
        // 考试模式
        case 'EXAM':
          this.showCountDown = true
          this.showAnswer = false
          this.showQuestionScore = true
          this.showRightChoose = false
          this.showErrorChoose = false
          this.startTime = this.utils.timestamp()
          break

        // 练习模式
        case 'TRAINING':
          this.showCountDown = false
          this.showAnswer = false
          this.showQuestionScore = false
          this.showRightChoose = false
          this.showErrorChoose = true
          break

        // 背题模式
        case 'VIEW':
          this.showCountDown = false
          this.showAnswer = true
          this.showQuestionScore = false
          this.showRightChoose = true
          this.showErrorChoose = false

          // 显示试题正确的选项
          this.list.forEach((item, index) => {
            if (item.options_json) {
              // console.log('item.options_json', item.options_json)
              Array.from(item.options_json).forEach((optionItem, optionIndex) => {
                item.options_json[optionIndex].click_index = true
                item.options_json[optionIndex].classes = item.answer.indexOf(optionItem.key) > -1 ? 'active_true' : ''
              })

              this.list[index] = item
            }
          })
          break
      }
    },
    // 计算试题积分
    getSingleScore (kind, difficulty, question) {
      if (this.paperde == 'FIX') {
        return question.score
      }

      const configs = this.configs[kind.toLowerCase()];
      if (configs && configs['use_difficulty']) {
        return configs['difficulty'][difficulty.toLowerCase()]['score'];
      }
      return configs['score'];
    },
    // 延迟加载试题
    loadQuestion () {
      if (this.mode != 'TRAINING' && this.mode != 'VIEW') {
        return
      }
      if (this.viewMode != 'normal') {
        return
      }

      // 取下一页数据
      let page = Math.round(this.swiperIndex / this.pageCount) + 1
      if (!this.loadQuestionPage.includes(page)) {
        this.loadQuestionPage.push(page)
        this.$emit('loadQuestion', page)
      }
    },
    // 滑动切题
    swiperChange (e, type = '') {
      // console.log('swiperChange', this.list.length, this.swiperIndex, e.detail.current)
      // 当前题标
      this.swiperIndex = e.detail.current + 1
      // 材料题处理
      this.showFullMaterialTitle(e.detail.current)
      // 填空题处理
      this.splitFillTitle(e.detail.current)
      // 加载题目
      this.loadQuestion()
      // 记忆模式
      this.memoryQuestion()
    },
    // 上一题
    prev () {
      if (this.swiperIndex > 1) {
        this.swiperIndex--
      }
    },
    // 下一题
    next () {
      // console.log('next', this.list.length, this.swiperIndex)
      if (this.list.length - this.swiperIndex >= 1) {
        // 填空题处理
        this.splitFillTitle(this.swiperIndex)
        this.swiperIndex++
      } else {
        let title = '没有更多题了~'
        if (this.mode == 'EXAM') {
          title = '可以交卷了~'
        }

        uni.showToast({
          title: title,
          icon: 'none',
          duration: 1500
        })
      }
    },
    // 选择
    chooseItem (questionIndex, optionIndex, kind) {
      // 背题模式不让选
      if (this.mode == 'VIEW') {
        return
      }

      let questionItem = this.list[questionIndex]

      switch (kind) {
        // 多选题
        case 'MULTI':
          questionItem.options_json[optionIndex].click_index = !questionItem.options_json[optionIndex].click_index;
          //多选 确认按钮
          let arr = []
          questionItem.options_json.forEach((item, index) => {
            if (item.click_index == true) {
              arr.push(item.key)
            }
          })
          questionItem.check = arr.join(',')
          questionItem.selected = true
          break

        // 单选/判断
        default:
          questionItem.options_json.forEach((item, index) => {
            questionItem.options_json[index].click_index = false
            questionItem.options_json[index].classes = ''
          })
          questionItem.options_json[optionIndex].click_index = true
          questionItem.check = questionItem.options_json[optionIndex].key
          questionItem.selected = true
          break
      }

      questionItem.options_json[optionIndex].classes = this.optionChooseClass(questionIndex, optionIndex, questionItem, questionItem.options_json[optionIndex])
      this.list[questionIndex] = questionItem

      // #ifdef H5
      this.$forceUpdate()
      // #endif

      // #ifdef APP
      this.$forceUpdate()
      // #endif
    },
    // 选项选择样式
    optionChooseClass (questionIndex, optionIndex, questionItem, optionItem) {
      if (this.showRightChoose) {
        let classNames = ''

        if ((questionItem.selected && optionItem.code) || (questionItem.kind == 'MULTI' && optionItem.click_index)) {
          classNames = 'active_true'
        }
        if ((questionItem.kind != 'MULTI' && optionItem.click_index && !optionItem.code) || (questionItem.kind == 'MULTI' && optionItem.click_index && !optionItem.code && questionItem.selected)) {
          classNames += ' active'
        }

        return classNames
      } else {
        // 练习模式，选择后显示错误选项
        if (this.showErrorChoose) {
          let result = ''
          switch (questionItem.kind) {
            // 多选题
            case 'MULTI':
              let isRight = true
              let answer_arr = questionItem.answer.split(',')
              // console.log('questionItem.answer', questionItem.answer, questionItem.check, answer_arr)

              let check_arr = questionItem.check.indexOf(',') > 0 ? questionItem.check.split(',') : [questionItem.check]
              for (const check_answer of check_arr) {
                if (!answer_arr.includes(check_answer)) {
                  isRight = false
                  break
                }
              }

              // 选择数量跟答案数量一致
              if (questionItem.check && (!isRight || questionItem.check.length == questionItem.answer.length)) {
                // 选择后显示答案和解析
                questionItem.show_answer = true
                this.list[questionIndex] = questionItem
              }

              result = questionItem.answer.indexOf(optionItem.key) > -1 ? 'active_true' : 'active'
              break

            // 单选/判断
            default:
              // 选择后显示答案和解析
              questionItem.show_answer = true
              this.list[questionIndex] = questionItem
              // 选择正确与否
              result = optionItem.key == questionItem.answer ? 'active_true' : 'active'
              break
          }

          // 记录练习结果
          if (result === 'active_true') {
            this.list[questionIndex]['is_right'] = 'right'
          } else {
            this.list[questionIndex]['is_right'] = 'error'

            if (this.mode == 'TRAINING') {
              // 练题模式 - 记录错题
              questionApi.addQuestionWrong(this, {
                question_id: questionItem.id,
                source: 'TRAINING',
                user_answer: questionItem.check
              })
            }
          }

          return result
        } else {
          if (optionItem.click_index) {
            return 'active_true'
          }
        }
      }

      return ''
    },
    // 获取未答题数量
    getUncheckedCount () {
      if (this.mode === 'EXAM') {
        let unchecked = []
        for (let i in this.list) {
          let question = this.list[i]
          let item = {
            id: question.id,
            answer: ''
          }

          switch (question.kind) {
            case 'JUDGE':
            case 'SINGLE':
            case 'MULTI':
              if (question.check) {
                item.answer = question.check
              } else {
                unchecked.push({
                  id: question.id
                })
              }
              break

            case 'FILL':
              if (question.user_answers) {
                item.answer = question.user_answers
              } else {
                unchecked.push({
                  id: question.id
                })
              }
              break

            case 'SHORT':
              if (question.user_answers) {
                item.answer = question.user_answers
              } else {
                unchecked.push({
                  id: question.id
                })
              }
              break
          }
        }

        return unchecked.length
      } else {
        let right = 0
        let error = 0
        let unchecked = 0
        for (let i in this.list) {
          if (this.list[i].is_right) {
            if (this.list[i].is_right == 'right') {
              right++
            } else {
              error++
            }
          } else {
            unchecked++
          }
        }
        //       this.trainResult.right = (Array.from(new Set(this.trainResult.rightIds))).length
        // this.trainResult.error = (Array.from(new Set(this.trainResult.errorIds))).length
        this.trainResult.right = right
        this.trainResult.error = error
        let count = this.total - this.trainResult.right - this.trainResult.error
        return count > 0 ? count : 0;
      }
    },
    // 左下角交卷按钮点击弹窗
    submitShowModal () {
      this.buttonClicked = false

      let unchecked_count = this.getUncheckedCount()
      let modal_title = '确认要交卷吗？'
      if (unchecked_count > 0) {
        modal_title = `还有${unchecked_count}道题未答，` + modal_title
      }

      uni.showModal({
        title: '提示',
        content: modal_title,
        success: res => {
          if (res.confirm) {
            this.submit()
          } else {
            this.buttonClicked = true
          }
        }
      })
    },
    // 倒计时结束
    endOfTime () {
      // 非考试模式不处理
      if (this.mode != 'EXAM') {
        return
      }
      // 为0时不处理
      if (this.limit_time == 0) {
        return
      }

      uni.showToast({
        title: '考试时间到，即将自动交卷',
        duration: 2500
      })
      this.buttonClicked = true

      setTimeout(() => {
        this.submit()
      }, 2500)
    },
    // 交卷
    submit () {
      let data = {}
      let questions = {}

      let list = this.list
      for (let i in list) {
        let item = {}

        switch (list[i].kind) {
          case 'JUDGE':
          case 'SINGLE':
          case 'MULTI':
            item.id = list[i].id
            item.answer = list[i].check ? list[i].check : ''
            break

          case 'FILL':
            item.id = list[i].id
            item.answer = list[i].user_answers ? list[i].user_answers : []
            break

          case 'SHORT':
            item.id = list[i].id
            item.answer = list[i].user_answers ? list[i].user_answers : ''
            break

          default:
            break
        }

        // 材料题主题ID
        item.material_id = list[i].material_id ? list[i].material_id : 0
        questions[i] = item
      }

      data.start_time = this.startTime
      data.questions = questions
      this.$emit('submitQuestion', data)
    },
    // 控制题目面板显示隐藏
    handleNumberPanel () {
      // if (this.mode == 'TRAINING') {
      // 	return
      // }
      this.showNumberPanel = !this.showNumberPanel;
    },
    // 题目面板跳题
    async changeQuestion (e, type = '') {
      // 题已加载，直接跳
      if (e + 1 <= this.list.length) {
        // this.swiperIndex = e + 1
        this.showNumberPanel = !this.showNumberPanel
        this.swiperChange({ detail: { current: e } })
      } else {
        // 题未加载，一页页加载
        let currLastPage = this.loadQuestionPage.at(-1)
        let willGetPage = Math.round(e / this.pageCount) + 1


        for (var page = currLastPage; page <= willGetPage; page++) {

          if (this.loadQuestionPage.includes(page)) {
            continue;
          }

          await new Promise((resolve, reject) => {
            this.$emit('loadQuestion', page, () => {
              this.loadQuestionPage.push(page)

              if (page >= willGetPage) {
                setTimeout(() => {
                  this.changeQuestion(e, 'digui')
                }, 1000)
              }

              resolve()
            })
          })

        }
      }
    },
    // 删除错题
    wrongDel (showDialog = true) {
      // 弹窗提示
      if (showDialog) {
        this.showDeleteDialog = true
        this.wrongDeleteType = 'single'
        return
      }

      this.hideModal()

      // 执行删除
      this.http('question/wrongDelete', {
        question_id: this.list[this.swiperIndex - 1].id
      }, 'get').then(res => {
        uni.showToast({
          icon: 'none',
          title: res.msg
        })

        if (res.code == 1) {
          setTimeout(() => {
            this.$emit('refresh')
          }, 1200)
        }
      });
    },
    // 清空错题
    wrongClear (showDialog = true) {
      // 弹窗提示
      if (showDialog) {
        this.showDeleteDialog = true
        this.wrongDeleteType = 'all'
        return
      }

      this.hideModal()

      // 执行清空
      this.http('question/wrongClear', {}).then(res => {
        uni.showToast({
          icon: 'none',
          title: res.msg
        })

        if (res.code == 1) {
          setTimeout(() => {
            this.$emit('refresh')
          }, 1200)
        }
      });
    },
    // 确认删除错题
    confirmDelWrong () {
      if (this.wrongDeleteType == 'single') {
        this.wrongDel(false)
      } else if (this.wrongDeleteType == 'all') {
        this.wrongClear(false)
      }

    },
    // 隐藏弹窗
    hideModal (e) {
      this.showDeleteDialog = false
      this.showEndTrainDialog = false
      this.buttonClicked = true
    },
    // 添加收藏
    collectAdd () {
      let id = this.list[this.swiperIndex - 1].id
      let index = this.swiperIndex - 1

      this.http('question/collectAdd', {
        question_id: id
      }, 'get').then(res => {
        uni.showToast({
          icon: 'none',
          title: res.msg
        })

        if (res.code == 1) {
          this.list[index]['collected'] = true
          this.$forceUpdate()
        }
      });
    },
    // 取消收藏
    collectDel () {
      let id = this.list[this.swiperIndex - 1].id
      let index = this.swiperIndex - 1

      this.http('question/collectCancel', {
        question_id: id
      }, 'get').then(res => {
        uni.showToast({
          icon: 'none',
          title: res.msg
        })

        if (res.code == 1) {
          this.list[index]['collected'] = false
          this.$forceUpdate()
        }
      });
    },
    // 结束练习弹窗
    endTrainShowModal () {
      this.buttonClicked = false
      this.trainResult.unchecked = this.getUncheckedCount()
      this.showEndTrainDialog = true
    },
    // 结束练习
    endTrain () {
      this.utils.goback()
    },
    // 记忆模式 - 缓存key
    getMemoryCacheKey (cate_id) {
      return this.mode.toLowerCase() + '-' + cate_id
    },
    // 记忆当前题目信息
    memoryQuestion () {
      if (this.viewMode != 'memory') {
        return
      }

      let question = this.list[0]
      let data = {
        memory_cate_id: question.cate_id,
        memory_question_id: question.id,
        memory_index: this.swiperIndex
      }
      this.memoryData = data

      // 缓存当前题目信息
      let cache_key = this.getMemoryCacheKey(question.cate_id) //this.mode.toLowerCase() + '-' + question.cate_id
      this.utils.setData(cache_key, data)
    },
    // 记忆模式 - 跳转上次题标
    jumpMemoryQuestion () {
      // 记忆模式且是第一次加载试题
      if (this.viewMode == 'memory' && this.loadQuestionCount == 0) {
        let cache_key = this.getMemoryCacheKey(this.questions[0].cate_id)
        this.memoryData = this.utils.getData(cache_key)
        if (this.memoryData) {
          // 跳转到上次题标位置
          this.swiperIndex = this.memoryData.memory_index

          // 填空题处理
          this.splitFillTitle(this.swiperIndex - 1)
        }
      }
    },
    // 图片预览
    previewImage (image) {
      uni.previewImage({
        current: 0,
        urls: [image]
      })
    },
    // 填空题 - 处理题目数据
    splitFillTitle (index) {
      if (this.list[index] && this.list[index].kind == 'FILL') {
        if (!this.list[index].title_data) {
          this.list[index]['title_data'] = this.list[index].title.split('______')
        }

        // 未回答标识
        if (!this.list[index].is_answered) {
          this.list[index]['is_answered'] = false
        }
      }
    },
    // 填空题 - 文本框修改
    changeFillInput (e, titleIndex, swiperIndex) {
      if (this.mode == 'VIEW') {
        return
      }

      if (!this.list[swiperIndex - 1]['user_answers']) {
        this.list[swiperIndex - 1]['user_answers'] = []
      }

      this.list[swiperIndex - 1].user_answers[titleIndex] = e.target.value
    },
    // 填空题 - 练习模式 - 确认答案
    confirmFillAnswer () {
      let index = this.swiperIndex - 1
      if (!this.list[index].user_answers || this.list[index].user_answers.length != this.list[index].answer.length) {
        this.utils.toast('请在文本框填写完整的答案')
        return
      }

      let right_count = 0
      for (var i = 0; i < this.list[index].user_answers.length; i++) {
        let user_answer = this.list[index].user_answers[i]
        if (user_answer === '') {
          this.utils.toast('第' + (i + 1) + '个文本框未填写答案')
          return
        }

        let is_right = false
        for (var j = 0; j < this.list[index].answer[i].answers.length; j++) {
          let right_answer = this.list[index].answer[i].answers[j]
          if (user_answer === right_answer) {
            is_right = true
            break
          }
        }

        // 填空题对错 - 文本框样式
        this.list[index].answer[i]['class'] = is_right ? 'fill-input-right' : 'fill-input-error'
        if (is_right) {
          right_count++
        }
      }

      // 练习模式，选择后显示错误选项
      if (this.showErrorChoose) {
        // 记录练题情况
        if (right_count == this.list[index].user_answers.length) {
          // this.trainResult.right++
          this.list[index]['is_right'] = 'right'
        } else {
          // this.trainResult.error++
          this.list[index]['is_right'] = 'error'
        }
      }

      if (this.mode == 'TRAINING') {
        console.log('right_count', right_count, this.list[index].answer.length, this.list[index].user_answers)
        // 练题模式 - 记录错题
        if (right_count != this.list[index].answer.length) {
          questionApi.addQuestionWrong(this, {
            question_id: this.list[index].id,
            source: 'TRAINING',
            user_answer: this.list[index].user_answers
          })
        }
      }

      // 显示答案
      this.list[index]['show_answer'] = true
      // 标记为已回答
      this.list[index]['is_answered'] = true

      this.$forceUpdate()
    },
    // 简答题 - 文本框修改
    changeShortInput (e, swiperIndex) {
      if (this.mode == 'VIEW') {
        return
      }

      if (!this.list[swiperIndex - 1]['user_answers']) {
        this.list[swiperIndex - 1]['user_answers'] = ''
      }

      this.list[swiperIndex - 1].user_answers = e.target.value
    },
    // 简答题 - 练习模式 - 确认答案
    confirmShortAnswer () {
      let index = this.swiperIndex - 1
      if (!this.list[index].user_answers) {
        this.utils.toast('请在文本框填写完整的答案')
        return
      }

      let right_count = 0
      // let right_indexes = []
      for (var i = 0; i < this.list[index].answer.config.length; i++) {
        this.list[index].answer.config[i]['class'] = ''
        if (this.list[index].user_answers.indexOf(this.list[index].answer.config[i].answer) > -1) {
          right_count++
          this.list[index].answer.config[i]['class'] = 'short-input-right'
          // right_indexes.push(i)
        }
      }

      if (this.mode == 'TRAINING') {
        // 练题模式 - 记录错题
        if (right_count != this.list[index].answer.length) {
          questionApi.addQuestionWrong(this, {
            question_id: this.list[index].id,
            source: 'TRAINING',
            user_answer: this.list[index].user_answers
          })
        }
      }

      // 练习模式，选择后显示错误选项
      if (this.showErrorChoose) {
        // 记录练题情况
        if (right_count > 0) {
          // this.trainResult.right++
          this.list[index]['is_right'] = 'right'
        } else {
          // this.trainResult.error++
          this.list[index]['is_right'] = 'error'
        }
      }


      // 显示答案
      this.list[index]['show_answer'] = true
      // 标记为已回答
      this.list[index]['is_answered'] = true

      this.$forceUpdate()
    },
    // 材料题 - 处理材料题目数据
    showFullMaterialTitle (index, item, status = false) {
      if (item && item.material_title != undefined) {// item.kind == 'MATERIAL'
        // 未回答标识
        if (!item.show_full) {
          item['show_full'] = false
          if (this.list[index]) {
            this.list[index]['show_full'] = false
          }
        }

        item.show_full = status
        if (this.list[index]) {
          this.list[index].show_full = status
        }
        // console.log('showFullMaterialTitle', item.show_full)
        this.$forceUpdate()
      }
    },
    // 点击悬浮按钮的内容
    clickFabItem (e) {
      // console.log('clickFabItem', e)
      // 错题反馈
      if (e.index === 0) {
        correctionApi.getCorrectionTypes(this, {}).then(res => {
          this.correctionTypeslist = res.data.types
          this.showCorrection = true
        })
      } else if (e.index === 1 && this.canDeleteWrong) {
        // 清空错题
        this.wrongClear()
      }
    },
    // 纠错反馈类型选择
    correctionTypeChange (e) {
      // console.log('correctionTypeChange', e)
      this.checkCorrectionTypeslist = e
    },
    // 提交纠错反馈
    submitCorrection () {
      if (this.checkCorrectionTypeslist.length == 0) {
        this.utils.toast('请选择纠错类型')
        return
      }

      let data = {
        question_id: this.list[this.swiperIndex - 1].id,
        type_names: this.checkCorrectionTypeslist,
        remark: this.correctionRemark
      }
      correctionApi.submitCorrection(this, data).then(res => {
        if (res.code) {
          this.checkCorrectionTypeslist = []
          this.correctionRemark = ''
          this.utils.toast(res.msg)
        }
        this.showCorrection = false
      })
    }
  }
}
</script>

<style lang="less">
page {
  height: 100%;
}

.questions {
  height: 100%;
  position: relative;

  .test-header {
    width: 100%;
    padding: 0 30rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 80rpx;
    background: #fff;
    position: relative;
    font-size: 34rpx;
  }

  .card-shadow {
    margin-top: 20rpx;

    .topic-title {
      font-size: 34rpx;
      padding: 30rpx 20rpx;
      background: #fff;
      border-bottom: 1px solid #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: space-between;

      .topic-title_left {
        display: flex;
        align-items: center;

        .text-kind {
          font-size: 24rpx;
          color: #fff;
          background: linear-gradient(135deg, #7892fd, #5677fc);
          padding: 8rpx 10rpx;
          border-radius: 15rpx 15rpx 15rpx 0;
          margin-right: 20rpx;
        }
      }

      .title-index {
        color: #5677fc;
      }
    }

    .questions-cont {
      height: 100vh;
      display: flex;
      flex-wrap: nowrap;
      transition: all 0.5s;

      .swiper-item {
        width: 100vw;
        min-width: 100vw;
        max-width: 100vw;
        height: 100%;
        overflow: auto;

        .test-main {
          padding: 0 20rpx;
          // margin: 40rpx 30rpx;
          // margin-bottom: 40rpx;
          // border-radius: 8px;
          background: #fff;

          .test-title {
            color: #333;
            padding: 20rpx 0;

            .text-kind {
              font-size: 24rpx;
              color: #fff;
              background: linear-gradient(135deg, #7892fd, #5677fc);
              padding: 8rpx 10rpx;
              border-radius: 15rpx 15rpx 15rpx 0;
            }

            .test-favor {
              position: relative;
              color: #aaa;
              float: right;
            }

            .test-favor-fill {
              background: #fff;
              color: #fbbd08;
              float: right;
            }
          }

          .test-title-fill {
            width: 100%;
            word-wrap: break-word;
            word-break: break-all;
            display: flex;
            flex-wrap: wrap;
          }

          .test-title-fill-item {
            margin: 5px;
          }

          .test-cont {
            display: flex;
            flex-direction: column;
            padding-bottom: 20rpx;
            color: #333333;

            .test-cont-item {
              padding: 20rpx;
              display: flex;
              background-color: #f6f6f6;
              margin-bottom: 20rpx;
              border-radius: 10rpx;
              align-items: center;
              justify-content: center;
              position: relative;

              &::after {
                background: #333;
                content: "";
                width: 100%;
                height: 100%;
                position: absolute;
                opacity: 0;
                transition: all 0.35s;
              }

              &:active::after {
                opacity: .3;
                width: 0%;
                transition: 0s;
              }

              .cont {
                flex: 1;
                height: 100%;
                padding-left: 20rpx;
                display: flex;
                align-items: center;
                justify-content: space-between;

                .cont-text {
                  display: flex;
                  align-items: center;
                  flex-wrap: wrap;
                  flex: 1;
                  height: 100%;
                  font-p: 32rpx;

                  .image {
                    width: 50%;
                    height: auto;
                    margin-right: 20rpx;
                  }
                }

                .cont-icon {
                  width: 40rpx;
                  margin-left: 20rpx;
                  color: #5677fc;
                  font-size: 36rpx;
                }
              }

              .key {
                width: 50rpx;
                height: 50rpx;
                background-color: #d0d0d0;
                border-radius: 50%;
                color: #FFFFFF;
                display: flex;
                align-items: center;
                justify-content: center;
              }

              &.active_true {
                background-color: rgba(86, 119, 252, 0.2);

                .key {
                  background-color: #5677fc;
                }

                .cont {
                  .cont-icon {
                    font-weight: bold;
                    color: #5677fc;
                  }
                }
              }

              &.active {
                background-color: rgba(255, 68, 0, 0.2);

                .key {
                  background-color: #ff4400;
                }

                .cont {
                  .cont-icon {
                    font-weight: bold;
                    color: #ff4400;
                  }
                }
              }

            }
          }
        }

        .test-describe {
          // padding: 0 20rpx;
          padding-bottom: 10px;
          background-color: #fff;

          .describe-title {
            height: 48px;
            line-height: 48px;
            display: flex;

            text {
              color: #666;
              font-size: 12px;
            }

            image {
              width: 14px;
              height: 14px;
              margin-top: 17px;
              margin-left: 3px;
            }
          }

          .describe-cont {
            // background: #f5f5f5;
            // padding: 12rpx;
            display: flex;
            flex-direction: column;
            font-size: 34rpx;
            margin-top: 8rpx;
            line-height: 40rpx;

            // & > view {
            //   color: #666;
            //   font-size: 30rpx;
            //   background-color: #fff;
            //   text-indent: 15px;
            //   margin-bottom: 2rpx;
            //   // margin-top: 2rpx;
            //   // border-radius: 10rpx;

            //   // &:nth-child(3) {
            //   //   font-size: 12px;
            //   //   line-height: 20px;
            //   // }
            // }
          }
        }
      }
    }
  }


  .fixed-bottom {
    .tibiao {
      background: #fff;
      width: 100%;
      height: 50vh;
      padding: 35rpx;
      padding-bottom: calc(constant(safe-area-inset-bottom) + 35rpx);
      padding-bottom: calc(constant(safe-area-inset-bottom) + 35rpx);
      border-radius: 20rpx 20rpx 0 0;

      .tibiao-scroll {
        height: 100%;

        .tibiao-scroll-list {
          display: flex;
          align-items: center;
          flex-wrap: wrap;

          .tibiao-item {
            height: 100rpx;
            width: 100rpx;
            border-radius: 50%;
            margin-bottom: 30rpx;
            border: 1rpx solid #d0d0d0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 45rpx;

            &:nth-child(5n) {
              margin-right: 0;
            }

            &.tibiao-right {
              background: #4caf50;
              color: #fff;
            }

            &.tibiao-error {
              background: #ff4400;
              color: #fff;
            }

            &.selected {
              background: #5677fc;
              color: #fff;
            }
          }
        }


      }
    }
  }
}


.cu-list {
  width: 100%;
  height: 100rpx;
  position: fixed;
  left: 0;
  bottom: 0;
  text-align: center;
  border-radius: 8px;
}

.cu-list.grid > .cu-item {
  padding-top: 5px;
}

.cu-list image {
  width: 25px;
  height: 25px;
  display: inline-block;
  margin: 0 auto;
}

// 弹窗
.result {
  width: 100%;
  height: 100vh;
  background: #fff;
  padding-top: 10px;
}

.progress_box {
  position: relative;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.progress_bg {
  position: absolute;
  width: 220px;
  height: 220px;
}

.progress_bar {
  width: 220px;
  height: 220px;
}

.progress_txt {
  position: absolute;
  font-size: 28upx;
  color: #999999;
}

.progress_info {
  font-size: 36upx;
  padding-left: 16upx;
  letter-spacing: 2upx;
  font-size: 52upx;
  color: #333333;
}

.progress_dot {
  width: 16upx;
  height: 16upx;
  border-radius: 50%;
  background-color: #fb9126;
}

.table {
  width: 90%;
  margin: 0 auto;
  overflow: hidden;
}

.describe-item {
  // height: 70rpx;
  padding-top: 20rpx;
  text-indent: 30rpx;
}

.flex_1 {
  text-align: left;
}

.flex_2 {
  text-align: right;
}

.red {
  color: #f00;
}

.error {
  display: inline-block;
  height: 30px;
  line-height: 30px;
  border-radius: 5px;
  padding: 0 10px;
  margin-left: 15px;
}

/*收藏 */

.tui-fabulous__box {
  position: relative;
}

.tui-fabulous {
  position: absolute;
  left: 60px;
  top: 0;
  visibility: hidden;
}

.tui-fabulous__active {
  animation: fabulousAni 2s linear;
}

@keyframes fabulousAni {
  0% {
    transform: translateY(0) scale(0.8);
    visibility: visible;
    opacity: 1;
  }

  15% {
    transform: translateY(-40px) scale(1.25);
    opacity: 1;
  }

  100% {
    transform: translateY(-240px) scale(0.5);
    visibility: hidden;
    opacity: 0;
  }
}

/* 红心收藏效果  */

.cu-list.grid > .cu-item text {
  margin-top: 0;
}

.cu-list.grid > .cu-item:after {
  border: 0px;
}

.fix-bottom {
  bottom: calc(constant(safe-area-inset-bottom) + 30rpx);
  bottom: calc(env(safe-area-inset-bottom) + 30rpx);
  width: 95%;
  position: fixed;
  margin: 0 auto;
  left: 0;
  right: 0;
}

.tui-prompt-title {
  padding-bottom: 20rpx;
  font-size: 34rpx;
  font-weight: bold;
  text-align: center;
}

.tui-flex-box {
  width: 100%;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  margin-top: 40rpx;
}

.tui-flex-botton-view {
  width: 45%;
  margin: 0 auto;
}

@keyframes anime {
  0% {
    background-size: 0% 0%;
  }

  100% {
    background-position: 100% 100%;
  }
}

/** 填空题输入框 */
.fill-input {
  border: 0px;
  border-bottom: 2px solid #5677fc;
  width: 200rpx;
  margin: 0 10rpx;
}

.fill-input-right {
  border-bottom: 2px solid #4caf50;
}

.fill-input-error {
  border-bottom: 2px solid #ff4400;
}

.btn-confirm {
  margin-top: 60rpx;
  margin-bottom: 20rpx;
}

/** 简答题输入框 */
.short-input {
  border: 0px;
  border-bottom: 2px solid #5677fc;
  width: 100%;
  min-height: 500rpx;
  margin: 10rpx;
}

.short-input-right {
  color: #4caf50;
}

.short-input-error {
  color: #ff4400;
}

.material-title {
  background-color: #fff;
  padding: 10px;
}

.material-title-tip {
  font-size: 36rpx;
  font-weight: bold;
}

.title-video {
  width: 100%;
  min-height: 200px;
  pointer-events: auto !important;
}

.explain-video-view {
  width: 100%;
  min-height: 240px;
  background-color: #fff;
  padding: 20px 0px;
}

.explain-video {
  width: 100%;
  min-height: 200px;
  pointer-events: auto !important;
}

.fab-bg-color {
  background-color: #e6e6e6;
}

textarea::-webkit-input-placeholder {
  font-size: 24rpx;
}

textarea:-moz-placeholder {
  font-size: 24rpx;
}

textarea::-moz-placeholder {
  font-size: 24rpx;
}

textarea::-ms-input-placeholder {
  font-size: 24rpx;
}

.text-right-answer {
  font-size: 25rpx;
  color: #fff;
  background: linear-gradient(135deg, #7892fd, #5677fc);
  padding: 8rpx 0rpx;
  border-radius: 15rpx 15rpx 15rpx 0;
  margin-left: 20rpx;
  width: 168rpx;
  height: 50rpx;
  line-height: 38rpx;
  display: inline-block;
}

.text-right-answer-val {
  font-size: 28rpx;
  color: #5677fc;
  padding: 8rpx 0rpx;
  border-radius: 15rpx 15rpx 15rpx 0;
  margin-left: 0;
  // width: 150rpx;
  // height: 50rpx;
  line-height: 40rpx;
  display: inline-block;
}

.answer-row {
  display: flex;
  align-items: center;
  margin-bottom: 10rpx;
}

.text-indent {
  text-indent: 30rpx;
}

.text-user-answer {
  font-size: 24rpx;
  color: #fff;
  background: linear-gradient(135deg, #7892fd, #5677fc);
  padding: 8rpx 0rpx;
  border-radius: 15rpx 15rpx 15rpx 0;
  margin-left: 20rpx;
  width: 168rpx;
  // height: 50rpx;
  line-height: 38rpx;
  display: flex;
  align-items: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;

  text {
    color: #fff;
    font-size: 24rpx;
  }
}

.text-right-answer-val-item {
  font-size: 28rpx;
  color: #5677fc;
  padding: 8rpx 0rpx;
  border-radius: 15rpx 15rpx 15rpx 0;
  margin-left: 0;
}

.explain-content {
  background-color: #fff9e6;
  margin: 10rpx 20rpx;
  border-radius: 10rpx;
  padding: 10rpx;
}

.explain-content-title {
  font-size: 32rpx;
  font-weight: bold;
  margin-bottom: 20rpx;
  text-indent: 30rpx;

  text {
    font-size: 30rpx;
  }
}

.explain-content-text {
  font-size: 30rpx !important;
  color: #333333;
  text-indent: 30rpx;
  padding: 6rpx;
  line-height: 42rpx;
  width: 90%;
  display: block;
}

.pt-0 {
  padding-top: 0 !important;
}

.font-size-30 {
  font-size: 30rpx;
}

</style>

