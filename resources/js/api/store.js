import request from '@/utils/request';
import Resource from '@/api/resource';

class StoreResource extends Resource {
  constructor() {
    super('store-config');
  }
}

export { StoreResource as default };
