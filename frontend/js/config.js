const API_CONFIG = {
    baseUrl: (function () {
        const envUrl = (window.ENV && window.ENV.API_URL);
        const isPlaceholder = envUrl === '__API_URL_PLACEHOLDER__';

        if (envUrl && !isPlaceholder) {
            return envUrl;
        }

        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            return 'http://localhost/eventmanager/backend';
        }

        // 4. Fallback for production (Based on logs)
        return 'https://event-manager-ek9w.onrender.com';
    })()
};

window.API_CONFIG = API_CONFIG;
console.log('API Config determined:', API_CONFIG);
console.log('Hostname:', window.location.hostname);
