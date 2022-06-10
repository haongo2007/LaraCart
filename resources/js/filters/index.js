// set function parseTime,formatTime to filter
export { parseTime, formatTime } from '@/utils';

export function pluralize(time, label) {
  if (time === 1) {
    return time + label;
  }
  return time + label + 's';
}

export function timeAgo(time) {
  const between = Date.now() / 1000 - Number(time);
  if (between < 3600) {
    return pluralize(~~(between / 60), ' minute');
  } else if (between < 86400) {
    return pluralize(~~(between / 3600), ' hour');
  } else {
    return pluralize(~~(between / 86400), ' day');
  }
}

/* Number formating*/
export function numberFormatter(num, digits) {
  const si = [
    { value: 1E18, symbol: 'E' },
    { value: 1E15, symbol: 'P' },
    { value: 1E12, symbol: 'T' },
    { value: 1E9, symbol: 'G' },
    { value: 1E6, symbol: 'M' },
    { value: 1E3, symbol: 'k' },
  ];
  for (let i = 0; i < si.length; i++) {
    if (num >= si[i].value) {
      return (num / si[i].value + 0.1).toFixed(digits).replace(/\.0+$|(\.[0-9]*[1-9])0+$/, '$1') + si[i].symbol;
    }
  }
  return num.toString();
}

export function toThousandFilter(num, currency = '') {
  return (+num || 0).toString().replace(/^-?\d+/g, m => m.replace(/(?=(?!\b)(\d{3})+$)/g, ',')) +' '+ currency;
}

export function uppercaseFirst(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

export function formatPhone(phone) {
  return phone.replace(/[^0-9]/g, '')
    .replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
}

export function statusFilter(status, name) {
  const statusMapLabel = {
    0: 'danger',
    1: 'success',
  };
  const statusMapName = {
    0: 'Deactive',
    1: 'Active',
  };
  if (name) {
    return statusMapName[status];
  }
  return statusMapLabel[status];
}

export function kindFilter(kind, name) {
  const statusKindLabel = {
    0: '',
    1: 'success',
    2: 'danger',
  };
  const statusKindName = {
    0: 'Single',
    1: 'Bundle',
    2: 'Group',
  };
  if (name) {
    return statusKindName[kind];
  }
  return statusKindLabel[kind];
}

export function changeCurrency(price, exchange_rate, currency) {
  let result = 0;
  if (price > 0 ) {
    result = price * exchange_rate;
  }
  return toThousandFilter(result,currency);
}