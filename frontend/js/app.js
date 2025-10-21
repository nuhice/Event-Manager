const App = {
    user: null,
    isAuthenticated: false,

    init: function() {
        const userData = localStorage.getItem('user');
        if (userData) {
            this.user = JSON.parse(userData);
            this.isAuthenticated = true;
        }

        this.registerRoutes();

        Router.init();

        this.updateNavigation();

        setTimeout(() => {
            $('.loader').fadeOut();
            $('#preloder').delay(200).fadeOut('slow');
        }, 500);
    },

    registerRoutes: function() {
        Router.register('home', () => {
            Router.loadView('home', () => {
                this.initHomePage();
            });
        });

        Router.register('login', () => {
            if (this.isAuthenticated) {
                Router.navigate('dashboard');
                return;
            }
            Router.loadView('login', () => {
                this.initLoginPage();
            });
        });

        Router.register('register', () => {
            if (this.isAuthenticated) {
                Router.navigate('dashboard');
                return;
            }
            Router.loadView('register', () => {
                this.initRegisterPage();
            });
        });

        Router.register('dashboard', () => {
            if (!this.isAuthenticated) {
                Router.navigate('login');
                return;
            }
            if (!this.user || this.user.role !== 'admin') {
                Router.navigate('home');
                return;
            }
            Router.loadView('dashboard', () => {
                this.initDashboard();
            });
        });

        Router.register('events', () => {
            if (this.isAuthenticated && this.user.role === 'admin') {
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
            if (!this.isAuthenticated) {
                Router.navigate('login');
                return;
            }
            if (!this.user || this.user.role !== 'admin') {
                Router.navigate('home');
                return;
            }
            Router.loadView('event-form', () => {
                this.initEventForm('add');
            });
        });

        Router.register('event-edit', () => {
            if (!this.isAuthenticated) {
                Router.navigate('login');
                return;
            }
            if (!this.user || this.user.role !== 'admin') {
                Router.navigate('home');
                return;
            }
            Router.loadView('event-form', () => {
                this.initEventForm('edit');
            });
        });

        Router.register('venues', () => {
            if (!this.isAuthenticated) {
                Router.navigate('login');
                return;
            }
            if (!this.user || this.user.role !== 'admin') {
                Router.navigate('home');
                return;
            }
            Router.loadView('venues', () => {
                this.initVenuesPage();
            });
        });

        Router.register('my-bookings', () => {
            if (!this.isAuthenticated) {
                Router.navigate('login');
                return;
            }
            Router.loadView('my-bookings', () => {
                this.initMyBookingsPage();
            });
        });

        Router.register('bookings', () => {
            if (!this.isAuthenticated || this.user.role !== 'admin') {
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


    updateNavigation: function() {
        const nav = document.getElementById('main-nav');
        if (this.isAuthenticated) {
            if (this.user.role === 'admin') {
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

    login: function(email, password, role) {
        const user = {
            id: 1,
            name: email.split('@')[0],
            email: email,
            role: role
        };
        localStorage.setItem('user', JSON.stringify(user));
        this.user = user;
        this.isAuthenticated = true;
        this.updateNavigation();
        
        if (role === 'admin') {
            Router.navigate('dashboard');
        } else {
            Router.navigate('home');
        }
    },

    logout: function() {
        localStorage.removeItem('user');
        this.user = null;
        this.isAuthenticated = false;
        this.updateNavigation();
        Router.navigate('home');
    },

    initHomePage: function() {
        console.log('Home page loaded');
    },


    initLoginPage: function() {
        const form = document.getElementById('login-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const role = document.getElementById('role').value;
                this.login(email, password, role);
            });
        }
    },

    initRegisterPage: function() {
        const form = document.getElementById('register-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                alert('Registration successful! Please login.');
                Router.navigate('login');
            });
        }
    },

    initDashboard: function() {
        console.log('Dashboard loaded');
    },

    initEventsPage: function() {
        console.log('Events page loaded');
        
        document.querySelectorAll('.book-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                
                if (!this.isAuthenticated) {
                    alert('Please login to book an event!');
                    Router.navigate('login');
                } else {
                    alert('Booking confirmed! Check My Bookings page.');
                }
            });
        });
    },


    initEventForm: function(mode) {
        const form = document.getElementById('event-form');
        const title = document.querySelector('.form-container h2');
        
        if (mode === 'edit') {
            if (title) title.textContent = 'Edit Event';
            
            setTimeout(() => {
                document.getElementById('event-name').value = 'Tech Conference 2025';
                document.getElementById('event-description').value = 'Join us for the biggest tech conference of the year.';
                document.getElementById('event-date').value = '2025-11-15';
                document.getElementById('event-time').value = '09:00';
                document.getElementById('venue').value = '1';
                document.getElementById('category').value = '1';
                document.getElementById('speaker').value = '1';
                document.getElementById('capacity').value = '500';
                document.getElementById('price').value = '100.00';
                document.getElementById('status').value = 'active';
            }, 100);
        } else {
            if (title) title.textContent = 'Add New Event';
        }
        
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const action = mode === 'edit' ? 'updated' : 'created';
                alert(`Event ${action} successfully!`);
                Router.navigate('events');
            });
        }
    },

    initVenuesPage: function() {
        console.log('Venues page loaded');
    },

    initMyBookingsPage: function() {
        console.log('My Bookings page loaded');
    },

    initBookingsPage: function() {
        console.log('All Bookings page loaded (Admin only)');
    }
};

$(document).ready(function() {
    App.init();
});
