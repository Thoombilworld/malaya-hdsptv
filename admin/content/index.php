<?php
require __DIR__ . '/../../bootstrap.php';
hs_require_admin();
hs_require_permission('article.create');
require __DIR__ . '/../_layout.php';

hs_admin_shell_start('Content Manager – HDSPTV', 'Content Manager', 'content');
?>

<section class="grid-12">
  <article class="card col-4 col-md-12">
    <h2>Articles</h2>
    <p class="muted">Create, review, edit and publish all newsroom stories.</p>
    <a class="btn btn-primary" href="<?= hs_base_url('admin/content/articles.php') ?>">Open Articles</a>
  </article>

  <article class="card col-4 col-md-12">
    <h2>Categories</h2>
    <p class="muted">Manage category hierarchy, visibility, and taxonomy structure.</p>
    <a class="btn btn-secondary" href="<?= hs_base_url('admin/content/categories.php') ?>">Open Categories</a>
  </article>

  <article class="card col-4 col-md-12">
    <h2>Tags</h2>
    <p class="muted">Control topic tags for SEO, discovery and internal linking.</p>
    <a class="btn btn-secondary" href="<?= hs_base_url('admin/content/tags.php') ?>">Open Tags</a>
  </article>
</section>

<?php hs_admin_shell_end(); ?>
