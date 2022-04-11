import request from '@/utils/request';
import Resource from '@/api/resource';

class StoreConfigResource extends Resource {
  constructor() {
    super('store-config');
  }
}

export { StoreConfigResource as default };
