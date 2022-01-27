import request from '@/utils/request';
import Resource from '@/api/resource';

class CustomerResource extends Resource {
  constructor() {
    super('customer');
  }
}

export { CustomerResource as default };
