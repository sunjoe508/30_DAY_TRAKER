import { Router } from './router.js';
import { AuthManager } from '../auth/auth-manager.js';
import { NotificationService } from '../services/notification-service.js';

class SmartLearnApp {
    constructor() {
        this.router = new Router();
        this.auth = new AuthManager();
        this.notifications = new NotificationService();
        this.init();
    }

    async init() {
        try {
            // Initialize services
            await this.auth.init();
            await this.notifications.init();
            
            // Start router
            this.router.init();
            
            // Hide loading screen
            this.hideLoading();
            
            console.log('🚀 SmartLearn Portal initialized successfully');
        } catch (error) {
            console.error('Failed to initialize app:', error);
            this.showError('Failed to initialize application');
        }
    }

    hideLoading() {
        const loading = document.getElementById('loading');
        if (loading) {
            loading.style.opacity = '0';
            setTimeout(() => loading.remove(), 500);
        }
    }

    showError(message) {
        // Implement error notification
        console.error('App Error:', message);
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.app = new SmartLearnApp();
});