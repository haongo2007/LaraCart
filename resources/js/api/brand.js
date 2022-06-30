import request from '@/utils/request';
import Resource from '@/api/resource';

class BrandResource extends Resource {
  constructor() {
    super('brand');
  }
}

export { BrandResource as default };
