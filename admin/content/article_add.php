<?php
require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/../../app/Modules/Admin/module.php';
require __DIR__ . '/../../app/Modules/Content/module.php';
hs_require_admin();
hs_require_permission('article.create');
require __DIR__ . '/../_layout.php';
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
            header('Location: ' . hs_admin_content_url('articles.php'));
            exit;
        }
    }
    }
}

hs_admin_shell_start('Add Article – HDSPTV', 'Create News', 'content');
?>

<section class="card" style="max-width:980px;">
  <h2>Add Article</h2>
  <?php if ($error): ?><div class="error-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post" action="<?= hs_admin_content_url('article_add.php') ?>" enctype="multipart/form-data">
    <?= hs_csrf_input() ?>
    <div class="field"><label>Title</label><input type="text" name="title" required></div>
    <div class="field"><label>Slug (optional)</label><input type="text" name="slug"></div>
    <div class="field">
      <label>Category</label>
      <select name="category_id">
        <option value="0">-- None --</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="grid-12" style="gap:16px;">
      <div class="col-6 col-md-12 field">
        <label>Type</label>
        <select name="type">
          <option value="article">Article</option>
          <option value="video">Video</option>
          <option value="gallery">Gallery</option>
        </select>
      </div>
      <div class="col-6 col-md-12 field">
        <label>Region</label>
        <select name="region">
          <option value="global">Global</option>
          <option value="india">India</option>
          <option value="gcc">GCC</option>
          <option value="kerala">Kerala</option>
          <option value="world">World</option>
          <option value="sports">Sports</option>
        </select>
      </div>
    </div>

    <div class="field">
      <label>Short Description (Excerpt)</label>
      <textarea name="excerpt" style="width:100%;height:80px;border:1px solid var(--border);border-radius:12px;padding:12px;"></textarea>
    </div>
    <div class="field">
      <label>Content (HTML allowed)</label>
      <textarea name="content" style="width:100%;height:220px;border:1px solid var(--border);border-radius:12px;padding:12px;"></textarea>
    </div>

    <div class="field"><label>Main Image</label><input type="file" name="image_main"></div>
    <div class="field"><label>Video URL (YouTube / MP4 link)</label><input type="text" name="video_url"></div>
    <div class="field"><label>Tags (comma separated)</label><input type="text" name="tags"></div>

    <label style="display:flex;gap:8px;align-items:center;"><input type="checkbox" name="is_breaking"> Breaking</label>
    <label style="display:flex;gap:8px;align-items:center;"><input type="checkbox" name="is_featured"> Featured</label>
    <label style="display:flex;gap:8px;align-items:center;margin-bottom:16px;"><input type="checkbox" name="is_trending"> Trending</label>

    <div class="field" style="max-width:260px;">
      <label>Status</label>
      <select name="status">
        <option value="draft">Draft</option>
        <option value="published">Published</option>
      </select>
    </div>
    <button class="btn btn-primary" type="submit">Save Article</button>
  </form>
</section>

<?php hs_admin_shell_end(); ?>
