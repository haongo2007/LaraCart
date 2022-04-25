import request from '@/utils/request';
import Resource from '@/api/resource';

class PageResource extends Resource {
  constructor() {
    super('page');
  }
}

export { PageResource as default };
