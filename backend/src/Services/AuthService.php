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

        $role = $data['role'] ?? 'student';
        if (!in_array($role, ['student', 'admin'], true)) {
            $role = 'student';
        }

        $id = $this->userRepo->create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'role'          => $role,
            'language'      => $data['language'] ?? 'pt',
        ]);

        $user = $this->userRepo->findById($id);
        $token = JwtHelper::encode(['user_id' => $id, 'role' => $role]);

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

    public function forgotPassword(string $email): array {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(json_encode(['email' => ['Email invalido']]));
        }

        $token = bin2hex(random_bytes(32));
        $expiry = (new \DateTimeImmutable('+1 hour'))->format('Y-m-d H:i:s');
        $this->userRepo->setResetToken($email, $token, $expiry);

        return [
            'reset_token' => $token,
            'expires_at' => $expiry,
        ];
    }

    public function resetPassword(string $token, string $password): void {
        $errors = Validator::validate(
            ['token' => $token, 'password' => $password],
            ['token' => 'required|min:20', 'password' => 'required|min:6']
        );
        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }

        $user = $this->userRepo->findByResetToken($token);
        if (!$user) {
            throw new \RuntimeException('Token invalido ou expirado');
        }

        $this->userRepo->updatePassword(
            (int)$user['id'],
            password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])
        );
    }
}
