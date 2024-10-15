<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BackendServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $models = [
            'User',
            'UserCourse',
            'UserDetail',
            'Cart',
            'Category',
            'Chapter',
            'Comment',
            'CommentLike',
            'Course',
            'Course',
            'CourseInfo',
            'Lesson',
            'Notification',
            'NotificationUser',
            'Order',
            'OrderItem',
            'Payment',
            'Review',
            'Tag',
            'TagCourse',
            'Wishlist'
        ];

        foreach ($models as $model) {
            $this->app->bind("App\Repositories\Interfaces\\" . $model . "Repository",
                "App\Repositories\Eloquents\Db" . $model . "Impl");
        }
    }
}
