<?php

namespace App\Providers;

use App\Article;
use App\Discussion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Tools\FileManager\BaseManager;
use App\Tools\FileManager\UpyunManager;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * 限制数据库表字符型字段的长度:
         * From: https://laravel-news.com/laravel-5-4-key-too-long-error
         * From: https://news.laravel-china.org/posts/544
         *
         * Laravel 5.4 把默认数据库字符集更改成 utf8mb4，作为对存储 emojis 的支持。
         * 只要你运行的是 MySQL v5.7.7 及更高版本，那么你就不会出现本文提到的错误。
         * 对于那些运行 MariaDB 或旧版本的 MySQL 的程序，可能会在尝试运行迁移时遇到下面的错误：
         * 'syntax error or access violation: 1071 Specified key was t oo long; max key length is 767 bytes'
         */
        Schema::defaultStringLength(191);

        $lang = config('app.locale') != 'zh_cn' ? config('app.locale') : 'zh';
        \Carbon\Carbon::setLocale($lang);

        Relation::morphMap([
            'discussions' => Discussion::class,
            'articles'    => Article::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('uploader', function ($app) {
            $config = config('filesystems.default', 'public');

            if ($config == 'upyun') {
                return new UpyunManager();
            }

            return new BaseManager();
        });
    }
}
