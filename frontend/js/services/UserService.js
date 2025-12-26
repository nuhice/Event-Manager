const UserService = {
    getAll: async function() {
        const res = await ApiService.get('users');
        return res.data || [];
    },
    getById: async function(id) {
        const res = await ApiService.get(`users/${id}`);
        return res.data;
    },
    create: async function(data) {
        return await ApiService.post('users', data);
    },
    update: async function(id, data) {
        return await ApiService.put(`users/${id}`, data);
    },
    delete: async function(id) {
        return await ApiService.delete(`users/${id}`);
    }
};
