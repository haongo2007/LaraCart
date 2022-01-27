<template>
  <div v-if="items.length>0" class="lb">
    <div class="lb-grid" :class="[css,items.length>cells?'lb-grid-' + cells: 'lb-grid-' + items.length]">
      <div v-for="(src, i) in items" v-if="i<cells" class="lb-item" :style="bg(src)" @click="show(i)">
        <span v-if="i==cells-1 && items.length - cells>0" class="lb-more">{{ items.length - cells }}+</span>
      </div>
    </div>

    <transition name="el-zoom-in-center" mode="out-in">
      <div v-if="index>=0" class="lb-modal">
        <el-button class="lb-modal-close" type="primary" icon="el-icon-close" @click="close" />
        <el-button class="lb-modal-prev" icon="el-icon-arrow-left" circle @click="prev" />
        <el-button class="lb-modal-next" icon="el-icon-arrow-right" circle @click="next" />

        <div class="lb-modal-img" @click="close">
          <img :src="src">
          <div v-if="loading" class="spinner spinner-dots-wave">
            <span class="badge badge-primary rounded-circle w-10 h-10">
              <i class="sr-only">&nbsp;</i>
            </span>
            <span class="badge badge-primary rounded-circle w-10 h-10">
              <i class="sr-only">&nbsp;</i>
            </span>
            <span class="badge badge-primary rounded-circle w-10 h-10">
              <i class="sr-only">&nbsp;</i>
            </span>
            <span class="badge badge-primary rounded-circle w-10 h-10">
              <i class="sr-only">&nbsp;</i>
            </span>
            <span class="badge badge-primary rounded-circle w-10 h-10">
              <i class="sr-only">&nbsp;</i>
            </span>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import './css/lightbox.css';
export default {
  name: 'Lightbox',
  props: {
    items: {
      type: Array,
      default: () => {
        return [];
      },
    },

    css: {
      type: String,
      default: () => 'h-250 h-md-400 h-lg-600',
    },

    cells: {
      type: Number,
      default: () => 5,
    },
  },

  data() {
    return {
      src: '',
      index: -1,
      loading: false,
      events: [],
    };
  },

  methods: {

    bind() {
      if (this.events.length > 0) {
        return;
      }

      this.events.push(['keydown', e => {
        if (this.index < 0) {
          return;
        }
        if (e.keyCode === 37) {
          this.prev();
        } else if (e.keyCode === 39) {
          this.next();
        } else if (e.keyCode === 27) {
          this.close();
        }
      }]);
      this.events.forEach(e => {
        window.addEventListener(e[0], e[1]);
      });
    },

    show(i) {
      if (i >= this.items.length) {
        i = 0;
      }
      if (i < 0) {
        i = this.items.length - 1;
      }
      this.loading = true;
      this.bind();
      this.index = i;
      var src = this.items[i];
      var img = new Image();
      img.src = src;
      img.onload = () => {
        this.loading = false;
        this.src = src;
      };
    },
    next() {
      this.show(this.index - 1);
    },

    prev() {
      this.show(this.index + 1);
    },
    close() {
      this.index = -1;
      this.events.forEach(e => {
        window.removeEventListener(e[0], e[1]);
      });
      this.events = [];
    },
    bg(i) {
      return i && i.length > 0 ? `background-image: url('${i}')` : '';
    },

  },

};
</script>
