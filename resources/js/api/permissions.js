import request from '@/utils/request';
import Resource from '@/api/resource';

class PermissionsResource extends Resource {
  constructor() {
    super('permissions');
  }
}

export { PermissionsResource as default };
