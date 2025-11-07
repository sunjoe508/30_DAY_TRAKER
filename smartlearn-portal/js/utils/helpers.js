export class Helpers {
    static formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    static generateID(prefix = '') {
        return `${prefix}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    }

    static debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    static calculateScore(correct, total) {
        return total > 0 ? Math.round((correct / total) * 100) : 0;
    }

    static getDifficultyColor(difficulty) {
        const colors = {
            beginner: '#00ff88',
            intermediate: '#ffaa00',
            advanced: '#ff4444'
        };
        return colors[difficulty] || '#666666';
    }

    static sanitizeHTML(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
}