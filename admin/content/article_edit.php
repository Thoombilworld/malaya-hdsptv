<?php
require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/../../app/Modules/Admin/module.php';
require __DIR__ . '/../../app/Modules/Content/module.php';
hs_require_admin();
hs_require_permission('article.edit');
require __DIR__ . '/../_layout.php';
$db = hs_db();

// categories for select
$catRes = mysqli_query($db, "SELECT id, name FROM hs_categories ORDER BY name ASC");
$categories = $catRes ? mysqli_fetch_all($catRes, MYSQLI_ASSOC) : [];

$id = (int)($_GET['id'] ?? 0);
$res = mysqli_query($db, "SELECT * FROM hs_posts WHERE id=".$id." LIMIT 1");
$post = $res ? mysqli_fetch_assoc($res) : null;
if (!$post) {
    echo "Post not found.";
    exit;
}
$error = '';
$tags_str = '';
$tr = mysqli_query($db, "SELECT t.name FROM hs_tags t JOIN hs_post_tags pt ON pt.tag_id=t.id WHERE pt.post_id=".$id);
if ($tr) {
    $names = [];
    while ($row = mysqli_fetch_assoc($tr)) $names[] = $row['name'];
    $tags_str = implode(', ', $names);
}

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
    $image_main = $post['image_main'];

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

        $stmt = mysqli_prepare($db, "UPDATE hs_posts SET category_id=?, title=?, slug=?, excerpt=?, content=?, type=?, region=?, image_main=?, video_url=?, is_breaking=?, is_featured=?, is_trending=?, status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'issssssssiiisi', $cat_id,$title,$slug,$excerpt,$content,$type,$region,$image_main,$video_url,$is_breaking,$is_featured,$is_trending,$status,$id);
        if (!mysqli_stmt_execute($stmt)) {
            $error = 'Error updating post: ' . mysqli_error($db);
        } else {
            if ($tags_raw !== '') {
                hs_content_sync_tags($db, (int)$id, $tags_raw);
            } else {
                mysqli_query($db, "DELETE FROM hs_post_tags WHERE post_id=".$id);
            }
            header('Location: ' . hs_base_url('admin/content/articles.php'));
            exit;
        }
    }

    $post['title'] = $title;
    $post['slug'] = $slug;
    $post['category_id'] = $cat_id;
    $post['type'] = $type;
    $post['region'] = $region;
    $post['excerpt'] = $excerpt;
    $post['content'] = $content;
    $post['image_main'] = $image_main;
    $post['video_url'] = $video_url;
    $post['is_breaking'] = $is_breaking;
    $post['is_featured'] = $is_featured;
    $post['is_trending'] = $is_trending;
    $post['status'] = $status;
    $tags_str = $tags_raw;
    }
}

hs_admin_shell_start('Edit Article – HDSPTV', 'Edit News', 'content');
?>

<section class="card" style="max-width:980px;">
  <h2>Edit Article</h2>
  <?php if ($error): ?><div class="error-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <?= hs_csrf_input() ?>
    <div class="field"><label>Title</label><input type="text" name="title" required value="<?= htmlspecialchars($post['title']) ?>"></div>
    <div class="field"><label>Slug</label><input type="text" name="slug" value="<?= htmlspecialchars($post['slug']) ?>"></div>
    <div class="field">
      <label>Category</label>
      <select name="category_id">
        <option value="0">-- None --</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= (int)$c['id'] ?>" <?= $post['category_id']==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="grid-12" style="gap:16px;">
      <div class="col-6 col-md-12 field">
        <label>Type</label>
        <select name="type">
          <option value="article" <?= $post['type']=='article'?'selected':'' ?>>Article</option>
          <option value="video" <?= $post['type']=='video'?'selected':'' ?>>Video</option>
          <option value="gallery" <?= $post['type']=='gallery'?'selected':'' ?>>Gallery</option>
        </select>
      </div>
      <div class="col-6 col-md-12 field">
        <label>Region</label>
        <select name="region">
          <option value="global" <?= $post['region']=='global'?'selected':'' ?>>Global</option>
          <option value="india" <?= $post['region']=='india'?'selected':'' ?>>India</option>
          <option value="gcc" <?= $post['region']=='gcc'?'selected':'' ?>>GCC</option>
          <option value="kerala" <?= $post['region']=='kerala'?'selected':'' ?>>Kerala</option>
          <option value="world" <?= $post['region']=='world'?'selected':'' ?>>World</option>
          <option value="sports" <?= $post['region']=='sports'?'selected':'' ?>>Sports</option>
        </select>
      </div>
    </div>

    <div class="field"><label>Short Description (Excerpt)</label><textarea name="excerpt" style="width:100%;height:80px;border:1px solid var(--border);border-radius:12px;padding:12px;"><?= htmlspecialchars($post['excerpt']) ?></textarea></div>
    <div class="field"><label>Content (HTML allowed)</label><textarea name="content" style="width:100%;height:220px;border:1px solid var(--border);border-radius:12px;padding:12px;"><?= htmlspecialchars($post['content']) ?></textarea></div>

    <div class="field">
      <label>Main Image</label>
    <?php if (!empty($post['image_main'])): ?>
      <p class="muted" style="margin:0;">Current: <?= htmlspecialchars($post['image_main']) ?></p>
    <?php endif; ?>
      <input type="file" name="image_main">
    </div>

    <div class="field"><label>Video URL (YouTube / MP4 link)</label><input type="text" name="video_url" value="<?= htmlspecialchars($post['video_url']) ?>"></div>
    <div class="field"><label>Tags (comma separated)</label><input type="text" name="tags" value="<?= htmlspecialchars($tags_str) ?>"></div>

    <label style="display:flex;gap:8px;align-items:center;"><input type="checkbox" name="is_breaking" <?= $post['is_breaking']?'checked':'' ?>> Breaking</label>
    <label style="display:flex;gap:8px;align-items:center;"><input type="checkbox" name="is_featured" <?= $post['is_featured']?'checked':'' ?>> Featured</label>
    <label style="display:flex;gap:8px;align-items:center;margin-bottom:16px;"><input type="checkbox" name="is_trending" <?= $post['is_trending']?'checked':'' ?>> Trending</label>

    <div class="field" style="max-width:260px;">
      <label>Status</label>
      <select name="status">
        <option value="draft" <?= $post['status']=='draft'?'selected':'' ?>>Draft</option>
        <option value="published" <?= $post['status']=='published'?'selected':'' ?>>Published</option>
      </select>
    </div>

    <button class="btn btn-primary" type="submit">Update Article</button>
  </form>
</section>

<?php hs_admin_shell_end(); ?>
