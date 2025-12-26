const EventService = {
    getAll: async function() {
        const res = await ApiService.get('events');
        return res.data || [];
    },
    getById: async function(id) {
        const res = await ApiService.get(`events/${id}`);
        return res.data;
    },
    create: async function(data) {
        return await ApiService.post('events', data);
    },
    update: async function(id, data) {
        return await ApiService.put(`events/${id}`, data);
    },
    delete: async function(id) {
        return await ApiService.delete(`events/${id}`);
    }
};
