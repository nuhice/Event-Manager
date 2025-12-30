const App = {
    init: function () {
        this.registerRoutes();
        Router.init();
        this.updateNavigation();

        setTimeout(() => {
            $('.loader').fadeOut();
            $('#preloder').delay(200).fadeOut('slow');
        }, 500);
    },

    registerRoutes: function () {
        Router.register('home', () => {
            Router.loadView('home', () => {
                this.initHomePage();
            });
        });

        Router.register('login', () => {
            if (AuthService.isAuthenticated()) {
                Router.navigate('dashboard');
                return;
            }
            Router.loadView('login', () => {
                this.initLoginPage();
            });
        });

        Router.register('register', () => {
            if (AuthService.isAuthenticated()) {
                Router.navigate('dashboard');
                return;
            }
            Router.loadView('register', () => {
                this.initRegisterPage();
            });
        });

        Router.register('dashboard', () => {
            if (!AuthService.isAuthenticated()) {
                Router.navigate('login');
                return;
            }
            if (!AuthService.isAdmin()) {
                Router.navigate('home');
                return;
            }
            Router.loadView('dashboard', () => {
                this.initDashboard();
            });
        });

        Router.register('events', () => {
            if (AuthService.isAdmin()) {
                Router.loadView('events', () => {
                    this.initEventsPage();
                });
            } else {
                Router.loadView('events-attendee', () => {
                    this.initEventsPage();
                });
            }
        });

        Router.register('event-add', () => {
            if (!AuthService.isAdmin()) {
                Router.navigate('home');
                return;
            }
            Router.loadView('event-form', () => {
                this.initEventForm('add');
            });
        });

        Router.register('event-edit', () => {
            if (!AuthService.isAdmin()) {
                Router.navigate('home');
                return;
            }
            Router.loadView('event-form', () => {
                this.initEventForm('edit');
            });
        });

        Router.register('venues', () => {
            if (!AuthService.isAdmin()) {
                Router.navigate('home');
                return;
            }
            Router.loadView('venues', () => {
                this.initVenuesPage();
            });
        });

        Router.register('my-bookings', () => {
            if (!AuthService.isAuthenticated()) {
                Router.navigate('login');
                return;
            }
            Router.loadView('my-bookings', () => {
                this.initMyBookingsPage();
            });
        });

        Router.register('bookings', () => {
            if (!AuthService.isAdmin()) {
                Router.navigate('home');
                return;
            }
            Router.loadView('bookings', () => {
                this.initBookingsPage();
            });
        });

        Router.register('contact', () => {
            Router.loadView('contact');
        });
    },


    updateNavigation: function () {
        const nav = document.getElementById('main-nav');
        if (AuthService.isAuthenticated()) {
            if (AuthService.isAdmin()) {
                nav.innerHTML = `
                    <li><a href="#" data-page="dashboard">Dashboard</a></li>
                    <li><a href="#" data-page="events">Manage Events</a></li>
                    <li><a href="#" data-page="venues">Manage Venues</a></li>
                    <li><a href="#" data-page="bookings">All Bookings</a></li>
                    <li><a href="#" data-page="contact">Contact</a></li>
                    <li><a href="#" onclick="App.logout()">Logout</a></li>
                `;
            } else {
                nav.innerHTML = `
                    <li><a href="#" data-page="home">Home</a></li>
                    <li><a href="#" data-page="events">Events</a></li>
                    <li><a href="#" data-page="my-bookings">My Bookings</a></li>
                    <li><a href="#" data-page="contact">Contact</a></li>
                    <li><a href="#" onclick="App.logout()">Logout</a></li>
                `;
            }
        } else {
            nav.innerHTML = `
                <li><a href="#" data-page="home">Home</a></li>
                <li><a href="#" data-page="events">Events</a></li>
                <li><a href="#" data-page="contact">Contact</a></li>
                <li><a href="#" data-page="login">Login</a></li>
                <li><a href="#" data-page="register">Register</a></li>
            `;
        }

    },

    logout: function () {
        AuthService.logout();
        this.updateNavigation();
        Router.navigate('home');
    },

    initHomePage: function () {
        console.log('Home page loaded');
    },


    initLoginPage: function () {
        const form = document.getElementById('login-form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email || !emailRegex.test(email)) {
                    alert('Please enter a valid email');
                const eventsTable = document.querySelector('.col-lg-6:first-child tbody');
                const bookingsTable = document.querySelector('.col-lg-6:last-child tbody');
                }
                if (!password) {
                    alert('Please enter your password');
                    return;
                }

                try {
                    await AuthService.login(email, password);
                    this.updateNavigation();
                    if (AuthService.isAdmin()) {
                        Router.navigate('dashboard');
                    } else {
                        Router.navigate('home');
                    }
                } catch (error) {
                    alert(error.message);
                }
            });
        }
    },

    initRegisterPage: function () {
        const form = document.getElementById('register-form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm-password').value;

                if (password !== confirmPassword) {
                    alert('Passwords do not match');
                    return;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!name || !emailRegex.test(email)) {
                    alert('Please enter a valid name and email');
                    return;
                }
                if (password.length < 6) {
                    alert('Password must be at least 6 characters');
                    return;
                }

                try {
                    await AuthService.register(name, email, password);
                    alert('Registration successful! Please login.');
                    Router.navigate('login');
                } catch (error) {
                    alert(error.message);
                }
            });
        }
    },

    initDashboard: async function () {
        console.log('Dashboard loaded');

        try {
            const events = await EventService.getAll();
            const locations = await LocationService.getAll();
            const bookings = await BookingService.getAll();

            const statsCards = document.querySelectorAll('.stats-card h3');
            if (statsCards.length >= 3) {
                statsCards[0].textContent = events.length || 0;
                statsCards[1].textContent = locations.length || 0;
                statsCards[2].textContent = bookings.length || 0;
            }

            const eventsTable = document.querySelector('.col-lg-6:first-child tbody');
            if (eventsTable && events) {
                eventsTable.innerHTML = '';
                events.slice(0, 5).forEach(event => {
                    eventsTable.innerHTML += `
                        <tr>
                            <td>${event.title}</td>
                            <td>${new Date(event.start_date).toLocaleDateString()}</td>
                            <td><span style="color: #28a745;">Active</span></td>
                        </tr>
                    `;
                });
            }

            const bookingsTable = document.querySelector('.col-lg-6:last-child tbody');
            if (bookingsTable && bookings) {
                bookingsTable.innerHTML = '';
                bookings.slice(0, 5).forEach(booking => {
                    bookingsTable.innerHTML += `
                        <tr>
                            <td>${booking.user_name}</td>
                            <td>${booking.event_title}</td>
                            <td>1</td>
                        </tr>
                    `;
                });
            }

        } catch (error) {
            console.error('Error loading dashboard stats:', error);
        }
    },

    initEventsPage: async function () {
        console.log('Events page loaded');
        const container = document.querySelector('.events-grid');
        if (!container) return;
        container.innerHTML = '<div class="col-12 text-center"><div class="loader" style="display:inline-block; position:relative;"></div></div>';

        try {
            const events = await EventService.getAll();
            container.innerHTML = '';

            if (!events || events.length === 0) {
                container.innerHTML = '<div class="col-12"><p>No events found.</p></div>';
                return;
            }

            events.forEach(event => {
                const card = this.createEventCard(event);
                container.appendChild(card);
            });
        } catch (error) {
            container.innerHTML = `<div class="col-12"><p class="text-danger">Error loading events: ${error.message}</p></div>`;
        }
    },

    createEventCard: function (event) {
        const div = document.createElement('div');
        div.className = 'event-card';

        const isAdmin = AuthService.isAdmin();
        const date = new Date(event.start_date).toLocaleDateString();

        let actionButtons = '';
        if (isAdmin) {
            actionButtons = `
                <div style="margin-top: 15px;">
                    <a href="#" data-id="${event.event_id}" class="btn-edit">Edit</a>
                    <a href="#" data-id="${event.event_id}" class="btn-delete">Delete</a>
                </div>
            `;
        } else {
            actionButtons = `
                <div style="margin-top: 15px;">
                    <a href="#" data-id="${event.event_id}" class="btn-primary book-btn" style="width: auto; padding: 10px 20px;">Book Now</a>
                </div>
            `;
        }

        div.innerHTML = `
            <div class="event-card-body">
                <h4>${event.title}</h4>
                <p class="event-date"><i class="fa fa-calendar"></i> ${date}</p>
                <p><i class="fa fa-map-marker"></i> Location ID: ${event.location_id}</p>
                <p><i class="fa fa-users"></i> Capacity: ${event.capacity}</p>
                <p style="margin-top: 10px;">${event.description || ''}</p>
                ${actionButtons}
            </div>
        `;

        
        if (isAdmin) {
            div.querySelector('.btn-edit').addEventListener('click', (e) => {
                e.preventDefault();
                alert('Edit feature coming soon');
            });
            div.querySelector('.btn-delete').addEventListener('click', async (e) => {
                e.preventDefault();
                if (confirm('Delete this event?')) {
                    try {
                        await EventService.delete(event.event_id);
                        this.initEventsPage();
                    } catch (err) {
                        alert(err.message);
                    }
                }
            });
        } else {
            div.querySelector('.book-btn').addEventListener('click', async (e) => {
                e.preventDefault();
                if (!AuthService.isAuthenticated()) {
                    alert('Please login to book an event!');
                    Router.navigate('login');
                    return;
                }

                    try {
                        await BookingService.create({
                            event_id: event.event_id,
                            status: 'pending'
                        });
                    alert('Booking confirmed!');
                } catch (err) {
                    alert(err.message);
                }
            });
        }

        return div;
    },


    initEventForm: async function (mode) {
        const form = document.getElementById('event-form');
        const title = document.querySelector('.form-container h2');
        const venueSelect = document.getElementById('venue');

        await this.loadVenuesIntoSelect();

        if (mode === 'edit') {
            if (title) title.textContent = 'Edit Event';
            setTimeout(() => {
                document.getElementById('event-name').value = 'Tech Conference 2025';
                document.getElementById('event-description').value = 'Join us for the biggest tech conference of the year.';
                document.getElementById('event-date').value = '2025-11-15';
                document.getElementById('event-time').value = '09:00';
                document.getElementById('venue').value = '1';
                document.getElementById('capacity').value = '500';
            }, 100);
        } else {
            if (title) title.textContent = 'Add New Event';
        }

        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const title = document.getElementById('event-name').value;
                const description = document.getElementById('event-description').value;
                const date = document.getElementById('event-date').value;
                const time = document.getElementById('event-time').value;
                const location_id = document.getElementById('venue').value;
                const capacity = document.getElementById('capacity').value;

                const start_date = `${date} ${time}:00`;

                const data = {
                    title,
                    description,
                    start_date,
                    location_id,
                    capacity
                };

                    try {
                    if (mode === 'edit') {
                        alert('Edit not implemented yet');
                    } else {
                        if (!title || !date || !time || !location_id || !capacity) {
                            alert('Please fill all required event fields');
                            return;
                        }
                        await EventService.create(data);
                        alert('Event created successfully!');
                        Router.navigate('events');
                    }
                } catch (error) {
                    alert(error.message);
                }
            });
        }
    },

    loadVenuesIntoSelect: async function () {
        const venueSelect = document.getElementById('venue');
        if (!venueSelect) {
            console.error('Venue select not found!');
            return;
        }

            try {
            console.log('Loading venues into select...');
            const venues = await LocationService.getAll();

            venueSelect.innerHTML = '<option value="">Select Venue</option>';

            if (!venues || venues.length === 0) {
                venueSelect.innerHTML += '<option value="" disabled>No venues available</option>';
                console.log('No venues found');
                return;
            }

            venues.forEach(venue => {
                const option = document.createElement('option');
                option.value = venue.location_id;
                option.textContent = `${venue.name} - ${venue.city}`;
                venueSelect.appendChild(option);
            });

            console.log(`Loaded ${venues.length} venues into select`);
        } catch (error) {
            console.error('Error loading venues:', error);
            venueSelect.innerHTML += '<option value="" disabled>Error loading venues</option>';
        }
    },

    initVenuesPage: async function () {
        console.log('=== Venues page loaded ===');
        const tbody = document.querySelector('.data-table tbody');
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';

        try {
            const locations = await LocationService.getAll();

            window.venueLocations = locations;

            tbody.innerHTML = '';

            if (!locations || locations.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No venues found.</td></tr>';
                return;
            }

            locations.forEach((location, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${location.location_id}</td>
                    <td>${location.name}</td>
                    <td>${location.address}, ${location.city}</td>
                    <td>${location.capacity}</td>
                    <td><span style="color: #28a745;">Available</span></td>
                    <td>
                        <a href="#" class="btn-edit" onclick="App.editVenue(${index}); return false;">Edit</a>
                        <a href="#" class="btn-delete" onclick="App.deleteVenue(${location.location_id}); return false;">Delete</a>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            setTimeout(() => {
                const addBtn = document.getElementById('add-venue-btn');

                if (addBtn) {
                    addBtn.addEventListener('click', () => {
                        this.openVenueModal('add');
                    });
                }

                const modal = document.getElementById('venue-modal');
                if (!modal) {
                    console.error('ERROR: venue-modal NOT FOUND in DOM!');
                }

                this.setupVenueModalHandlers();
            }, 100);

        } catch (error) {
            console.error('Error in initVenuesPage:', error);
            tbody.innerHTML = `<tr><td colspan="6" class="text-danger">Error loading venues: ${error.message}</td></tr>`;
        }
    },

    openBookingModal: async function () {
        const modal = document.getElementById('booking-modal');
        const userSelect = document.getElementById('booking-user');
        const eventSelect = document.getElementById('booking-event');

        const events = await EventService.getAll();
        const users = await UserService.getAll();

        eventSelect.innerHTML = '<option value="">Select Event</option>';
        events.forEach(ev => {
            const opt = document.createElement('option');
            opt.value = ev.event_id;
            opt.textContent = `${ev.title} (${new Date(ev.start_date).toLocaleDateString()})`;
            eventSelect.appendChild(opt);
        });

        userSelect.innerHTML = '';
        users.forEach(u => {
            const opt = document.createElement('option');
            opt.value = u.user_id;
            opt.textContent = `${u.name} <${u.email}>`;
            userSelect.appendChild(opt);
        });

        modal.style.display = 'flex';
    },

    handleBookingSubmit: async function (e) {
        e.preventDefault();
        const userId = document.getElementById('booking-user').value;
        const eventId = document.getElementById('booking-event').value;
        const tickets = parseInt(document.getElementById('booking-tickets').value) || 1;
        const status = document.getElementById('booking-status').value || 'pending';

        if (!eventId) {
            alert('Please select an event');
            return false;
        }

        try {
            const payload = {
                event_id: parseInt(eventId),
                tickets: tickets,
                status: status
            };
            if (AuthService.isAdmin() && userId) payload.user_id = parseInt(userId);

            await BookingService.create(payload);
            alert('Booking created successfully');
            document.getElementById('booking-modal').style.display = 'none';
            document.getElementById('booking-form').reset();
            await this.initBookingsPage();
        } catch (err) {
            alert(err.message);
        }
        return false;
    },

    editVenue: function (index) {
        const location = window.venueLocations[index];
        this.openVenueModal('edit', location);
    },

    deleteVenue: async function (locationId) {
        if (confirm('Delete this venue?')) {
            try {
                await LocationService.delete(locationId);
                this.initVenuesPage();
            } catch (err) {
                alert(err.message);
            }
        }
    },

    handleVenueSubmit: async function (event) {
        event.preventDefault();
        console.log('handleVenueSubmit called');

        const modal = document.getElementById('venue-modal');
        const form = document.getElementById('venue-form');
        const venueId = document.getElementById('venue-id').value;
        const name = document.getElementById('venue-name').value;
        const address = document.getElementById('venue-address').value;
        const city = document.getElementById('venue-city').value;
        const capacity = document.getElementById('venue-capacity').value;

        const data = {
            name,
            address,
            city,
            capacity: parseInt(capacity)
        };

        console.log('Submitting venue data:', data);

            try {
                if (venueId) {
                console.log('Updating venue ID:', venueId);
                await LocationService.update(venueId, data);
                alert('Venue updated successfully!');
            } else {
                console.log('Creating new venue');
                const response = await LocationService.create(data);
                console.log('Create response:', response);
                alert('Venue added successfully!');
            }

            modal.style.display = 'none';
            form.reset();

            console.log('Calling initVenuesPage to reload...');
            await this.initVenuesPage();
            console.log('Venues page reloaded');
        } catch (error) {
            console.error('Error in handleVenueSubmit:', error);
            alert('Error: ' + error.message);
        }

        return false;
    },

    openVenueModal: function (mode, venue = null) {
        try { this.setupVenueModalHandlers(); } catch (e) { }
        const modal = document.getElementById('venue-modal');
        console.log('Modal element:', modal);
        const modalTitle = document.getElementById('modal-title');
        const form = document.getElementById('venue-form');

        if (!modal) {
            console.error('Modal not found in DOM!');
            alert('Error: Modal form not found. Please refresh the page.');
            return;
        }

        if (mode === 'edit' && venue) {
            modalTitle.textContent = 'Edit Venue';
            document.getElementById('venue-id').value = venue.location_id;
            document.getElementById('venue-name').value = venue.name;
            document.getElementById('venue-address').value = venue.address;
            document.getElementById('venue-city').value = venue.city;
            document.getElementById('venue-capacity').value = venue.capacity;
        } else {
            modalTitle.textContent = 'Add New Venue';
            form.reset();
            document.getElementById('venue-id').value = '';
        }

        console.log('Setting modal display to flex');
        modal.style.display = 'flex';
    },

    setupVenueModalHandlers: function () {
        if (this._venueHandlersSetup) return;
        this._venueHandlersSetup = true;
        const modal = document.getElementById('venue-modal');
        const form = document.getElementById('venue-form');
        const cancelBtn = document.getElementById('cancel-btn');
        const self = this;

        if (!modal || !form) {
            this._venueHandlersSetup = false;
            return;
        }

            if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                modal.style.display = 'none';
                form.reset();
            });
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
                form.reset();
            }
        });

        if (typeof this.handleVenueSubmit === 'function') {
            form.addEventListener('submit', (e) => this.handleVenueSubmit(e));
        } else {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const venueId = document.getElementById('venue-id').value;
                const name = document.getElementById('venue-name').value;
                const address = document.getElementById('venue-address').value;
                const city = document.getElementById('venue-city').value;
                const capacity = document.getElementById('venue-capacity').value;
                const data = { name, address, city, capacity: parseInt(capacity) };
                try {
                    if (!form.checkValidity()) { form.reportValidity(); return; }
                    if (venueId) await LocationService.update(venueId, data);
                    else await LocationService.create(data);
                    modal.style.display = 'none'; form.reset(); await self.initVenuesPage();
                } catch (err) { alert('Error: ' + err.message); }
            });
        }
    },


    initMyBookingsPage: async function () {
        console.log('My Bookings page loaded');
        const container = document.querySelector('.row')
        if (!container) return;
        container.innerHTML = '<div class="col-12 text-center"><div class="loader" style="display:inline-block; position:relative;"></div></div>';

        try {
            const bookings = await BookingService.getAll();
            container.innerHTML = '';

            if (!bookings || bookings.length === 0) {
                container.innerHTML = '<div class="col-12"><p>No bookings found.</p></div>';
                return;
            }

            bookings.forEach(booking => {
                const date = new Date(booking.start_date).toLocaleDateString();
                const div = document.createElement('div');
                div.className = 'col-lg-4 col-md-6';
                div.style.marginBottom = '30px';
                div.innerHTML = `
                    <div class="event-card">
                        <div class="event-card-body">
                            <h4>${booking.event_title}</h4>
                            <p class="event-date"><i class="fa fa-calendar"></i> ${date}</p>
                            <p><i class="fa fa-map-marker"></i> Location ID: ${booking.location_id}</p>
                            <p style="margin-top: 10px;"><span style="color: ${booking.status === 'confirmed' ? '#28a745' : '#ffc107'}; font-weight: 600;">${booking.status}</span></p>
                            <div style="margin-top: 15px;">
                                <a href="#" class="btn-delete" data-id="${booking.booking_id}">Cancel Booking</a>
                            </div>
                        </div>
                    </div>
                `;

                div.querySelector('.btn-delete').addEventListener('click', async (e) => {
                    e.preventDefault();
                    if (confirm('Cancel this booking?')) {
                        try {
                            await BookingService.delete(booking.booking_id);
                            this.initMyBookingsPage();
                        } catch (err) {
                            alert(err.message);
                        }
                    }
                });

                container.appendChild(div);
            });
        } catch (error) {
            container.innerHTML = `<div class="col-12"><p class="text-danger">Error loading bookings: ${error.message}</p></div>`;
        }
    },

    initBookingsPage: async function () {
        console.log('All Bookings page loaded (Admin only)');
        const tbody = document.querySelector('.data-table tbody');
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">Loading...</td></tr>';

        try {
            const bookings = await BookingService.getAll();
            tbody.innerHTML = '';

            if (!bookings || bookings.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">No bookings found.</td></tr>';
                return;
            }

            bookings.forEach(booking => {
                const date = new Date(booking.booking_date).toLocaleDateString();
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>#${booking.booking_id}</td>
                    <td>${booking.user_name}</td>
                    <td>${booking.user_email}</td>
                    <td>${booking.event_title}</td>
                    <td>1</td>
                    <td>-</td>
                    <td>${date}</td>
                    <td><span style="color: ${booking.status === 'confirmed' ? '#28a745' : '#ffc107'};">${booking.status}</span></td>
                    <td>
                        <a href="#" class="btn-delete" data-id="${booking.booking_id}">Cancel</a>
                    </td>
                `;

                tr.querySelector('.btn-delete').addEventListener('click', async (e) => {
                    e.preventDefault();
                    if (confirm('Cancel this booking?')) {
                        try {
                            await BookingService.delete(booking.booking_id);
                            this.initBookingsPage();
                        } catch (err) {
                            alert(err.message);
                        }
                    }
                });

                tbody.appendChild(tr);
            });
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-danger">Error loading bookings: ${error.message}</td></tr>`;
        }

        const addBtn = document.getElementById('add-booking-btn');
        if (addBtn) {
            if (AuthService.isAdmin()) {
                addBtn.style.display = 'inline-block';
                addBtn.removeEventListener && addBtn.removeEventListener('click', this._addBookingHandler);
                const handler = () => this.openBookingModal();
                addBtn.addEventListener('click', handler);
                this._addBookingHandler = handler;
            } else {
                addBtn.style.display = 'none';
            }
        }
    }
};

$(document).ready(function () {
    App.init();
});
