<?php

namespace App\Controllers;

class AuthController
{
    // Simple in-memory user database (replace with real DB in production)
    private const USERS = [
        'admin' => '123456',
        'user' => 'password'
    ];

    /**
     * Show login form
     */
    public function showLogin(): void
    {
        // Redirect to home if already logged in
        if (!empty($_SESSION['user_id'])) {
            header('Location: /home');
            exit;
        }

        include __DIR__ . '/../../views/login.php';
    }

    /**
     * Handle login API request
     */
    public function login(): void
    {
        header('Content-Type: application/json');

        // Only allow POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['username']) || empty($input['password'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Usuário e senha são obrigatórios']);
                return;
            }

            $username = trim($input['username']);
            $password = trim($input['password']);

            // Validate credentials
            if (!isset(self::USERS[$username]) || self::USERS[$username] !== $password) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Usuário ou senha inválidos']);
                return;
            }

            // Set session
            $_SESSION['user_id'] = md5($username . time());
            $_SESSION['user_name'] = ucfirst($username);
            $_SESSION['login_time'] = time();

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'user' => $_SESSION['user_name']
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Show home page
     */
    public function showHome(): void
    {
        // Check if user is logged in
        if (empty($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        include __DIR__ . '/../../views/home.php';
    }

    /**
     * Handle logout
     */
    public function logout(): void
    {
        session_destroy();
        header('Location: /');
        exit;
    }
}
