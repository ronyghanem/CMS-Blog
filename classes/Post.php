<?php

class Post
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $sql = "
            SELECT
                posts.id,
                posts.title,
                posts.content,
                posts.date,
                posts.reading_time,
                categories.category_name,
                users.name AS author_name
            FROM posts
            INNER JOIN categories
                ON posts.category_id = categories.id
            INNER JOIN users
                ON posts.user_id = users.id
            ORDER BY posts.id DESC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM posts
             WHERE id = :id"
        );

        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->fetch();
    }

    public function create(
        $title,
        $categoryId,
        $userId,
        $content,
        $date,
        $readingTime
    ) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO posts
            (title, category_id, user_id, content, date, reading_time)
            VALUES
            (:title, :category_id, :user_id, :content, :date, :reading_time)"
        );

        return $stmt->execute([
            'title' => $title,
            'category_id' => $categoryId,
            'user_id' => $userId,
            'content' => $content,
            'date' => $date,
            'reading_time' => $readingTime
        ]);
    }

    public function update(
        $id,
        $title,
        $categoryId,
        $userId,
        $content,
        $date,
        $readingTime
    ) {
        $stmt = $this->pdo->prepare(
            "UPDATE posts SET
                title = :title,
                category_id = :category_id,
                user_id = :user_id,
                content = :content,
                date = :date,
                reading_time = :reading_time
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $id,
            'title' => $title,
            'category_id' => $categoryId,
            'user_id' => $userId,
            'content' => $content,
            'date' => $date,
            'reading_time' => $readingTime
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM posts
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $id
        ]);
    }

    public function count()
{
    $stmt = $this->pdo->query(
        "SELECT COUNT(*) FROM posts"
    );

    return $stmt->fetchColumn();
}
}