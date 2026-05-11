<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Helpers\JwtHelper;
use App\Helpers\Validator;

class AuthService {
    private UserRepository $userRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
    }

    public function register(array $data): array {
        $errors = Validator::validate($data, [
            'name'     => 'required|min:3|max:120',
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }

        if ($this->userRepo->emailExists($data['email'])) {
            throw new \RuntimeException('Este email já está registado');
        }

        $id = $this->userRepo->create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'role'          => 'student',
            'language'      => $data['language'] ?? 'pt',
        ]);

        $user = $this->userRepo->findById($id);
        $token = JwtHelper::encode(['user_id' => $id, 'role' => 'student']);

        return ['user' => $user, 'token' => $token];
    }

    public function login(string $email, string $password): array {
        $user = $this->userRepo->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new \RuntimeException('Email ou password incorrectos');
        }

        unset($user['password_hash'], $user['reset_token'], $user['reset_expiry']);
        $token = JwtHelper::encode(['user_id' => $user['id'], 'role' => $user['role']]);

        return ['user' => $user, 'token' => $token];
    }
}