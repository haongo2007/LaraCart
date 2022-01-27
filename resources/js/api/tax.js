import request from '@/utils/request';
import Resource from '@/api/resource';

class TaxResource extends Resource {
  constructor() {
    super('tax');
  }
}

export { TaxResource as default };
