<?php

class Sanitizer {
    public function sanitize_input($user_input)
    {
        $user_input = strip_tags($user_input);
        $user_input = trim($user_input);
        $user_input = htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
        return $user_input;
    }
}

?>