import { API_SERVICE } from '../services/api-service.js';
import { Helpers } from '../utils/helpers.js';

export class TestEngine {
    constructor() {
        this.currentTest = null;
        this.currentQuestionIndex = 0;
        this.userAnswers = [];
        this.timer = null;
        this.timeRemaining = 0;
    }

    async init() {
        const testId = this.getTestIdFromURL();
        if (testId) {
            await this.loadTest(testId);
            this.startTimer();
            this.renderCurrentQuestion();
        }
    }

    getTestIdFromURL() {
        const path = window.location.pathname;
        const match = path.match(/\/test\/(.+)/);
        return match ? match[1] : null;
    }

    async loadTest(testId) {
        const response = await API_SERVICE.get(`/tests/get-test/${testId}`);
        if (response.success) {
            this.currentTest = response.data;
            this.timeRemaining = this.currentTest.duration * 60; // Convert to seconds
        }
    }

    startTimer() {
        this.timer = setInterval(() => {
            this.timeRemaining--;
            this.updateTimerDisplay();
            
            if (this.timeRemaining <= 0) {
                this.submitTest();
            }
        }, 1000);
    }

    updateTimerDisplay() {
        const timerElement = document.getElementById('test-timer');
        if (timerElement) {
            timerElement.textContent = Helpers.formatTime(this.timeRemaining);
        }
    }

    renderCurrentQuestion() {
        if (!this.currentTest || !this.currentTest.questions) return;

        const question = this.currentTest.questions[this.currentQuestionIndex];
        const questionElement = document.getElementById('question-container');
        
        if (questionElement) {
            questionElement.innerHTML = this.renderQuestion(question);
        }
    }

    renderQuestion(question) {
        return `
            <div class="question-card glass-card">
                <div class="question-header">
                    <span class="question-number">Question ${this.currentQuestionIndex + 1}</span>
                    <span class="question-difficulty ${question.difficulty}">${question.difficulty}</span>
                </div>
                
                <div class="question-content">
                    <p>${question.question}</p>
                    ${question.code_snippet ? `
                        <pre class="code-snippet"><code>${question.code_snippet}</code></pre>
                    ` : ''}
                </div>

                <div class="options-container">
                    ${question.options ? question.options.map((option, index) => `
                        <div class="option-item">
                            <input type="radio" 
                                   name="answer" 
                                   value="${index}" 
                                   id="option-${index}"
                                   onchange="window.testEngine.selectAnswer(${index})">
                            <label for="option-${index}">${option}</label>
                        </div>
                    `).join('') : ''}

                    ${question.type === 'code_challenge' ? `
                        <div class="code-editor-container">
                            <textarea class="code-editor" placeholder="Write your Python code here..."></textarea>
                        </div>
                    ` : ''}
                </div>

                <div class="question-navigation">
                    ${this.currentQuestionIndex > 0 ? `
                        <button class="holo-btn" onclick="window.testEngine.previousQuestion()">Previous</button>
                    ` : ''}
                    
                    ${this.currentQuestionIndex < this.currentTest.questions.length - 1 ? `
                        <button class="holo-btn" onclick="window.testEngine.nextQuestion()">Next</button>
                    ` : `
                        <button class="holo-btn submit-btn" onclick="window.testEngine.submitTest()">Submit Test</button>
                    `}
                </div>
            </div>
        `;
    }

    selectAnswer(answerIndex) {
        this.userAnswers[this.currentQuestionIndex] = answerIndex;
    }

    nextQuestion() {
        if (this.currentQuestionIndex < this.currentTest.questions.length - 1) {
            this.currentQuestionIndex++;
            this.renderCurrentQuestion();
        }
    }

    previousQuestion() {
        if (this.currentQuestionIndex > 0) {
            this.currentQuestionIndex--;
            this.renderCurrentQuestion();
        }
    }

    async submitTest() {
        clearInterval(this.timer);
        
        const result = {
            testId: this.currentTest.id,
            answers: this.userAnswers,
            timeSpent: this.currentTest.duration * 60 - this.timeRemaining
        };

        const response = await API_SERVICE.post('/results/submit-result', result);
        if (response.success) {
            window.app.router.navigate('/results');
        }
    }

    async render() {
        return `
            <div class="test-engine">
                <div class="futuristic-bg"></div>
                <div class="test-header">
                    <h1>Python Test</h1>
                    <div class="test-timer" id="test-timer">00:00</div>
                </div>

                <div class="test-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${((this.currentQuestionIndex + 1) / (this.currentTest?.questions.length || 1)) * 100}%"></div>
                    </div>
                    <span>${this.currentQuestionIndex + 1} / ${this.currentTest?.questions.length || 0}</span>
                </div>

                <div id="question-container"></div>
            </div>
        `;
    }
}

// Make available globally for event handlers
window.testEngine = new TestEngine();