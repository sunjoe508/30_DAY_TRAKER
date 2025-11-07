import { API_SERVICE } from '../services/api-service.js';

export class ResultsManager {
    constructor() {
        this.results = [];
        this.analytics = {};
    }

    async init() {
        await this.loadUserResults();
        await this.loadAnalytics();
        this.renderResults();
        this.renderAnalytics();
    }

    async loadUserResults() {
        const response = await API_SERVICE.get('/results/get-student-results');
        if (response.success) {
            this.results = response.data;
        }
    }

    async loadAnalytics() {
        const response = await API_SERVICE.get('/results/get-analytics');
        if (response.success) {
            this.analytics = response.data;
        }
    }

    renderResults() {
        const resultsContainer = document.getElementById('results-container');
        if (resultsContainer) {
            resultsContainer.innerHTML = this.results.map(result => `
                <div class="result-card glass-card">
                    <h3>${result.test_title}</h3>
                    <div class="result-stats">
                        <span class="score">Score: ${result.score}%</span>
                        <span class="time">Time: ${result.time_spent}</span>
                        <span class="date">Date: ${new Date(result.completed_at).toLocaleDateString()}</span>
                    </div>
                    <button class="holo-btn view-details-btn" data-result-id="${result.id}">
                        View Details
                    </button>
                </div>
            `).join('');
        }
    }

    renderAnalytics() {
        const analyticsContainer = document.getElementById('analytics-container');
        if (analyticsContainer && this.analytics) {
            analyticsContainer.innerHTML = `
                <div class="analytics-card glass-card">
                    <h3>Learning Analytics</h3>
                    <div class="analytics-stats">
                        <div class="analytics-item">
                            <span>Average Score</span>
                            <span class="value">${this.analytics.averageScore || 0}%</span>
                        </div>
                        <div class="analytics-item">
                            <span>Tests Completed</span>
                            <span class="value">${this.analytics.testsCompleted || 0}</span>
                        </div>
                        <div class="analytics-item">
                            <span>Strongest Topic</span>
                            <span class="value">${this.analytics.strongestTopic || 'N/A'}</span>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    async render() {
        return `
            <div class="results-manager">
                <div class="futuristic-bg"></div>
                <div class="results-header">
                    <h1>Test Results & Analytics</h1>
                </div>

                <div class="results-content">
                    <div id="analytics-container"></div>
                    <div class="results-list">
                        <h2>Recent Test Results</h2>
                        <div id="results-container"></div>
                    </div>
                </div>
            </div>
        `;
    }
}