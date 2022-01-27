<template>
        <!-- <div class="fm-notification">
            <transition-group name="notify">
                <div class="fm-notification-item" role="alert"
                     v-for="(notification, index) in notifications"
                     v-bind:class="`fm-${notification.status}`"
                     v-bind:key="`notify-${index}`">
                    {{ notification.message }}
                </div>
            </transition-group>
        </div> -->
</template>

<script>
import EventBus from './../../eventBus';

export default {
  name: 'notification',
  mounted() {
    /**
     * Listen 'addNotification' events
     */
    EventBus.$on('addNotification', ({ status, message }) => this.$message({
        message: message,
        type: status
    }));
  },
  methods: {
    /**
     * Show new notification
     * @param status
     * @param message
     */
    // addNotification(status, message) {
    //   this.notifications.push({
    //     status, message,
    //   });
    //   // timeout for closing
    //   setTimeout(() => {
    //     this.notifications.shift();
    //   }, 3000);
    // },
  },
};
</script>