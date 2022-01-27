import request from '@/utils/request';
import Resource from '@/api/resource';

class PaymentResource extends Resource {
  constructor() {
    super('payment');
  }
}

export { PaymentResource as default };
