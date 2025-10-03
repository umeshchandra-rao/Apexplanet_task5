<?php
/**
 * Secure database queries using prepared statements
 */
require_once 'db.php';

/**
 * Get all posts with secure query
 * 
 * @param int $limit Optional limit
 * @param int $offset Optional offset
 * @return array Posts
 */
function get_all_posts($limit = null, $offset = null) {
    $pdo = get_pdo();
    $sql = "SELECT p.*, u.username FROM posts p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC";
    
    if ($limit !== null) {
        $sql .= " LIMIT :limit";
        if ($offset !== null) {
            $sql .= " OFFSET :offset";
        }
    }
    
    $stmt = $pdo->prepare($sql);
    
    if ($limit !== null) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        if ($offset !== null) {
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
    }
    
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Get post by ID with secure query
 * 
 * @param int $id Post ID
 * @return array|false Post data or false if not found
 */
function get_post_by_id($id) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT p.*, u.username FROM posts p LEFT JOIN users u ON p.user_id = u.id WHERE p.id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
}

/**
 * Create new post with secure query
 * 
 * @param string $title Post title
 * @param string $content Post content
 * @param int|null $user_id User ID (optional)
 * @return bool Success status
 */
function create_post($title, $content, $user_id = null) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)");
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':content', $content, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $user_id, $user_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    return $stmt->execute();
}

/**
 * Update post with secure query
 * 
 * @param int $id Post ID
 * @param string $title Post title
 * @param string $content Post content
 * @return bool Success status
 */
function update_post($id, $title, $content) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':content', $content, PDO::PARAM_STR);
    return $stmt->execute();
}

/**
 * Delete post with secure query
 * 
 * @param int $id Post ID
 * @return bool Success status
 */
function delete_post($id) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

/**
 * Search posts with secure query
 * 
 * @param string $query Search query
 * @return array Posts
 */
function search_posts($query) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("
        SELECT p.*, u.username 
        FROM posts p 
        LEFT JOIN users u ON p.user_id = u.id 
        WHERE MATCH(p.title, p.content) AGAINST(:query IN NATURAL LANGUAGE MODE)
        ORDER BY p.created_at DESC
    ");
    $stmt->bindValue(':query', $query, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll();
}