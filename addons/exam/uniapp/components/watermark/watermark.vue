<template>
  <view>
    <!-- 水印层（浮层，不阻挡点击） -->
    <view class="wm-root" :style="bgStyle"></view>

    <!-- 页面内容层 -->
    <slot></slot>

    <!-- 透明但可渲染的 canvas -->
    <canvas canvas-id="wmCanvas" class="wm-canvas"></canvas>
  </view>
</template>

<script>
export default {
  props: {
    text: { type: String, default: "CONFIDENTIAL" }
  },

  data() {
    return { watermarkImg: "" };
  },

  computed: {
    bgStyle() {
      return this.watermarkImg
        ? `background-image:url(${this.watermarkImg});background-size:200px 200px;background-repeat:repeat;`
        : "";
    }
  },

  mounted() {
    // 确保真机渲染完成
    this.$nextTick(() => {
      setTimeout(() => this.draw(), 50);
    });
  },

  methods: {
    draw() {
      const ctx = uni.createCanvasContext("wmCanvas", this);
      const w = 200, h = 200;

      ctx.setFillStyle("rgba(255,255,255,0)");
      ctx.fillRect(0, 0, w, h);

      ctx.setFontSize(14);
      ctx.setFillStyle("rgba(0,0,0,0.06)");
      ctx.translate(100, 100);
      ctx.rotate(-30 * Math.PI / 180);
      ctx.fillText(this.text, -60, 0);

      ctx.draw(false, () => {
        // ★ 延迟确保真机 draw 完成
        setTimeout(() => {
         uni.canvasToTempFilePath({
           canvasId: "wmCanvas",
           fileType: 'png',
           quality: 1,
           success: res => {
             // 再次读取为 base64
             uni.getFileSystemManager().readFile({
               filePath: res.tempFilePath,
               encoding: 'base64',
               success: r => {
                 this.watermarkImg = 'data:image/png;base64,' + r.data
               }
             });
           }
         }, this);

        }, 120);
      });
    }
  }
};
</script>

<style scoped>
.wm-root {
  position: fixed;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;

  pointer-events: none;
  z-index: 999999;
}

/* ★ 不能移出屏幕，只能透明（真机必须可渲染） */
.wm-canvas {
  width: 200px;
  height: 200px;
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0; /* 真机能渲染 */
}
</style>
