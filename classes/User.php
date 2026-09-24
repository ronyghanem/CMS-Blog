<?php

class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($name, $email, $password)
    {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $this->pdo->prepare(
            "INSERT INTO users
            (name, email, password)
            VALUES
            (:name, :email, :password)"
        );

        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword
        ]);
    }

    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM users
             WHERE email = :email"
        );

        $stmt->execute([
            'email' => $email
        ]);

        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM users
             WHERE id = :id"
        );

        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->fetch();
    }

    public function login($email, $password)
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        return $user;
    }

    public function emailExists($email)
    {
        $stmt = $this->pdo->prepare(
            "SELECT id
             FROM users
             WHERE email = :email"
        );

        $stmt->execute([
            'email' => $email
        ]);

        return $stmt->fetch() !== false;
    }

    public function count()
{
    $stmt = $this->pdo->query(
        "SELECT COUNT(*) FROM users"
    );

    return $stmt->fetchColumn();
}

public function updatePassword($id, $password)
{
    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $stmt = $this->pdo->prepare(
        "UPDATE users
         SET password = :password
         WHERE id = :id"
    );

    return $stmt->execute([
        'password' => $hashedPassword,
        'id' => $id
    ]);
}

public function getAll()
{
    $stmt = $this->pdo->query(
        "SELECT id, name
         FROM users
         ORDER BY name ASC"
    );

    return $stmt->fetchAll();
}
}