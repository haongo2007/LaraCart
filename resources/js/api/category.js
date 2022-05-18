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
  getChildren(data) {
    return request({
      url: '/' + this.uri + '/getChildren',
      method: 'post',
      data: data,
    });
  }
  getNested(ids) {
    return request({
      url: '/' + this.uri + '/getNested',
      method: 'post',
      data: { ids: ids },
    });
  }
}

export { CategoryResource as default };
