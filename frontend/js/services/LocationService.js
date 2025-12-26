const LocationService = {
    getAll: async function() {
        const res = await ApiService.get('locations');
        return res.data || [];
    },
    getById: async function(id) {
        const res = await ApiService.get(`locations/${id}`);
        return res.data;
    },
    create: async function(data) {
        return await ApiService.post('locations', data);
    },
    update: async function(id, data) {
        return await ApiService.put(`locations/${id}`, data);
    },
    delete: async function(id) {
        return await ApiService.delete(`locations/${id}`);
    }
};
