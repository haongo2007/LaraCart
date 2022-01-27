import request from '@/utils/request';
import Resource from '@/api/resource';

class BrandResource extends Resource {
  constructor() {
    super('brand');
  }
  update(id, resource) {
    return request({
      url: '/' + this.uri + '/' + id,
      method: 'post',
      data: resource,
    });
  }
}

export { BrandResource as default };
