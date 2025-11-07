import { API_SERVICE } from '../services/api-service.js';
import { Helpers } from '../utils/helpers.js';

export class StudentDashboard {
    constructor() {
        this.userProgress = null;
        this.recommendedTests = [];
    }

    async init() {
        await this.loadUserProgress();
        await this.loadRecommendedTests();
        this.initEventListeners();
        this.renderProgressCharts();
    }

    async loadUserProgress() {
        const response = await API_SERVICE.get('/users/progress');
        if (response.success) {
            this.userProgress = response.data;
        }
    }

    async loadRecommendedTests() {
        const response = await API_SERVICE.get('/tests/recommended');
        if (response.success) {
            this.recommendedTests = response.data;
        }
    }

    initEventListeners() {
        // Add dashboard interaction handlers
        document.addEventListener('click', (e) => {
            if (e.target.closest('.start-test-btn')) {
                this.startTest(e.target.dataset.testId);
            }
        });
    }

    renderProgressCharts() {
        // Implement circular progress charts
        const progressElements = document.querySelectorAll('.progress-ring');
        progressElements.forEach(element => {
            this.createProgressRing(element, element.dataset.progress);
        });
    }

    createProgressRing(element, progress) {
        const circle = element.querySelector('.progress-ring-circle');
        const radius = circle.r.baseVal.value;
        const circumference = radius * 2 * Math.PI;

        circle.style.strokeDasharray = `${circumference} ${circumference}`;
        circle.style.strokeDashoffset = circumference - (progress / 100) * circumference;
    }

    async render() {
        return `
            <div class="dashboard-container">
                <div class="futuristic-bg"></div>
                <div class="dashboard-header">
                    <h1>Python Learning Dashboard</h1>
                    <div class="user-welcome">
                        <span>Welcome back, Coder!</span>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <!-- Progress Overview -->
                    <div class="glass-card">
                        <h2>Learning Progress</h2>
                        <div class="progress-stats">
                            <div class="progress-item">
                                <span>Python Basics</span>
                                <div class="progress-ring" data-progress="75">
                                    <svg width="80" height="80">
                                        <circle class="progress-ring-circle" stroke-width="8" fill="transparent" r="36" cx="40" cy="40"/>
                                    </svg>
                                    <span>75%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recommended Tests -->
                    <div class="glass-card">
                        <h2>Recommended Tests</h2>
                        <div class="tests-grid">
                            ${this.recommendedTests.map(test => `
                                <div class="test-card" data-test-id="${test.id}">
                                    <h3>${test.title}</h3>
                                    <p>${test.description}</p>
                                    <button class="holo-btn start-test-btn" data-test-id="${test.id}">
                                        Start Test
                                    </button>
                                </div>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="glass-card">
                        <h2>Quick Stats</h2>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-value">12</span>
                                <span class="stat-label">Tests Completed</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value">85%</span>
                                <span class="stat-label">Average Score</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    startTest(testId) {
        window.app.router.navigate(`/test/${testId}`);
    }
}