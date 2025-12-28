const AuthService = {
    apiUrl: window.API_CONFIG.baseUrl,

    async login(email, password) {
        try {
            const response = await fetch(`${this.apiUrl}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (data.success) {
                localStorage.setItem('token', data.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                return data.user;
            } else {
                throw new Error(data.error || 'Login failed');
            }
        } catch (error) {
            throw error;
        }
    },

    async register(name, email, password) {
        try {
            const response = await fetch(`${this.apiUrl}/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name,
                    email,
                    password,
                    role_id: 2
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Registration failed');
            }
            return data;
        } catch (error) {
            throw error;
        }
    },

    logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
    },

    getCurrentUser() {
        const userStr = localStorage.getItem('user');
        return userStr ? JSON.parse(userStr) : null;
    },

    getToken() {
        return localStorage.getItem('token');
    },

    isAuthenticated() {
        return !!this.getToken();
    },

    isAdmin() {
        const user = this.getCurrentUser();
        return user && user.role === 'admin';
    },

    getAuthHeader() {
        const token = this.getToken();
        return token ? { 'Authorization': `Bearer ${token}` } : {};
    }
};
