const BookingService = {
    getAll: async function() {
        const res = await ApiService.get('bookings');
        return res.data || [];
    },
    getById: async function(id) {
        const res = await ApiService.get(`bookings/${id}`);
        return res.data;
    },
    create: async function(data) {
        return await ApiService.post('bookings', data);
    },
    update: async function(id, data) {
        return await ApiService.put(`bookings/${id}`, data);
    },
    delete: async function(id) {
        return await ApiService.delete(`bookings/${id}`);
    }
};
