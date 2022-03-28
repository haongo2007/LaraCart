import '@/bootstrap';
import { Message } from 'element-ui';
import { isLogged, setLogged } from '@/utils/auth';
import Cookies from 'js-cookie';

// Create axios instance
const service = window.axios.create({
  baseURL: process.env.MIX_BASE_API,
  timeout: 10000, // Request timeout
});

// Request intercepter
service.interceptors.request.use(
  config => {
    const token = isLogged();
    if (token) {
      config.headers['Authorization'] = 'Bearer ' + isLogged(); // Set JWT token
    }
    if (Cookies.get('language')) {
      config.headers['x-localization'] = Cookies.get('language'); // Set Language
    }
    return config;
  },
  error => {
    // Do something with request error
    Promise.reject(error);
  }
);

// response pre-processing
service.interceptors.response.use(
  response => {
    if (response.headers.authorization) {
      setLogged(response.headers.authorization);
      response.data.token = response.headers.authorization;
    }
    return response.data;
  },
  error => {
    let message = error.message;
    if (error.response.data && error.response.data.errors) {
      message = error.response.data.errors;
    } else if (error.response.data && error.response.data.error) {
      message = error.response.data.error;
    }
    if (typeof message === 'object' && message !== null) {
      Object.keys(message).forEach(function(key, index) {
        if (Array.isArray(message[key])) {
          message[key].forEach(function(v, i) {
            setTimeout(function(argument) {
              Message({
                message: v,
                type: 'error',
                duration: 5 * 1000,
              });
            }, 50);
          });
        } else {
          Message({
            message: message[key],
            type: 'error',
            duration: 5 * 1000,
          });
        }
      });
    } else {
      Message({
        message: message,
        type: 'error',
        duration: 5 * 1000,
      });
    }

    return Promise.reject(error);
  }
);

export default service;
