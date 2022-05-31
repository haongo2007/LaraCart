export default {
  computed: {
    /**
     * Selected translate
     * @returns {*}
     */
    lang() {
      // If selected translations exists
      if (Object.prototype.hasOwnProperty.call(
        this.$store.state.fm.settings.translations,
        this.$store.state.app.language,
      )) {
        return this.$store.state.fm.settings.translations[
          this.$store.state.app.language
        ];
      }
      // default translate - en
      return this.$store.state.fm.settings.translations.en;
    },
  },
};
