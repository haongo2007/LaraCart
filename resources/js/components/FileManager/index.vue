<template>
  <div class="fm" :class="{ 'fm-full-screen': fullScreen }">
    <el-row :gutter="20">
      <el-col :xs="24" :sm="8" :md="6" :lg="4" style="margin-bottom: 20px;">
        <folder-tree />
      </el-col>
      <el-col :xs="24" :sm="16" :md="18" :lg="20">
        <template>
          <left-manager
            manager="left"
            @click.native="selectManager('left')"
            @contextmenu.native="selectManager('left')"
          />
        </template>
      </el-col>
    </el-row>

    <notification />
    <context-menu />
    <modal v-if="showModal" />
  </div>
</template>

<script>
import '../../views/library/assets/all.css';
/* eslint-disable import/no-duplicates, no-param-reassign */
import { mapState } from 'vuex';
// Axios
import HTTP from './http/axios';
import EventBus from './eventBus';
// Components
import FolderTree from './components/tree/FolderTree.vue';
import LeftManager from './components/manager/Manager.vue';
// import RightManager from './components/manager/Manager.vue';
import Modal from './components/modals/Modal.vue';
import ContextMenu from './components/blocks/ContextMenu.vue';
import Notification from './components/blocks/Notification.vue';
// Mixins
import translate from './mixins/translate';

export default {
  name: 'FileManager',
  components: {
    FolderTree,
    LeftManager,
    Modal,
    ContextMenu,
    Notification,
  },
  mixins: [translate],
  props: {
    /**
     * LFM manual settings
     */
    getFile: {
      type: Boolean,
      default: false,
    },
    settings: {
      type: Object,
      default() {
        return {};
      },
    },
  },
  computed: {
    ...mapState('fm', {
      windowsConfig: state => state.settings.windowsConfig,
      activeManager: state => state.settings.activeManager,
      showModal: state => state.modal.showModal,
      fullScreen: state => state.settings.fullScreen,
    }),
  },
  created() {
    // manual settings
    this.$store.commit('fm/settings/manualSettings', this.settings);

    // initiate Axios
    this.$store.commit('fm/settings/initAxiosSettings');
    this.requestInterceptor();
    this.responseInterceptor();

    // initialize app settings
    this.$store.dispatch('fm/initializeApp');

    /**
     * todo Keyboard event
     */
    /*
    window.addEventListener('keyup', (event) => {
      event.preventDefault();
      event.stopPropagation();

      EventBus.$emit('keyMonitor', event);
    });
    */
    if (this.getFile) {
      this.$store.state.fm.fileCallback = (res) => {
        EventBus.$emit('getFileResponse', res);
      };
    }
  },
  destroyed() {
    // reset state
    this.$store.dispatch('fm/resetState');

    // delete events
    EventBus.$off(['contextMenu', 'addNotification']);
  },
  methods: {
    /**
     * Add axios request interceptor
     */
    requestInterceptor() {
      HTTP.interceptors.request.use((config) => {
        // overwrite base url and headers
        config.baseURL = this.$store.getters['fm/settings/baseUrl'];
        config.headers = this.$store.getters['fm/settings/headers'];

        // loading spinner +
        this.$store.commit('fm/messages/addLoading');

        return config;
      }, (error) => {
        // loading spinner -
        this.$store.commit('fm/messages/subtractLoading');
        return Promise.reject(error);
      });
    },

    /**
     * Add axios response interceptor
     */
    responseInterceptor() {
      HTTP.interceptors.response.use((response) => {
        // loading spinner -
        this.$store.commit('fm/messages/subtractLoading');

        // create notification, if find message text
        if (Object.prototype.hasOwnProperty.call(response.data, 'result')) {
          if (response.data.result.message) {
            const message = {
              status: response.data.result.status,
              message: Object.prototype.hasOwnProperty.call(this.lang.response, response.data.result.message)
                ? this.lang.response[response.data.result.message]
                : response.data.result.message,
            };

            // show notification
            EventBus.$emit('addNotification', message);

            // set action result
            this.$store.commit('fm/messages/setActionResult', message);
          }
        }

        return response;
      }, (error) => {
        // loading spinner -
        this.$store.commit('fm/messages/subtractLoading');

        const errorMessage = {
          status: 0,
          message: '',
        };

        const errorNotificationMessage = {
          status: 'error',
          message: '',
        };

        // add message
        if (error.response) {
          errorMessage.status = error.response.status;

          if (error.response.data.message) {
            const trMessage = Object.prototype.hasOwnProperty.call(this.lang.response, error.response.data.message)
              ? this.lang.response[error.response.data.message]
              : error.response.data.message;

            errorMessage.message = trMessage;
            errorNotificationMessage.message = trMessage;
          } else {
            errorMessage.message = error.response.statusText;
            errorNotificationMessage.message = error.response.statusText;
          }
        } else if (error.request) {
          errorMessage.status = error.request.status;
          errorMessage.message = error.request.statusText || 'Network error';
          errorNotificationMessage.message = error.request.statusText || 'Network error';
        } else {
          errorMessage.message = error.message;
          errorNotificationMessage.message = error.message;
        }

        // set error message
        this.$store.commit('fm/messages/setError', errorMessage);

        // show notification
        EventBus.$emit('addNotification', errorNotificationMessage);

        return Promise.reject(error);
      });
    },

    /**
     * Select manager (when shown 2 file manager windows)
     * @param managerName
     */
    selectManager(managerName) {
      if (this.activeManager !== managerName) {
        this.$store.commit('fm/setActiveManager', managerName);
      }
    },
  },
};
</script>

<style lang="scss" >
  @import "~plyr/src/sass/plyr.scss";
  table .sort-name-file{
    display:flex;
    align-items: center;
    justify-content: space-between;
    span{
      width: 80%;
      white-space: nowrap;
      overflow: hidden !important;
      text-overflow: ellipsis;
    }
  }
  .fm{
    background:#FFFFFF;
  }
  tr.table-info{
    background:#e7faf0;
    color:#13ce66;
  }
</style>

