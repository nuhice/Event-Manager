const ApiService = {
    apiUrl: 'http://localhost/eventmanager/backend',

    async get(endpoint) {
        try {
            const response = await fetch(`${this.apiUrl}/${endpoint}`, {
                headers: AuthService.getAuthHeader()
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'Request failed');
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
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'Request failed');
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
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'Request failed');
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
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'Request failed');
            return data;
        } catch (error) {
            throw error;
        }
    }
};
