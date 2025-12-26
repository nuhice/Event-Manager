const ApiService = {
    apiUrl: window.API_CONFIG?.baseUrl || 'http://localhost/eventmanager/backend',

    async parseResponse(response) {
        const contentType = response.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
            try {
                return await response.json();
            } catch (e) {
                return await response.text();
            }
        }
        return await response.text();
    },

    async get(endpoint) {
        try {
            const response = await fetch(`${this.apiUrl}/${endpoint}`, {
                headers: AuthService.getAuthHeader()
            });
            const data = await this.parseResponse(response);
            if (!response.ok) {
                const msg = (typeof data === 'string') ? data : (data && data.error) || 'Request failed';
                throw new Error(msg);
            }
            return data;
        } catch (error) {
            throw error;
        }
    },

    async post(endpoint, body) {
        try {
            const response = await fetch(`${this.apiUrl}/${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...AuthService.getAuthHeader()
                },
                body: JSON.stringify(body)
            });
            const data = await this.parseResponse(response);
            if (!response.ok) {
                const msg = (typeof data === 'string') ? data : (data && data.error) || 'Request failed';
                throw new Error(msg);
            }
            return data;
        } catch (error) {
            throw error;
        }
    },

    async put(endpoint, body) {
        try {
            const response = await fetch(`${this.apiUrl}/${endpoint}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    ...AuthService.getAuthHeader()
                },
                body: JSON.stringify(body)
            });
            const data = await this.parseResponse(response);
            if (!response.ok) {
                const msg = (typeof data === 'string') ? data : (data && data.error) || 'Request failed';
                throw new Error(msg);
            }
            return data;
        } catch (error) {
            throw error;
        }
    },

    async delete(endpoint) {
        try {
            const response = await fetch(`${this.apiUrl}/${endpoint}`, {
                method: 'DELETE',
                headers: AuthService.getAuthHeader()
            });
            const data = await this.parseResponse(response);
            if (!response.ok) {
                const msg = (typeof data === 'string') ? data : (data && data.error) || 'Request failed';
                throw new Error(msg);
            }
            return data;
        } catch (error) {
            throw error;
        }
    }
};
