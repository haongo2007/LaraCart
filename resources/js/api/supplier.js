import request from '@/utils/request';
import Resource from '@/api/resource';

class SupplierResource extends Resource {
  constructor() {
    super('supplier');
  }
}

export { SupplierResource as default };
