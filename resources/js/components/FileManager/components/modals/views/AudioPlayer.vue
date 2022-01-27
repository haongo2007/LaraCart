<template>
  <div class="audio-listen">
      <audio ref="fmAudio" controls></audio>
      <div class="p-10">
        <hr>
        <ul class="el-upload-list el-upload-list--text ul-2">
          <li class="el-upload-list__item is-success"
              v-bind:class="playingIndex === index ? 'bg-light' : ''"
              v-for="(item, index) in audioFiles"
              v-bind:key="index">
            <span>
                {{ index }}. {{ item.basename }}
            </span>
            <span v-if="playingIndex === index">
                <div v-if="status === 'playing'">
                    <i v-on:click="togglePlay()" class="fas fa-pause active"></i>
                </div>
                <div v-else>
                    <i v-on:click="togglePlay()" class="fas fa-play"></i>
                </div>
            </span>
            <span v-else>
                <div>
                    <i v-on:click="selectTrack(index)" class="fas fa-play"></i>
                </div>
            </span>
          </li>
        </ul>
      </div>
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
      playingIndex: 0,
      status: 'paused',
    };
  },
  mounted() {
    // initiate player
    this.player = new Plyr(this.$refs.fmAudio, {
      speed: {
        selected: 1,
        options: [0.5, 1, 1.5],
      },
    });

    // select first item in the list
    this.setSource(this.playingIndex);

    // add event listeners
    this.player.on('play', () => {
      this.status = 'playing';
    });

    this.player.on('pause', () => {
      this.status = 'paused';
    });

    this.player.on('ended', () => {
      if (this.audioFiles.length > this.playingIndex + 1) {
        // play next track
        this.selectTrack(this.playingIndex + 1);
      }
    });
  },
  beforeDestroy() {
    // destroy player
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
     * Audio files list
     * @returns {*}
     */
    audioFiles() {
      return this.$store.getters['fm/selectedItems'];
    },
  },
  methods: {
    /**
     * Select another audio track
     * @param index
     */
    selectTrack(index) {
      if (this.player.playing) {
        // stop playing
        this.player.stop();
      }
      // load new source
      this.setSource(index);
      // start play
      this.player.play();

      this.playingIndex = index;
    },

    /**
     * Set source to audio player
     * @param index
     */
    setSource(index) {
      this.player.source = {
        type: 'audio',
        title: this.audioFiles[index].filename,
        sources: [{
          src: `${this.$store.getters['fm/settings/baseUrl']}stream-file?disk=${this.selectedDisk}&path=${encodeURIComponent(this.audioFiles[index].path)}`,
          type: `audio/${this.audioFiles[index].extension}`,
        }],
      };
    },

    /**
     * Play/Pause
     */
    togglePlay() {
      this.player.togglePlay();
    },
  },
};
</script>

<style lang="scss">
.audio-listen{
  .p-10{
    padding:10px;
    .ul-2 li{
      display:flex; 
      justify-content: space-between;
      align-items: center;
      
    }
  }
}
</style>
