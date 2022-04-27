import request from '@/utils/request';
import Resource from '@/api/resource';

class SeoConfigResource extends Resource {
  constructor() {
    super('seo/config');
  }
}

export { SeoConfigResource as default };
