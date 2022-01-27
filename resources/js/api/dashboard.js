import request from '@/utils/request';

export function overView(query) {
  return request({
    url: '/dashboard/overview',
    method: 'get',
    params: query,
  });
}

export function orders(query) {
  return request({
    url: '/dashboard/orders',
    method: 'get',
    params: query,
  });
}
