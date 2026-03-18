// Tool Rental Management System - Main App Module
class App {
  constructor() {
    this.sidebarOpen = true;
    this.init();
  }

  init() {
    this.setupSidebar();
    this.setupNavigation();
    this.setupLogout();
  }

  setupSidebar() {
    // Sidebar toggle is now handled in layout.js
    // This method is kept for backward compatibility
  }

  setupNavigation() {
    const navLinks = document.querySelectorAll('[data-nav-link]');
    navLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const href = link.getAttribute('href');
        if (href && href !== '#') {
          window.location.href = href;
        }
      });
    });

    // Highlight active nav item
    this.highlightActiveNav();
  }

  highlightActiveNav() {
    const navItems = document.querySelectorAll('.nav-item');
    const currentPage = window.location.pathname.split('/').pop() || 'dashboard.html';

    navItems.forEach(item => {
      const href = item.getAttribute('href');
      if (href && href.endsWith(currentPage)) {
        item.classList.add('bg-blue-600', 'text-white');
        item.classList.remove('text-gray-700', 'hover:bg-gray-100');
      }
    });
  }

  setupLogout() {
    // Logout is now handled in layout.js
    // This method is kept for backward compatibility
  }

  // Show notification
  showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg text-white z-50 ${
      type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
      notification.remove();
    }, 3000);
  }

  // Format currency
  static formatCurrency(value) {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
    }).format(value);
  }

  // Format date
  static formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  }

  // Get status badge color
  static getStatusBadgeColor(status) {
    const statusColors = {
      'Active': 'bg-green-100 text-green-800',
      'Rented': 'bg-yellow-100 text-yellow-800',
      'Available': 'bg-blue-100 text-blue-800',
      'Completed': 'bg-gray-100 text-gray-800',
      'Pending': 'bg-orange-100 text-orange-800',
      'Delivered': 'bg-green-100 text-green-800',
      'In Transit': 'bg-blue-100 text-blue-800',
      'Damaged': 'bg-red-100 text-red-800',
      'Lost': 'bg-purple-100 text-purple-800',
      'Good': 'bg-green-100 text-green-800',
      'Inactive': 'bg-gray-100 text-gray-800',
      'On Track': 'bg-blue-100 text-blue-800',
      'Menunggak': 'bg-red-100 text-red-800',
      'Returning': 'bg-yellow-100 text-yellow-800',
      'On Check': 'bg-purple-100 text-purple-800',
    };
    return statusColors[status] || 'bg-gray-100 text-gray-800';
  }
}

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  window.app = new App();
});
