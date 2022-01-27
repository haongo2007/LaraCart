import request from '@/utils/request';
import Resource from '@/api/resource';

class LengthResource extends Resource {
  constructor() {
    super('length_unit');
  }
}

export { LengthResource as default };
