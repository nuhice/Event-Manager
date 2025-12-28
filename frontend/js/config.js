const API_CONFIG = {
    baseUrl: (window.ENV && window.ENV.API_URL && window.ENV.API_URL !== '__API_URL_PLACEHOLDER__')
        ? window.ENV.API_URL
        : 'http://localhost/eventmanager/backend'
};

window.API_CONFIG = API_CONFIG;
console.log('API Config loaded:', API_CONFIG);
console.log('Raw ENV:', window.ENV);
