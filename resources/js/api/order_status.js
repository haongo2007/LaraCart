import request from '@/utils/request';
import Resource from '@/api/resource';

class OrderStatusResource extends Resource {
  constructor() {
    super('order_status');
  }
}

export { OrderStatusResource as default };
