import request from '@/utils/request';
import Resource from '@/api/resource';

class CustomFieldResource extends Resource {
  constructor() {
    super('custom-field');
  }
}

export { CustomFieldResource as default };
