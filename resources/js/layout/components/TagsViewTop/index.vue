<template>
  <tags-view type="top" :visited-views="visitedViews" />
</template>

<script>
import TagsView from './TagsView';

export default {
  components: { TagsView },
  data() {
    return {
    };
  },
  computed: {
    visitedViews() {
    	return this.$store.state.tagsView.visitedViews;
    },
  },
};
</script>
