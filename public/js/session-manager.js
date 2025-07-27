// Session Management for Real Estate App
class SessionManager {
    constructor() {
        this.checkInterval = 300000; // Check every 5 minutes
        this.warningTime = 300; // Show warning 5 minutes before expiry
        this.sessionLifetime = 7200; // 2 hours default Laravel session
        this.lastActivity = Date.now();
        this.warningShown = false;

        this.init();
    }

    init() {
        // Start session monitoring
        this.startSessionCheck();

        // Track user activity
        this.trackActivity();

        // Handle page visibility changes
        this.handleVisibilityChange();
    }

    startSessionCheck() {
        setInterval(() => {
            this.checkSession();
        }, this.checkInterval);
    }

    trackActivity() {
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];

        events.forEach(event => {
            document.addEventListener(event, () => {
                this.lastActivity = Date.now();
                this.warningShown = false;
            }, true);
        });
    }

    handleVisibilityChange() {
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                // Page became visible, check session status
                this.checkSession();
            }
        });
    }

    async checkSession() {
        try {
            const response = await fetch('/check-session', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            });

            if (response.status === 401) {
                this.handleSessionExpired();
                return;
            }

            if (!response.ok) {
                console.warn('Session check failed:', response.status);
                return;
            }

            const data = await response.json();

            if (!data.authenticated) {
                this.handleSessionExpired();
            } else if (data.timeRemaining && data.timeRemaining < this.warningTime && !this.warningShown) {
                this.showSessionWarning(data.timeRemaining);
            }

        } catch (error) {
            console.error('Session check error:', error);
        }
    }

    handleSessionExpired() {
        // Clear any client-side data
        localStorage.clear();
        sessionStorage.clear();

        // Show expiry message
        this.showExpiredMessage();

        // Redirect to login after delay
        setTimeout(() => {
            window.location.href = '/login';
        }, 3000);
    }

    showSessionWarning(timeRemaining) {
        this.warningShown = true;

        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;

        const warning = document.createElement('div');
        warning.className = 'fixed top-4 right-4 bg-yellow-500 text-white p-4 rounded-lg shadow-lg z-50';
        warning.innerHTML = `
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <div class="font-bold">Session Warning</div>
                    <div class="text-sm">Your session will expire in ${minutes}:${seconds.toString().padStart(2, '0')}</div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(warning);

        // Auto remove after 10 seconds
        setTimeout(() => {
            if (warning.parentNode) {
                warning.remove();
            }
        }, 10000);
    }

    showExpiredMessage() {
        // Remove existing warnings
        document.querySelectorAll('.session-warning, .session-expired').forEach(el => el.remove());

        const expiredDiv = document.createElement('div');
        expiredDiv.className = 'session-expired fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        expiredDiv.innerHTML = `
            <div class="bg-white p-8 rounded-lg shadow-xl max-w-md mx-4 text-center">
                <div class="text-red-500 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Session Expired</h3>
                <p class="text-gray-600 mb-4">Your session has expired. You will be redirected to the login page.</p>
                <div class="text-sm text-gray-500">Redirecting in 3 seconds...</div>
            </div>
        `;

        document.body.appendChild(expiredDiv);
    }

    // Method to extend session
    async extendSession() {
        try {
            const response = await fetch('/extend-session', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                this.lastActivity = Date.now();
                this.warningShown = false;
                return true;
            }

            return false;
        } catch (error) {
            console.error('Session extend error:', error);
            return false;
        }
    }
}

// Initialize session manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize for authenticated pages
    if (document.querySelector('meta[name="user-authenticated"]')) {
        window.sessionManager = new SessionManager();
    }
});

// Handle AJAX session expired responses
document.addEventListener('DOMContentLoaded', () => {
    // Intercept fetch requests to handle 401 responses
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args)
            .then(response => {
                if (response.status === 401) {
                    if (window.sessionManager) {
                        window.sessionManager.handleSessionExpired();
                    } else {
                        window.location.href = '/login';
                    }
                }
                return response;
            });
    };

    // Intercept XMLHttpRequest for older AJAX calls
    const originalOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function(...args) {
        this.addEventListener('load', function() {
            if (this.status === 401) {
                if (window.sessionManager) {
                    window.sessionManager.handleSessionExpired();
                } else {
                    window.location.href = '/login';
                }
            }
        });
        return originalOpen.apply(this, args);
    };
});
