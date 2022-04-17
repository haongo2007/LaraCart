import request from '@/utils/request';
import Resource from '@/api/resource';

class PermissionsResource extends Resource {
  constructor() {
    super('permissions');
  }
  getAllPath() {
    return request({
      url: '/' + this.uri + '/getAllPath',
      method: 'get',
    });
  }
}

export { PermissionsResource as default };
