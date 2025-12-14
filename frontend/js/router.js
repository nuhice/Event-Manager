const Router = {
    routes: {},
    currentPage: null,

    register: function (path, handler) {
        this.routes[path] = handler;
    },

    navigate: function (path) {
        if (this.routes[path]) {
            this.currentPage = path;
            this.routes[path]();
            window.history.pushState({}, '', '#' + path);
        } else {
            console.error('Route not found:', path);
        }
    },

    loadView: function (viewName, callback) {
        const timestamp = new Date().getTime();
        fetch('views/' + viewName + '.html?t=' + timestamp)
            .then(response => response.text())
            .then(html => {
                document.getElementById('app-content').innerHTML = html;
                if (callback) callback();
            })
            .catch(error => {
                console.error('Error loading view:', error);
                document.getElementById('app-content').innerHTML = '<div class="container"><h2>Error loading page</h2></div>';
            });
    },

    init: function () {
        window.addEventListener('popstate', () => {
            const path = window.location.hash.slice(1) || 'home';
            this.navigate(path);
        });

        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-page]')) {
                e.preventDefault();
                const page = e.target.getAttribute('data-page');
                this.navigate(page);
            }
        });

        const initialPage = window.location.hash.slice(1) || 'home';
        this.navigate(initialPage);
    }
};
