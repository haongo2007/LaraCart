import request from '@/utils/request';
import Resource from '@/api/resource';

class CurrencyResource extends Resource {
  constructor() {
    super('currency');
  }
}

export { CurrencyResource as default };
