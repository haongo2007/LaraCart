import request from '@/utils/request';
import Resource from '@/api/resource';

class StoreResource extends Resource {
  constructor() {
    super('store-config');
  }
  getConfig(id) {
    return request({
      url: '/getConfig/'+id,
      method: 'get',
    });
  }
}

export { StoreResource as default };
