import request from '@/utils/request';
import Resource from '@/api/resource';

class PaymentStatusResource extends Resource {
  constructor() {
    super('payment-status');
  }
}

export { PaymentStatusResource as default };
