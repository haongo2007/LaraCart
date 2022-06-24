import request from '@/utils/request';
import Resource from '@/api/resource';

class EmailTemplateResource extends Resource {
  constructor() {
    super('email-template');
  }
  getGroups() {
    return request({
      url: '/' + this.uri + '/groups',
      method: 'get',
    });
  }
}

export { EmailTemplateResource as default };
