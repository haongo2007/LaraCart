import request from '@/utils/request';
import Resource from '@/api/resource';

class StoreResource extends Resource {
  constructor() {
    super('store');
  }
  getConfig(id) {
    return request({
      url: '/'+this.uri+'/getConfig/'+id,
      method: 'get',
    });
  }
}

export { StoreResource as default };
