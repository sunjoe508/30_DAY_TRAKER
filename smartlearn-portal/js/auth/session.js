import { STORAGE_SERVICE } from '../services/storage-service.js';

export class Session {
    constructor() {
        this.SESSION_KEY = 'smartlearn_session';
        this.user = null;
        this.token = null;
    }

    async init() {
        const sessionData = STORAGE_SERVICE.get(this.SESSION_KEY);
        if (sessionData) {
            this.user = sessionData.user;
            this.token = sessionData.token;
        }
    }

    create(sessionData) {
        this.user = sessionData.user;
        this.token = sessionData.token;
        
        STORAGE_SERVICE.set(this.SESSION_KEY, {
            user: this.user,
            token: this.token,
            createdAt: Date.now()
        });
    }

    destroy() {
        this.user = null;
        this.token = null;
        STORAGE_SERVICE.remove(this.SESSION_KEY);
    }

    isValid() {
        if (!this.token || !this.user) return false;
        
        const sessionData = STORAGE_SERVICE.get(this.SESSION_KEY);
        if (!sessionData) return false;

        // Check if session is expired (24 hours)
        const sessionAge = Date.now() - sessionData.createdAt;
        return sessionAge < 24 * 60 * 60 * 1000;
    }

    getUser() {
        return this.user;
    }

    getToken() {
        return this.token;
    }
}