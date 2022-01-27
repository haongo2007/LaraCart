import request from '@/utils/request';
import Resource from '@/api/resource';

class CategoryResource extends Resource {
  constructor() {
    super('category');
  }
  getRecursive(id) {
    return request({
      url: '/' + this.uri + '/getRecursive',
      method: 'post',
      data: { id: id },
    });
  }
  getChildren(id) {
    return request({
      url: '/' + this.uri + '/getChildren',
      method: 'post',
      data: { id: id },
    });
  }
  update(id, resource) {
    return request({
      url: '/' + this.uri + '/' + id,
      method: 'post',
      data: resource,
    });
  }
}

export { CategoryResource as default };
