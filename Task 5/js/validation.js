/**
 * Client-side form validation
 */

document.addEventListener('DOMContentLoaded', function() {
    // Find all forms with validation class
    const forms = document.querySelectorAll('form.needs-validation');
    
    // Add validation to each form
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!validateForm(form)) {
                event.preventDefault();
                event.stopPropagation();
            }
        }, false);
        
        // Add input event listeners for real-time validation
        const inputs = form.querySelectorAll('input, textarea, select');
        Array.from(inputs).forEach(input => {
            input.addEventListener('input', function() {
                validateInput(input);
            });
            
            input.addEventListener('blur', function() {
                validateInput(input);
            });
        });
    });
});

/**
 * Validate a form
 * @param {HTMLFormElement} form - The form to validate
 * @returns {boolean} - True if valid, false otherwise
 */
function validateForm(form) {
    let isValid = true;
    
    // Validate each input
    const inputs = form.querySelectorAll('input, textarea, select');
    Array.from(inputs).forEach(input => {
        if (!validateInput(input)) {
            isValid = false;
        }
    });
    
    // Add was-validated class to show validation styles
    form.classList.add('was-validated');
    
    return isValid;
}

/**
 * Validate a single input
 * @param {HTMLInputElement} input - The input to validate
 * @returns {boolean} - True if valid, false otherwise
 */
function validateInput(input) {
    // Skip disabled or hidden inputs
    if (input.disabled || input.type === 'hidden') {
        return true;
    }
    
    let isValid = true;
    const errorElement = input.nextElementSibling;
    
    // Clear previous error
    if (errorElement && errorElement.classList.contains('invalid-feedback')) {
        errorElement.textContent = '';
    }
    
    // Required validation
    if (input.hasAttribute('required') && !input.value.trim()) {
        isValid = false;
        showError(input, 'This field is required');
    }
    
    // Email validation
    if (input.type === 'email' && input.value.trim()) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(input.value)) {
            isValid = false;
            showError(input, 'Please enter a valid email address');
        }
    }
    
    // Password validation
    if (input.type === 'password' && input.value.trim()) {
        if (input.value.length < 8) {
            isValid = false;
            showError(input, 'Password must be at least 8 characters long');
        } else if (!/[A-Z]/.test(input.value)) {
            isValid = false;
            showError(input, 'Password must contain at least one uppercase letter');
        } else if (!/[a-z]/.test(input.value)) {
            isValid = false;
            showError(input, 'Password must contain at least one lowercase letter');
        } else if (!/[0-9]/.test(input.value)) {
            isValid = false;
            showError(input, 'Password must contain at least one number');
        }
    }
    
    // Min length validation
    if (input.hasAttribute('minlength') && input.value.trim()) {
        const minLength = parseInt(input.getAttribute('minlength'));
        if (input.value.length < minLength) {
            isValid = false;
            showError(input, `Must be at least ${minLength} characters long`);
        }
    }
    
    // Max length validation
    if (input.hasAttribute('maxlength') && input.value.trim()) {
        const maxLength = parseInt(input.getAttribute('maxlength'));
        if (input.value.length > maxLength) {
            isValid = false;
            showError(input, `Cannot exceed ${maxLength} characters`);
        }
    }
    
    // Pattern validation
    if (input.hasAttribute('pattern') && input.value.trim()) {
        const pattern = new RegExp(input.getAttribute('pattern'));
        if (!pattern.test(input.value)) {
            isValid = false;
            showError(input, input.getAttribute('data-pattern-message') || 'Please match the requested format');
        }
    }
    
    // Update visual state
    if (isValid) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    } else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
    }
    
    return isValid;
}

/**
 * Show error message for an input
 * @param {HTMLInputElement} input - The input with error
 * @param {string} message - Error message
 */
function showError(input, message) {
    let errorElement = input.nextElementSibling;
    
    // Create error element if it doesn't exist
    if (!errorElement || !errorElement.classList.contains('invalid-feedback')) {
        errorElement = document.createElement('div');
        errorElement.className = 'invalid-feedback';
        input.parentNode.insertBefore(errorElement, input.nextSibling);
    }
    
    errorElement.textContent = message;
}