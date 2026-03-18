// Form Management Utilities
class FormManager {
  static openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }
  }

  static closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  }

  static clearForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
      form.reset();
    }
  }

  static setupFormSubmit(formId, callback) {
    const form = document.getElementById(formId);
    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        if (typeof callback === 'function') {
          callback(data);
        }
      });
    }
  }

  static setupModalTrigger(triggerId, modalId) {
    const trigger = document.getElementById(triggerId);
    if (trigger) {
      trigger.addEventListener('click', () => {
        FormManager.openModal(modalId);
      });
    }
  }

  static setupModalClose(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Close on X button
    const closeBtn = modal.querySelector('[data-close-modal]');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        FormManager.closeModal(modalId);
      });
    }

    // Close on background click
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        FormManager.closeModal(modalId);
      }
    });
  }

  static validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  static showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (field) {
      field.classList.add('border-red-500');
      const errorEl = document.createElement('p');
      errorEl.className = 'text-red-500 text-xs mt-1';
      errorEl.textContent = message;
      field.parentNode.appendChild(errorEl);
    }
  }

  static clearFieldErrors(formId) {
    const form = document.getElementById(formId);
    if (form) {
      const fields = form.querySelectorAll('input, select, textarea');
      fields.forEach(field => {
        field.classList.remove('border-red-500');
      });
      const errors = form.querySelectorAll('.text-red-500');
      errors.forEach(error => error.remove());
    }
  }

  static populateForm(formId, data) {
    const form = document.getElementById(formId);
    if (!form) return;

    Object.keys(data).forEach(key => {
      const field = form.querySelector(`[name="${key}"]`);
      if (field) {
        field.value = data[key];
      }
    });
  }
}
