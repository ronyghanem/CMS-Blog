<?php

class Category
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM categories ORDER BY id DESC"
        );

        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categories WHERE id = :id"
        );

        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->fetch();
    }

    public function create($name)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (category_name)
             VALUES (:name)"
        );

        return $stmt->execute([
            'name' => $name
        ]);
    }

    public function update($id, $name)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE categories
             SET category_name = :name
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $id,
            'name' => $name
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM categories
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $id
        ]);
    }

    public function count()
{
    $stmt = $this->pdo->query(
        "SELECT COUNT(*) FROM categories"
    );

    return $stmt->fetchColumn();
}

}