import Vue from 'vue';
import VueI18n from 'vue-i18n';
import Cookies from 'js-cookie';
import elementEnLocale from 'element-ui/lib/locale/lang/en'; // element-ui lang
import elementViLocale from 'element-ui/lib/locale/lang/vi';// element-ui lang
import usLocale from './us';
import vnLocale from './vn';

Vue.use(VueI18n);

const messages = {
  us: {
    ...usLocale,
    ...elementEnLocale,
  },
  vn: {
    ...vnLocale,
    ...elementViLocale,
  },
};

export function getLanguage() {
  const chooseLanguage = Cookies.get('language');
  if (chooseLanguage) {
    return chooseLanguage;
  }

  // if has not choose language
  const language = (navigator.language || navigator.browserLanguage).toLowerCase();
  const locales = Object.keys(messages);
  for (const locale of locales) {
    if (language.indexOf(locale) > -1) {
      return locale;
    }
  }
  return 'us';
}
const i18n = new VueI18n({
  // set locale
  // options: us | ru | vn | zh
  locale: getLanguage(),
  // set locale messages
  messages,
});

export default i18n;
