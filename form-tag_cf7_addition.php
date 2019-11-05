Add this code in line 232 to form-tag.php in Contact Form 7 to use 
custom user fields that are saved as arrays as default values in CF7.

if (is_array($user_prop)) {
    $user_prop = implode(', ',$user_prop);
}