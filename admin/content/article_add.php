<?php
require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/../../app/Modules/Admin/module.php';
require __DIR__ . '/../../app/Modules/Content/module.php';
hs_require_admin();
hs_require_permission('article.create');
$db = hs_db();

// categories for select
$catRes = mysqli_query($db, "SELECT id, name FROM hs_categories ORDER BY name ASC");
$categories = $catRes ? mysqli_fetch_all($catRes, MYSQLI_ASSOC) : [];

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid form session. Refresh and try again.';
    } else {
    $title   = trim($_POST['title'] ?? '');
    $slug_in = trim($_POST['slug'] ?? '');
    $slug    = $slug_in !== '' ? $slug_in : hs_content_slugify($title);
    $cat_id  = (int)($_POST['category_id'] ?? 0);
    $type    = $_POST['type'] ?? 'article';
    $region  = $_POST['region'] ?? 'global';
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $is_breaking = isset($_POST['is_breaking']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_trending = isset($_POST['is_trending']) ? 1 : 0;
    $video_url   = trim($_POST['video_url'] ?? '');
    $status  = $_POST['status'] ?? 'draft';
    $tags_raw = trim($_POST['tags'] ?? '');
    $image_main = null;

    if ($title === '') {
        $error = 'Title is required.';
    } else {
        if (!empty($_FILES['image_main']['name'])) {
            $uploadDir = __DIR__ . '/../../writable/uploads/images/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $base = basename($_FILES['image_main']['name']);
            $safe = preg_replace('/[^A-Za-z0-9_.-]/', '_', $base);
            $target = $uploadDir . time() . '_' . $safe;
            if (move_uploaded_file($_FILES['image_main']['tmp_name'], $target)) {
                $image_main = 'writable/uploads/images/' . basename($target);
            }
        }

        $stmt = mysqli_prepare($db, "INSERT INTO hs_posts (category_id,title,slug,excerpt,content,type,region,image_main,video_url,is_breaking,is_featured,is_trending,status)
                                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'issssssssiiis', $cat_id,$title,$slug,$excerpt,$content,$type,$region,$image_main,$video_url,$is_breaking,$is_featured,$is_trending,$status);
        if (!mysqli_stmt_execute($stmt)) {
            $error = 'Error saving post: ' . mysqli_error($db);
        } else {
            $post_id = mysqli_insert_id($db);
            if ($tags_raw !== '') {
                hs_content_sync_tags($db, (int)$post_id, $tags_raw);
            }
            header('Location: ' . hs_base_url('admin/content/articles.php'));
            exit;
        }
    }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add Article – NEWS HDSPTV</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>">
</head>
<body style="max-width:900px;margin:20px auto;padding:0 16px;">
  <?= hs_admin_back_link() ?>
  <h1>Add Article</h1>
  <?php if ($error): ?><div style="color:red;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <?= hs_csrf_input() ?>
    <label>Title</label><br>
    <input type="text" name="title" style="width:100%;" required><br><br>

    <label>Slug (optional)</label><br>
    <input type="text" name="slug" style="width:100%;"><br><br>

    <label>Category</label><br>
    <select name="category_id" style="width:100%;">
      <option value="0">-- None --</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
      <?php endforeach; ?>
    </select><br><br>

    <label>Type</label><br>
    <select name="type">
      <option value="article">Article</option>
      <option value="video">Video</option>
      <option value="gallery">Gallery</option>
    </select><br><br>

    <label>Region</label><br>
    <select name="region">
      <option value="global">Global</option>
      <option value="india">India</option>
      <option value="gcc">GCC</option>
      <option value="kerala">Kerala</option>
      <option value="world">World</option>
      <option value="sports">Sports</option>
    </select><br><br>

    <label>Short Description (Excerpt)</label><br>
    <textarea name="excerpt" style="width:100%;height:60px;"></textarea><br><br>

    <label>Content (HTML allowed)</label><br>
    <textarea name="content" style="width:100%;height:200px;"></textarea><br><br>

    <label>Main Image</label><br>
    <input type="file" name="image_main"><br><br>

    <label>Video URL (YouTube / MP4 link)</label><br>
    <input type="text" name="video_url" style="width:100%;"><br><br>

    <label>Tags (comma separated)</label><br>
    <input type="text" name="tags" style="width:100%;"><br><br>

    <label><input type="checkbox" name="is_breaking"> Breaking</label><br>
    <label><input type="checkbox" name="is_featured"> Featured</label><br>
    <label><input type="checkbox" name="is_trending"> Trending</label><br><br>

    <label>Status</label><br>
    <select name="status">
      <option value="draft">Draft</option>
      <option value="published">Published</option>
    </select><br><br>

    <button type="submit">Save Article</button>
  </form>
</body>
</html>
