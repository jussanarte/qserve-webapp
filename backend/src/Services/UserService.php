<?php
namespace App\Services;

use App\Helpers\Validator;
use App\Repositories\UserRepository;

class UserService {
    private UserRepository $repo;

    public function __construct() {
        $this->repo = new UserRepository();
    }

    public function attendants(): array {
        return $this->repo->findByRole('attendant');
    }

    public function createAttendant(array $data): array {
        $errors = Validator::validate($data, [
            'name' => 'required|min:3|max:120',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }

        if ($this->repo->emailExists($data['email'])) {
            throw new \RuntimeException('Este email ja esta registado');
        }

        $id = $this->repo->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'role' => 'attendant',
            'language' => $data['language'] ?? 'pt',
        ]);

        return $this->repo->findById($id);
    }

    public function updateAttendant(int $id, array $data): array {
        $current = $this->repo->findById($id);
        if (!$current || $current['role'] !== 'attendant') {
            throw new \RuntimeException('Funcionario nao encontrado');
        }

        $errors = Validator::validate($data, [
            'name' => 'required|min:3|max:120',
            'email' => 'required|email',
        ]);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }

        $existing = $this->repo->findByEmail($data['email']);
        if ($existing && (int)$existing['id'] !== $id) {
            throw new \RuntimeException('Este email ja esta registado');
        }

        $this->repo->update($id, [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'attendant',
            'language' => $data['language'] ?? $current['language'],
            'is_active' => $data['is_active'] ?? $current['is_active'],
        ]);

        if (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                throw new \InvalidArgumentException(json_encode(['password' => ['Minimo 6 caracteres']]));
            }
            $this->repo->updatePassword($id, password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]));
        }

        return $this->repo->findById($id);
    }

    public function deactivateAttendant(int $id): void {
        $current = $this->repo->findById($id);
        if (!$current || $current['role'] !== 'attendant') {
            throw new \RuntimeException('Funcionario nao encontrado');
        }
        $this->repo->deactivate($id);
    }
}
