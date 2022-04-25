import request from '@/utils/request';
import Resource from '@/api/resource';

class BannerResource extends Resource {
  constructor() {
    super('banner');
  }
}

export { BannerResource as default };
