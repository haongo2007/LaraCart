import request from '@/utils/request';
import Resource from '@/api/resource';

class WeightResource extends Resource {
  constructor() {
    super('weight_unit');
  }
}

export { WeightResource as default };
