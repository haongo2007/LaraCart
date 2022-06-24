import i18n from '@/lang';
import store from '@/store';
import Cookies from 'js-cookie';

export default function getPageTitle(key) {
	let title_config ,title = '';
	let store_ck = Cookies.get('store');
	if (store_ck) {
		store_ck = JSON.parse(store_ck);
	}else{
		store_ck = Object.keys(store.state.user.storeList)[0];
	}
	if (store_ck && store_ck.length == 0) {
		let index = Object.keys(store.state.user.storeList);
		title_config = store.state.user.storeList[index[0]] ? store.state.user.storeList[index[0]].admin_custom_config.filter((item) => item.key == 'ADMIN_TITLE') : '';
		title = title_config ? title_config[0].value : 'HighLight Admin';
	}else{
		store_ck = typeof store_ck === 'object' ? store_ck[0] : store_ck;
		title_config = store.state.user.storeList[store_ck] ? store.state.user.storeList[store_ck].admin_custom_config.filter((item) => item.key == 'ADMIN_TITLE') : '';
		title = title_config ? title_config[0].value : 'HighLight Admin';
	}

  const hasKey = i18n.te(`route.${key}`);
  if (hasKey && key != undefined) {
    const pageName = i18n.t(`route.${key}`);
    return `${pageName} - ${title}`;
  }
  return `${title}`;
}
