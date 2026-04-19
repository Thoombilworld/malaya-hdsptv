<?php

function hs_content_slugify(string $text): string {
    $text = trim($text);
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text !== '' ? $text : ('post-' . time());
}

function hs_content_sync_tags(mysqli $db, int $postId, string $tagsRaw): void {
    if ($postId <= 0) return;
    mysqli_query($db, 'DELETE FROM hs_post_tags WHERE post_id=' . $postId);

    $tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
    foreach ($tags as $tagName) {
        if ($tagName === '') continue;
        $slug = hs_content_slugify($tagName);
        $stmt = mysqli_prepare($db, 'INSERT INTO hs_tags (name,slug) VALUES (?,?) ON DUPLICATE KEY UPDATE name=VALUES(name)');
        mysqli_stmt_bind_param($stmt, 'ss', $tagName, $slug);
        mysqli_stmt_execute($stmt);

        $tagId = mysqli_insert_id($db);
        if ($tagId === 0) {
            $res = mysqli_query($db, "SELECT id FROM hs_tags WHERE slug='" . mysqli_real_escape_string($db, $slug) . "' LIMIT 1");
            $row = $res ? mysqli_fetch_assoc($res) : null;
            $tagId = (int)($row['id'] ?? 0);
        }

        if ($tagId > 0) {
            mysqli_query($db, 'INSERT IGNORE INTO hs_post_tags (post_id, tag_id) VALUES (' . $postId . ',' . $tagId . ')');
        }
    }
}
