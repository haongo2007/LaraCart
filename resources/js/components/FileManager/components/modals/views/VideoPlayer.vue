<template>
    <div>
        <video ref="fmVideo" controls></video>
        <p>{{ videoFile.basename }}</p>
    </div>
</template>

<script>
import Plyr from 'plyr';
import modal from './../mixins/modal';
import translate from './../../../mixins/translate';

export default {
  name: 'Player',
  mixins: [modal, translate],
  data() {
    return {
      player: {},
    };
  },
  mounted() {
    // initiate video player
    this.player = new Plyr(this.$refs.fmVideo);
    // load source
    this.player.source = {
      type: 'video',
      title: this.videoFile.filename,
      sources: [{
        src: `${this.$store.getters['fm/settings/baseUrl']}stream-file?disk=${this.selectedDisk}&path=${encodeURIComponent(this.videoFile.path)}`,
        type: `audio/${this.videoFile.extension}`,
      }],
    };
  },
  beforeDestroy() {
    this.player.destroy();
  },
  computed: {
    /**
     * Selected disk
     * @returns {*}
     */
    selectedDisk() {
      return this.$store.getters['fm/selectedDisk'];
    },

    /**
     * Video file
     * @returns {*}
     */
    videoFile() {
      return this.$store.getters['fm/selectedItems'][0];
    },
  },
  methods: {
  },
};
</script>