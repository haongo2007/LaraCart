import request from '@/utils/request';
import Resource from '@/api/resource';

class ShippingResource extends Resource {
  constructor() {
    super('shipping');
  }
}

export { ShippingResource as default };
