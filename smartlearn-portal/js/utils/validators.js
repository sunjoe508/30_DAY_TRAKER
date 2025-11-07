export class Validators {
    static validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    static validatePassword(password) {
        return password.length >= 8;
    }

    static validateName(name) {
        return name.trim().length >= 2;
    }

    static validateTestData(data) {
        const errors = [];
        
        if (!data.title || data.title.trim().length < 3) {
            errors.push('Test title must be at least 3 characters long');
        }
        
        if (!data.duration || data.duration < 1) {
            errors.push('Test duration must be at least 1 minute');
        }
        
        if (data.questions && data.questions.length === 0) {
            errors.push('Test must have at least one question');
        }
        
        return errors;
    }

    static validateQuestion(question) {
        const errors = [];
        
        if (!question.question || question.question.trim().length === 0) {
            errors.push('Question text is required');
        }
        
        if (question.type === 'multiple_choice' && (!question.options || question.options.length < 2)) {
            errors.push('Multiple choice questions must have at least 2 options');
        }
        
        if (!question.correct_answer && question.type !== 'code_challenge') {
            errors.push('Correct answer is required');
        }
        
        return errors;
    }

    static sanitizeHTML(html) {
        const div = document.createElement('div');
        div.textContent = html;
        return div.innerHTML;
    }

    static escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
}