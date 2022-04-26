import request from '@/utils/request';
import Resource from '@/api/resource';

class CouponResource extends Resource {
  constructor() {
    super('discount');
  }
}

export { CouponResource as default };
