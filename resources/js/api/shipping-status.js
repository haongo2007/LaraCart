import request from '@/utils/request';
import Resource from '@/api/resource';

class ShippingStatusResource extends Resource {
  constructor() {
    super('shipping-status');
  }
}

export { ShippingStatusResource as default };
