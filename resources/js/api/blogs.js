import request from '@/utils/request';
import Resource from '@/api/resource';

class BlogsResource extends Resource {
  constructor() {
    super('blogs');
  }
}

export { BlogsResource as default };
