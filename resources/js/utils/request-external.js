import '@/bootstrap';
import { Message } from 'element-ui';

// Create axios instance
const service = window.axios.create({
  timeout: 10000, // Request timeout
});

// Request intercepter
service.interceptors.request.use(
  config => {
    config.withCredentials = false;
    return config;
  },
  error => {
    // Do something with request error
    console.log(error); // for debug
    Promise.reject(error);
  }
);

// response pre-processing
service.interceptors.response.use(
  response => {
    return response.data;
  },
  error => {
    let message = error;
    if (error.message) {
      message = error.message;
    } else if (error.response.data && error.response.data.errors) {
      message = error.response.data.errors;
    } else if (error.response.data && error.response.data.error) {
      message = error.response.data.error;
    } else if (error.response.data && error.response.data.erro) {
      message = error.response.data.erro;
    }

    Message({
      message: message,
      type: 'error',
      duration: 5 * 1000,
      showClose: true,
    });
    return Promise.reject(error);
  }
);

export default service;
