<?php

/**
 * Laravel Auto-Login Plugin for RoundCube
 *
 * This plugin handles auto-login requests from Laravel application
 * Place this file in plugins/laravel_autologin/laravel_autologin.php
 */
class laravel_autologin extends rcube_plugin
{
    public $task = 'login';

    public function init()
    {
        $this->add_hook('startup', [$this, 'startup']);
        $this->add_hook('authenticate', [$this, 'authenticate']);
    }

    public function startup($args)
    {
        // Check if this is an auto-login request
        if ($args['task'] == 'login' && rcube_utils::get_input_value('_autologin', rcube_utils::INPUT_GET)) {
            $this->handle_autologin();
        }

        return $args;
    }

    public function authenticate($args)
    {
        // If auto-login is in progress, use the stored credentials
        if ($_SESSION['laravel_autologin_user'] && $_SESSION['laravel_autologin_pass']) {
            $args['user'] = $_SESSION['laravel_autologin_user'];
            $args['pass'] = $_SESSION['laravel_autologin_pass'];
            $args['valid'] = true;

            // Clear the session variables after use
            unset($_SESSION['laravel_autologin_user']);
            unset($_SESSION['laravel_autologin_pass']);
        }

        return $args;
    }

    private function handle_autologin()
    {
        $rcmail = rcmail::get_instance();
        $token = rcube_utils::get_input_value('token', rcube_utils::INPUT_GET);
        $email = rcube_utils::get_input_value('email', rcube_utils::INPUT_GET);

        if (! $token || ! $email) {
            $this->redirect_to_login('Missing token or email');

            return;
        }

        try {
            // Decode and decrypt the token
            $encrypted_data = base64_decode($token);

            // Use the shared secret key from config
            $secret_key = $rcmail->config->get('laravel_autologin_secret');

            // Simple decryption (in production, use proper encryption)
            $decrypted = $this->decrypt_token($encrypted_data, $secret_key);
            $payload = json_decode($decrypted, true);

            if (! $payload || ! isset($payload['email']) || ! isset($payload['password'])) {
                $this->redirect_to_login('Invalid token format');

                return;
            }

            // Verify token hasn't expired
            if (isset($payload['expires_at']) && time() > $payload['expires_at']) {
                $this->redirect_to_login('Token expired');

                return;
            }

            // Verify email matches
            if ($payload['email'] !== $email) {
                $this->redirect_to_login('Email mismatch');

                return;
            }

            // Store credentials in session for authentication hook
            $_SESSION['laravel_autologin_user'] = $payload['email'];
            $_SESSION['laravel_autologin_pass'] = $payload['password'];

            // Redirect to perform actual login
            $rcmail->output->redirect(['_task' => 'mail']);

        } catch (Exception $e) {
            error_log('RoundCube Laravel Auto-login Error: '.$e->getMessage());
            $this->redirect_to_login('Authentication failed');
        }
    }

    private function decrypt_token($encrypted_data, $key)
    {
        // Simple XOR decryption - in production use proper encryption like AES
        $key_length = strlen($key);
        $data_length = strlen($encrypted_data);
        $decrypted = '';

        for ($i = 0; $i < $data_length; $i++) {
            $decrypted .= chr(ord($encrypted_data[$i]) ^ ord($key[$i % $key_length]));
        }

        return $decrypted;
    }

    private function redirect_to_login($error = '')
    {
        $rcmail = rcmail::get_instance();
        $url = $rcmail->url(['_task' => 'login']);

        if ($error) {
            $url .= '&_err='.urlencode($error);
        }

        header('Location: '.$url);
        exit;
    }
}
