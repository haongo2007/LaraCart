import request from '@/utils/request';
import Resource from '@/api/resource';

class OrdersResource extends Resource {
  constructor() {
    super('orders');
  }
  getRelationData() {
    return request({
      url: '/' + this.uri + '/getRelationOrder',
      method: 'get',
    });
  }
  addMoreItem(resource){
  	return request({
      url: '/' + this.uri + '/addMoreItem',
      method: 'post',
      data: resource,
    });
  }
  deleteItem(resource){
    return request({
      url: '/' + this.uri + '/deleteItem',
      method: 'post',
      data: resource,
    });
  }
  downloadExcel(id, type){
    return request({
      url: '/' + this.uri + '/download?id=' + id + '&type=' + type,
      method: 'get',
      responseType: 'blob',
    });
  }
}

export { OrdersResource as default };
