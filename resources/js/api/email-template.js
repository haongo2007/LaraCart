import request from '@/utils/request';
import Resource from '@/api/resource';

class EmailTemplateResource extends Resource {
  constructor() {
    super('email-template');
  }
}

export { EmailTemplateResource as default };
