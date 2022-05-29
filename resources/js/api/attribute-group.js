import request from '@/utils/request';
import Resource from '@/api/resource';

class AttributeGroupResource extends Resource {
  constructor() {
    super('attributeGroup');
  }
}

export { AttributeGroupResource as default };
