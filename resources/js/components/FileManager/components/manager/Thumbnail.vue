<template>
  <div>
    <transition name="fade" mode="out-in">
      <div class="block">
        <i v-if="!src" class="far fa-file-image fa-5x pb-2"></i>
        <el-avatar 
                shape="square" 
                :size="100" 
                v-else
                v-bind:src="src">
        </el-avatar>
      </div>
    </transition>
  </div>
</template>

<script>
import GET from './../../http/get';

export default {
  name: 'Thumbnail',
  data() {
    return {
      src: '',
    };
  },
  props: {
    disk: {
      type: String,
      required: true,
    },
    file: {
      type: Object,
      required: true,
    },
  },
  watch: {
    'file.timestamp': 'loadImage',
  },
  mounted() {
    if (window.IntersectionObserver) {
      const observer = new IntersectionObserver(
        (entries, obs) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              this.loadImage();
              obs.unobserve(this.$el);
            }
          });
        },
        {
          root: null,
          threshold: '0.5',
        },
      );

      // add observer for template
      observer.observe(this.$el);
    } else {
      this.loadImage();
    }
  },
  computed: {
    /**
     * Authorization required
     * @return {*}
     */
    auth() {
      return this.$store.getters['fm/settings/authHeader'];
    },
  },
  methods: {
    /**
     * Load image
     */
    loadImage() {
      // if authorization required
      if (this.auth) {
        GET.thumbnail(
          this.disk,
          this.file.path,
        ).then((response) => {
          const mimeType = response.headers['content-type'].toLowerCase();
          const imgBase64 = Buffer.from(response.data, 'binary').toString('base64');

          this.src = `data:${mimeType};base64,${imgBase64}`;
        });
      } else {
        this.src = `${this.$store.getters['fm/settings/baseUrl']}thumbnails?disk=${this.disk}&path=${encodeURIComponent(this.file.path)}&v=${this.file.timestamp}`;
      }
    },
  },
};
</script>
