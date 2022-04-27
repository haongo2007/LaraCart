import request from '@/utils/request';
import Resource from '@/api/resource';

class SubscribeResource extends Resource {
  constructor() {
    super('subscribe');
  }
}

export { SubscribeResource as default };
