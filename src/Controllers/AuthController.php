<?php

namespace App\Controllers;

class AuthController
{
    private const USERS_FILE = __DIR__ . '/../../users.json';

    private function loadUsers(): array
    {
        if (!file_exists(self::USERS_FILE)) {
            return [];
        }
        $data = json_decode(file_get_contents(self::USERS_FILE), true);
        return $data['users'] ?? [];
    }

    private function saveUsers(array $users): bool
    {
        $data = ['users' => $users];
        return file_put_contents(self::USERS_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }

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

            // Load users and validate credentials
            $users = $this->loadUsers();
            $user = null;
            foreach ($users as $u) {
                if ($u['username'] === $username && $u['password'] === $password) {
                    $user = $u;
                    break;
                }
            }

            if (!$user) {
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
     * Show register form
     */
    public function showRegister(): void
    {
        // Redirect to home if already logged in
        if (!empty($_SESSION['user_id'])) {
            header('Location: /home');
            exit;
        }

        include __DIR__ . '/../../views/register.php';
    }

    /**
     * Handle registration API request
     */
    public function register(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['username']) || empty($input['password']) || empty($input['name'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Todos os campos são obrigatórios']);
                return;
            }

            $username = trim($input['username']);
            $password = trim($input['password']);
            $name = trim($input['name']);

            // Validation
            if (strlen($username) < 3) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Usuário deve ter no mínimo 3 caracteres']);
                return;
            }

            if (strlen($password) < 6) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Senha deve ter no mínimo 6 caracteres']);
                return;
            }

            // Load existing users and check if user already exists
            $users = $this->loadUsers();
            foreach ($users as $u) {
                if ($u['username'] === $username) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Usuário já existe']);
                    return;
                }
            }

            // Add new user
            $newUser = [
                'username' => $username,
                'password' => $password, // In production, use password_hash()
                'name' => $name,
                'created_at' => date('Y-m-d')
            ];

            $users[] = $newUser;

            // Save users
            if ($this->saveUsers($users)) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Cadastro realizado com sucesso!',
                    'user' => $username
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Erro ao salvar cadastro']);
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
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
