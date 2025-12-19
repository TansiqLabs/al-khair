<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn() && isset($_COOKIE['remember_token'])) {
    list($selector, $validator) = explode(':', $_COOKIE['remember_token']);

    if ($selector && $validator) {
        $db = getDBConnection();

        $stmt = $db->prepare("SELECT * FROM user_tokens WHERE selector = ?");
        $stmt->execute([$selector]);
        $token_data = $stmt->fetch();

        if ($token_data && hash_equals(hash('sha256', $validator), $token_data['validator_hash'])) {
            // Token is valid, log the user in
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$token_data['user_id']]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                // Regenerate session ID
                session_regenerate_id(true);

                // Rotate token
                $new_validator = bin2hex(random_bytes(32));
                $new_validator_hash = hash('sha256', $new_validator);
                $expires = new DateTime('+30 days');

                $stmt = $db->prepare("UPDATE user_tokens SET validator_hash = ?, expires = ? WHERE id = ?");
                $stmt->execute([$new_validator_hash, $expires->format('Y-m-d H:i:s'), $token_data['id']]);

                setcookie('remember_token', $selector . ':' . $new_validator, $expires->getTimestamp(), '/', '', false, true);
            }
        }
    }
}
