// translate router.meta.title, be used in breadcrumb sidebar tagsview
export function generateTitle(title, param = '') {
  const hasKey = this.$te('route.' + title);

  if (hasKey) {
    // $t :this method from vue-i18n, inject in @/lang/index.js
    var translatedTitle = this.$t('route.' + title);
    if (param['id'] !== undefined) {
  		translatedTitle += ' - ' + param.id;
  	}
    return translatedTitle;
  }
  return title;
}
