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
}

export { ProductResource as default };
