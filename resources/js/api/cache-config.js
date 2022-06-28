import request from '@/utils/request';

export function clearCache(data) {
  return request({
    url: '/cache/clear',
    method: 'post',
    data,
  });
}