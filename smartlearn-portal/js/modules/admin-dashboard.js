import { API_SERVICE } from '../services/api-service.js';

export class AdminDashboard {
    constructor() {
        this.stats = {};
        this.recentActivity = [];
    }

    async init() {
        await this.loadDashboardStats();
        await this.loadRecentActivity();
        this.initCharts();
    }

    async loadDashboardStats() {
        const response = await API_SERVICE.get('/admin/dashboard-stats');
        if (response.success) {
            this.stats = response.data;
        }
    }

    async loadRecentActivity() {
        const response = await API_SERVICE.get('/admin/activity-logs');
        if (response.success) {
            this.recentActivity = response.data;
        }
    }

    initCharts() {
        // Initialize admin analytics charts
        this.renderUserGrowthChart();
        this.renderTestPerformanceChart();
    }

    renderUserGrowthChart() {
        // Chart.js implementation for user growth
    }

    renderTestPerformanceChart() {
        // Chart.js implementation for test performance
    }

    async render() {
        return `
            <div class="admin-dashboard">
                <div class="futuristic-bg"></div>
                <div class="dashboard-header">
                    <h1>Admin Dashboard</h1>
                </div>

                <div class="stats-overview">
                    <div class="glass-card">
                        <h3>Total Users</h3>
                        <div class="stat-value">${this.stats.totalUsers || 0}</div>
                    </div>
                    <div class="glass-card">
                        <h3>Total Tests</h3>
                        <div class="stat-value">${this.stats.totalTests || 0}</div>
                    </div>
                    <div class="glass-card">
                        <h3>Active Today</h3>
                        <div class="stat-value">${this.stats.activeToday || 0}</div>
                    </div>
                </div>

                <div class="admin-content">
                    <div class="glass-card">
                        <h3>Recent Activity</h3>
                        <div class="activity-list">
                            ${this.recentActivity.map(activity => `
                                <div class="activity-item">
                                    <span class="activity-time">${activity.time}</span>
                                    <span class="activity-desc">${activity.description}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}