<?php
/**
 * Admin utility functions
 */

/**
 * Validates admin phone number
 * @param string $phone
 * @return bool|string True if valid, error message otherwise
 */
function validate_admin_phone($phone)
{
    $phone = trim($phone);
    if (empty($phone)) {
        return "Phone number is required.";
    }
    // Remove potential leading +
    $check_phone = ltrim($phone, '+');
    if (!ctype_digit($check_phone)) {
        return "Phone number must contain only digits.";
    }
    if (strlen($check_phone) < 7 || strlen($check_phone) > 15) {
        return "Phone number must be between 7 and 15 digits.";
    }
    return true;
}

/**
 * Validates admin email address using detailed logic
 * @param string $email
 * @return bool|string True if valid, error message otherwise
 */
function validate_admin_email($email)
{
    if (empty($email)) {
        return "Email is required.";
    }

    if (substr_count($email, '@') !== 1) {
        return "Invalid email format.";
    }

    list($local, $domain) = explode('@', $email);

    if (strlen($local) < 1 || strlen($local) > 64) {
        return "Local part must be 1-64 characters.";
    } elseif ($local[0] === '.' || substr($local, -1) === '.') {
        return "Local part cannot start or end with a dot.";
    } elseif (!preg_match('/^[A-Za-z0-9._%+-]+$/', $local)) {
        return "Local part contains invalid characters.";
    } elseif (strpos($domain, '.') === false) {
        return "Domain part must contain at least one dot.";
    } else {
        foreach (explode('.', $domain) as $label) {
            if (strlen($label) < 1 || strlen($label) > 63) {
                return "Domain label must be 1-63 characters.";
            }
            if ($label[0] === '-' || substr($label, -1) === '-') {
                return "Domain label cannot start or end with a hyphen.";
            }
            if (!preg_match('/^[A-Za-z0-9-]+$/', $label)) {
                return "Domain label contains invalid characters.";
            }
        }
    }

    return true;
}
?>