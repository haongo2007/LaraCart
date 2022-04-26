import request from '@/utils/request';
import Resource from '@/api/resource';

class ProductFlashsaleResource extends Resource {
  constructor() {
    super('product-flashsale');
  }
}

export { ProductFlashsaleResource as default };
