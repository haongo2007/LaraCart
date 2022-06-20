import request from '@/utils/request';
import Resource from '@/api/resource';

class PageResource extends Resource {
  constructor() {
    super('page');
  }
  get(params) {
    return request({
      url: '/' + this.uri + '/' + params.id+ '/' +params.lang,
      method: 'get',
    });
  }
}

export { PageResource as default };
