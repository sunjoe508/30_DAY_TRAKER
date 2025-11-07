import { CONSTANTS } from '../utils/constants.js';

class ApiService {
    constructor() {
        this.baseURL = CONSTANTS.API_BASE_URL;
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        };

        // Add auth token if available
        const token = this.getAuthToken();
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();
            
            return {
                success: response.ok,
                data: data.data || data,
                error: data.error || null,
                status: response.status
            };
        } catch (error) {
            return {
                success: false,
                error: 'Network error',
                status: 0
            };
        }
    }

    getAuthToken() {
        // Get token from session storage
        const session = JSON.parse(localStorage.getItem('smartlearn_session'));
        return session?.token || null;
    }

    async get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    }

    async post(endpoint, data) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async put(endpoint, data) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }
}

export const API_SERVICE = new ApiService();