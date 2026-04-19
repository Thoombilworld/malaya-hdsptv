<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;

class LegacyProxyController extends Controller
{
    private function legacy(string $file): View
    {
        $path = base_path('../' . ltrim($file, '/'));
        abort_unless(File::exists($path), 404);

        return view('legacy-proxy', ['legacyPath' => $path]);
    }

    public function home(): View { return $this->legacy('index.php'); }
    public function about(): View { return $this->legacy('about.php'); }
    public function contact(): View { return $this->legacy('contact.php'); }
    public function breaking(): View { return $this->legacy('breaking.php'); }
    public function trending(): View { return $this->legacy('trending.php'); }
    public function video(): View { return $this->legacy('video.php'); }
    public function gallery(): View { return $this->legacy('gallery.php'); }
    public function live(): View { return $this->legacy('live.php'); }
    public function profile(): View { return $this->legacy('profile.php'); }
    public function saved(): View { return $this->legacy('saved.php'); }
    public function notifications(): View { return $this->legacy('notifications.php'); }
    public function post(): View { return $this->legacy('post.php'); }
    public function category(): View { return $this->legacy('category.php'); }
    public function tag(): View { return $this->legacy('tag.php'); }
    public function search(): View { return $this->legacy('search.php'); }

    public function adminIndex(): View { return $this->legacy('admin/index.php'); }
    public function adminLogin(): View { return $this->legacy('admin/login.php'); }
    public function adminLogout(): View { return $this->legacy('admin/logout.php'); }
    public function adminUsers(): View { return $this->legacy('admin/users.php'); }
    public function adminAds(): View { return $this->legacy('admin/ads.php'); }
    public function adminSeo(): View { return $this->legacy('admin/seo.php'); }
    public function adminSocial(): View { return $this->legacy('admin/social.php'); }
    public function adminSocialDispatch(): View { return $this->legacy('admin/social_dispatch.php'); }
    public function adminHomepage(): View { return $this->legacy('admin/homepage.php'); }
    public function adminLogs(): View { return $this->legacy('admin/logs.php'); }

    public function adminContentIndex(): View { return $this->legacy('admin/content/index.php'); }
    public function adminContentArticles(): View { return $this->legacy('admin/content/articles.php'); }
    public function adminContentArticleAdd(): View { return $this->legacy('admin/content/article_add.php'); }
    public function adminContentArticleEdit(): View { return $this->legacy('admin/content/article_edit.php'); }
    public function adminContentArticleDelete(): View { return $this->legacy('admin/content/article_delete.php'); }
    public function adminContentCategories(): View { return $this->legacy('admin/content/categories.php'); }
    public function adminContentTags(): View { return $this->legacy('admin/content/tags.php'); }
}

