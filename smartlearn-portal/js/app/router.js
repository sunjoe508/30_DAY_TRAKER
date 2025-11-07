export class Router {
    constructor() {
        this.routes = {
            '/': 'dashboard',
            '/login': 'login',
            '/register': 'register',
            '/dashboard': 'dashboard',
            '/tests': 'tests',
            '/test/:id': 'test',
            '/results': 'results',
            '/admin': 'admin'
        };
        this.currentPath = window.location.pathname;
    }

    init() {
        // Handle navigation
        window.addEventListener('popstate', () => this.handleRoute());
        
        // Handle link clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-route]')) {
                e.preventDefault();
                this.navigate(e.target.getAttribute('href'));
            }
        });

        this.handleRoute();
    }

    navigate(path) {
        window.history.pushState({}, '', path);
        this.handleRoute();
    }

    async handleRoute() {
        this.currentPath = window.location.pathname;
        const app = document.getElementById('app');
        
        // Simple route matching (you can expand this with a proper router)
        if (this.currentPath === '/') {
            await this.loadDashboard();
        } else if (this.currentPath === '/tests') {
            await this.loadTests();
        }
        // Add more route handlers...
    }

    async loadDashboard() {
        const { StudentDashboard } = await import('../modules/student-dashboard.js');
        const dashboard = new StudentDashboard();
        document.getElementById('app').innerHTML = await dashboard.render();
        dashboard.init();
    }

    async loadTests() {
        const { TestEngine } = await import('../modules/test-engine.js');
        const testEngine = new TestEngine();
        document.getElementById('app').innerHTML = await testEngine.render();
        testEngine.init();
    }
}