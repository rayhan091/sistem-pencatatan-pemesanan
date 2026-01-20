// notifications.js - Notification System
class NotificationSystem {
    constructor() {
        this.container = document.getElementById('notificationContainer');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'notificationContainer';
            this.container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                max-width: 400px;
            `;
            document.body.appendChild(this.container);
        }
        
        this.notifications = [];
    }
    
    show(message, type = 'info', duration = 5000) {
        const id = Date.now();
        const notification = this.createNotification(id, message, type);
        
        this.container.appendChild(notification);
        this.notifications.push({ id, element: notification });
        
        // Auto remove
        if (duration > 0) {
            setTimeout(() => this.remove(id), duration);
        }
        
        return id;
    }
    
    createNotification(id, message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.dataset.id = id;
        notification.style.cssText = `
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
        `;
        
        // Set border color based on type
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        notification.style.borderLeftColor = colors[type] || '#17a2b8';
        
        const icon = this.getIcon(type);
        
        notification.innerHTML = `
            <div style="display: flex; align-items: flex-start; gap: 10px;">
                <div style="color: ${colors[type] || '#17a2b8'}; font-size: 18px;">
                    <i class="fas fa-${icon}"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; margin-bottom: 5px; color: #333;">
                        ${type.toUpperCase()}
                    </div>
                    <div style="color: #666; font-size: 14px;">
                        ${message}
                    </div>
                </div>
                <button class="close-btn" style="
                    background: none;
                    border: none;
                    color: #999;
                    cursor: pointer;
                    font-size: 16px;
                    padding: 0;
                    margin-left: 10px;
                ">&times;</button>
            </div>
        `;
        
        // Add close button event
        notification.querySelector('.close-btn').addEventListener('click', () => {
            this.remove(id);
        });
        
        return notification;
    }
    
    getIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
    
    remove(id) {
        const index = this.notifications.findIndex(n => n.id === id);
        if (index !== -1) {
            const notification = this.notifications[index].element;
            notification.style.animation = 'slideOut 0.3s ease forwards';
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
                this.notifications.splice(index, 1);
            }, 300);
        }
    }
    
    clearAll() {
        this.notifications.forEach(n => {
            if (n.element.parentNode) {
                n.element.parentNode.removeChild(n.element);
            }
        });
        this.notifications = [];
    }
}

// Create global instance
window.notificationSystem = new NotificationSystem();

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Helper functions
window.showSuccess = function(message, duration = 5000) {
    return notificationSystem.show(message, 'success', duration);
};

window.showError = function(message, duration = 5000) {
    return notificationSystem.show(message, 'error', duration);
};

window.showWarning = function(message, duration = 5000) {
    return notificationSystem.show(message, 'warning', duration);
};

window.showInfo = function(message, duration = 5000) {
    return notificationSystem.show(message, 'info', duration);
};