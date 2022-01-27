import request from '@/utils/request';
import Resource from '@/api/resource';

class CountryResource extends Resource {
  constructor() {
    super('country');
  }
}

export { CountryResource as default };
