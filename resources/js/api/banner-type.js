import request from '@/utils/request';
import Resource from '@/api/resource';

class BannerTypeResource extends Resource {
  constructor() {
    super('banner-type');
  }
}

export { BannerTypeResource as default };
