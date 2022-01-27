<template>
  <tags-view v-if="ViewsChildren.length > 0" type="bottom" :visited-views="visitedViews" />
</template>

<script>
import TagsView from '../TagsViewTop/TagsView';

export default {
  components: { TagsView },
  data() {
    return {
    };
  },
  computed: {
    ViewsChildren() {
      var result = [];
      var visitedViews = this.$store.state.tagsView.visitedViews;
      const selectedView = this.$store.state.tagsView.selectedViews;
      visitedViews.forEach(function(i, v) {
        if (visitedViews[v].meta.parent == selectedView) {
          result.push(visitedViews[v]);
        }
      });
      return result;
    },
    visitedViews() {
      return this.ViewsChildren;
    },
  },
};
</script>
