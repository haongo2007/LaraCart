import request from '@/utils/request';

export function fetchLanguagesActive() {
  return request({
    url: '/getActiveLanguage',
    method: 'get',
  });
}
