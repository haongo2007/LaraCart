import request from '@/utils/request';
import Resource from '@/api/resource';

class ProductResource extends Resource {
  constructor() {
    super('product');
  }

  getMaxPriceProduct(type) {
    return request({
      url: '/' + this.uri + '/getMaxPriceProduct/' + type,
      method: 'get',
    });
  }

  
  update(id, resource) {
    return request({
      url: '/' + this.uri + '/' + id,
      method: 'post',
      data: resource,
    });
  }
}

export { ProductResource as default };
