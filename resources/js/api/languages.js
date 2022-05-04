import request from '@/utils/request';
import Resource from '@/api/resource';

class LanguageResource extends Resource {
  constructor() {
    super('languages');
  }
  fetchLanguagesActive(id) {
    return request({
      url: '/languages/getActiveLanguage/' + id,
      method: 'get',
    });
  }
}

export { LanguageResource as default };
