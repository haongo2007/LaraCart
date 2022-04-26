import request from '@/utils/request';
import Resource from '@/api/resource';

class ReportResource extends Resource {
  constructor() {
    super('report');
  }

  product(query) {
    return request({
      url: '/' + this.uri + '/product',
      method: 'get',
      params: query,
    });
  }
}

export { ReportResource as default };
