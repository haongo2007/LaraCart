import request from '@/utils/request';
import Resource from '@/api/resource';

class LanguageResource extends Resource {
  constructor() {
    super('languages');
  }
  fetchLanguagesActive(id) {
    return request({
      url: '/languages/getActiveLanguage',
      method: 'get',
    });
  }
}

export { LanguageResource as default };
