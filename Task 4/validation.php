<?php
/**
 * Form validation functions
 */

/**
 * Validates and sanitizes input data
 * 
 * @param string $data Input data to validate
 * @return string Sanitized data
 */
function validate_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validates email format
 * 
 * @param string $email Email to validate
 * @return bool True if valid, false otherwise
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validates password strength
 * 
 * @param string $password Password to validate
 * @return array Array with 'valid' boolean and 'message' string
 */
function validate_password($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    
    return [
        'valid' => empty($errors),
        'message' => empty($errors) ? 'Password is valid' : implode(', ', $errors)
    ];
}

/**
 * Validates form data based on field type
 * 
 * @param array $data Form data to validate
 * @param array $rules Validation rules
 * @return array Array with 'valid' boolean and 'errors' array
 */
function validate_form($data, $rules) {
    $errors = [];
    $validated_data = [];
    
    foreach ($rules as $field => $rule) {
        // Skip if field is not required and not provided
        if (!isset($data[$field]) && !isset($rule['required'])) {
            continue;
        }
        
        // Check required fields
        if (isset($rule['required']) && $rule['required'] && (!isset($data[$field]) || trim($data[$field]) === '')) {
            $errors[$field] = "The {$field} field is required";
            continue;
        }
        
        // Skip validation if field is not provided
        if (!isset($data[$field])) {
            continue;
        }
        
        $value = validate_input($data[$field]);
        
        // Validate by type
        if (isset($rule['type'])) {
            switch ($rule['type']) {
                case 'email':
                    if (!validate_email($value)) {
                        $errors[$field] = "Invalid email format";
                    }
                    break;
                case 'password':
                    $password_validation = validate_password($value);
                    if (!$password_validation['valid']) {
                        $errors[$field] = $password_validation['message'];
                    }
                    break;
                case 'numeric':
                    if (!is_numeric($value)) {
                        $errors[$field] = "The {$field} must be a number";
                    }
                    break;
            }
        }
        
        // Validate min length
        if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
            $errors[$field] = "The {$field} must be at least {$rule['min_length']} characters";
        }
        
        // Validate max length
        if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
            $errors[$field] = "The {$field} cannot exceed {$rule['max_length']} characters";
        }
        
        $validated_data[$field] = $value;
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'data' => $validated_data
    ];
}