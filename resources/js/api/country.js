import request from '@/utils/request';
import Resource from '@/api/resource';

class CountryResource extends Resource {
  constructor() {
    super('country');
  }

  getFlags(code) {
    return request({
      url: '/' + this.uri + '/flags/'+code,
      method: 'get',
    });
  }
}

export { CountryResource as default };
