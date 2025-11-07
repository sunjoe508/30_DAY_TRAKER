export class NotificationService {
    constructor() {
        this.notifications = [];
        this.permission = 'default';
    }

    async init() {
        // Request notification permission
        if ('Notification' in window) {
            this.permission = Notification.permission;
            
            if (this.permission === 'default') {
                this.permission = await Notification.requestPermission();
            }
        }
    }

    show(message, type = 'info', duration = 5000) {
        this.createToast(message, type, duration);
        
        // Also show browser notification if permitted
        if (this.permission === 'granted') {
            new Notification('SmartLearn Portal', {
                body: message,
                icon: '/assets/images/logo.png'
            });
        }
    }

    createToast(message, type, duration) {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <span class="toast-message">${message}</span>
                <button class="toast-close">&times;</button>
            </div>
        `;

        document.body.appendChild(toast);

        // Add show class after a frame
        setTimeout(() => toast.classList.add('show'), 10);

        // Auto remove
        const autoRemove = setTimeout(() => {
            this.removeToast(toast);
        }, duration);

        // Click to remove
        toast.querySelector('.toast-close').addEventListener('click', () => {
            clearTimeout(autoRemove);
            this.removeToast(toast);
        });
    }

    removeToast(toast) {
        toast.classList.remove('show');
        toast.addEventListener('transitionend', () => toast.remove());
    }

    // Specific notification types
    success(message) {
        this.show(message, 'success');
    }

    error(message) {
        this.show(message, 'error');
    }

    warning(message) {
        this.show(message, 'warning');
    }

    info(message) {
        this.show(message, 'info');
    }
}