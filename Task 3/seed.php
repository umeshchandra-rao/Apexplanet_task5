<?php
require __DIR__ . '/db.php';

$pdo = get_pdo();

$pdo->exec('TRUNCATE TABLE posts');

$stmt = $pdo->prepare('INSERT INTO posts (title, content, created_at) VALUES (:title, :content, :created_at)');

$lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. Cras elementum ultrices diam. Maecenas ligula massa, varius a, semper congue, euismod non, mi. Proin porttitor, orci nec nonummy molestie, enim est eleifend mi, non fermentum diam nisl sit amet erat. Duis semper. Duis arcu massa, scelerisque vitae, consequat in, pretium a, enim.";

for ($i = 1; $i <= 37; $i++) {
    $title = "Sample Post #$i";
    $content = $lorem . "\n\nParagraph $i: " . str_repeat('content ', rand(10, 40));
    $created = date('Y-m-d H:i:s', time() - rand(0, 60*60*24*30));
    $stmt->execute([
        ':title' => $title,
        ':content' => $content,
        ':created_at' => $created,
    ]);
}

echo "Seeded 37 posts.\n";


