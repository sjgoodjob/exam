<template>
  <view>
    <!-- 水印层（浮层，永远不阻挡点击） -->
    <view class="wm-root" :style="bgStyle"></view>

    <!-- 页面内容层（保持事件） -->
    <slot></slot>

    <!-- 隐藏的 canvas -->
    <canvas canvas-id="wmCanvas" class="wm-canvas"></canvas>
  </view>
</template>

<script>
export default {
  props: {
    text: { type: String, default: "CONFIDENTIAL" }
  },

  data() {
    return { watermarkImg: "" }
  },

  computed: {
    bgStyle() {
      return this.watermarkImg
        ? `background-image:url(${this.watermarkImg});`
        : "";
    }
  },

  mounted() {
    this.draw();
  },

  methods: {
    draw() {
      const ctx = uni.createCanvasContext("wmCanvas", this);
      const w = 200, h = 200;

      ctx.setFillStyle("rgba(255,255,255,0)");
      ctx.fillRect(0,0,w,h);

      ctx.setFontSize(14);
      ctx.setFillStyle("rgba(0,0,0,0.06)");
      ctx.translate(100,100);
      ctx.rotate(-30 * Math.PI / 180);
      ctx.fillText(this.text, -60, 0);

      ctx.draw(false, () => {
        uni.canvasToTempFilePath({
          canvasId: "wmCanvas",
          success: res => this.watermarkImg = res.tempFilePath
        }, this);
      });
    }
  }
}
</script>

<style scoped>
/* 浮动水印层（最顶层 + 不影响点击） */
.wm-root {
  position: fixed;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;

  pointer-events: none;  /* ★ 不阻挡点击 */
  background-size: 200px 200px;
  background-repeat: repeat;

  z-index: 999999;
}

/* 隐藏 canvas */
.wm-canvas {
  position: absolute;
  left: -9999px;
  top: -9999px;
}
</style>
