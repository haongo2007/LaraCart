import request from '@/utils/request';

export function updateGlobalConfig(data) {
  return request({
    url: '/config-global/update',
    method: 'post',
    data,
  });
}

