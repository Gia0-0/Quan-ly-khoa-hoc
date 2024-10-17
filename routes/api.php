<?php

use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CommentController as ClientCommentController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Client\ReviewController as ClientReviewController;
use App\Http\Controllers\Client\WishlistController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Client\CourseClientController;
use App\Http\Controllers\Admin\CourseInfoController;
use App\Http\Controllers\Admin\DiscountCategoryController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Client\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Client\NotificationController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Root API
Route::group(["prefix" => "v1"], function () {

    // ======= PUBLIC API =======
    // Viết Public API từ đây (Public API: Là những API KHÔNG YÊU CẦU xác thực người dùng để trả về dữ liệu)
    Route::group(["prefix" => "auth"], function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post("register", "register")->name("register");
            Route::post("login", "login");
            Route::get("forgot-password", "forgotPassword");
            Route::post("reset-password", "resetPassword");
            Route::post("refresh-token", "refreshToken");
            Route::post("verify-account", "verifyAccount");
            Route::post("create-verify-token", "createVerifyToken");
        });
    });
    Route::group(["prefix" => "client"], function () {
        Route::controller(CategoryController::class)->group(function () {
            Route::get("categories/get-all", "getAllCategoryActive");
            Route::get("categories/get-params", "getParams");
            Route::get("categories/get-name-by-slug/{slug}", "getCategoryNameBySlug");
            Route::get("categories/get-categories-by-parent-id/{parentId}", "getListCategoryByParentId");
            Route::get("categories/get-categories-children", "getAllCategoryChildren");
        });
        Route::controller(CourseClientController::class)->group(function () {
            Route::get("courses/get-all", "index");
            Route::get("courses/get-detail/{slug}", "show");
            Route::get("course/get-preview-video/{id}", "getPreview");
        });
        Route::controller(DiscountCategoryController::class)->group(function () {
            // Route::apiResource("courses", OrderController::class)->names([
            //     "show" => "courses.getDetail",
            //     "index" => "getAll",
            // ])->except("store");
            Route::post("discount_category/create/{id}", "store");
            Route::delete("courses/delete/{id}", "destroy")->name("courses.delete");
        });
    });

    // ======= PRIVATE API =======
    Route::group(["middleware" => ["auth:api"]], function () {
        // Viết Private API từ đây (Private API: Là những API YÊU CẦU xác thực người dùng để trả về dữ liệu)
        Route::group(["prefix" => "auth"], function () {
            Route::controller(AuthController::class)->group(function () {
                Route::get("profile", "profile");
                Route::post("logout", "logout");
                Route::post("change-password", "changePassword");
            });
        });

        Route::group(["prefix" => "admin"], function () {
            Route::controller(CategoryController::class)->group(function () {
                Route::get("categories/get-all", "getAllCategoryActive");
                Route::post("categories/create", "postCreateCategory");
                //        Route::put("categories/update/{id}", "putUpdateCategory");
                Route::put("categories/update", "putUpdateCategory");
                Route::delete("categories/delete/{id}", "deleteCategory");
                Route::get("categories/get-params", "getParams");
            });

            Route::controller(CourseController::class)->group(function () {
                // Route::apiResource(name: "courses", CourseController::class)->names([
                //     "show" => "courses.getDetail",
                //     "index" => "getAll",
                // ])->except("store");
                Route::get("courses", "index");
                Route::post("courses/create", "store")->name("courses.create");
                Route::delete("courses/delete/{id}", "destroy")->name("courses.delete");
            });

            Route::controller(ChapterController::class)->group(function () {
                Route::put("chapters/update/{id}", "update")->name("chapters.update");
                Route::delete("chapters/delete/{id}", "delete")->name("chapters.delete");
            });

            Route::controller(LessonController::class)->group(function () {
                Route::put("lessons/update/{id}", "update")->name("lessons.update");
                Route::delete("lessons/delete/{id}", "delete")->name("lessons.delete");
                Route::post("lessons/create", "store")->name("lessons.create");
            });

            Route::controller(CourseInfoController::class)->group(function () {
                Route::put("course_infos/update/{id}", "update")->name("course_infos.update");
                Route::delete("course_infos/delete/{id}", "delete")->name("course_infos.delete");
            });

            Route::controller(AdminCommentController::class)->group(function () {
                Route::get("");
                Route::get("");
                Route::post("");
                Route::put("");
                Route::delete("");
            });
        });
        Route::group(["prefix" => "client"], function () {
            Route::controller(WishlistController::class)->group(function () {
                Route::get("wishlists", "index");
                Route::post("wishlists/add", "store");
                Route::delete("wishlists/delete/{id}", "delete");
            });
            Route::controller(CartController::class)->group(function () {
                Route::get("carts", "index");
                Route::post("carts/add", "store");
                Route::delete("carts/delete/{id}", "delete");
            });
            Route::controller(ClientReviewController::class)->group(function () {
                Route::post("reviews/create", "store");
                Route::put("reviews/update", "update");
                Route::delete("review/delete/{id}", "delete");
            });
            Route::controller(PaymentController::class)->group(function () {
                Route::get("payment/MomoPay", "createPaymentMomo");
            });
            Route::controller(NotificationController::class)->group(function () {
                Route::get("notifications", "getNotification");
            });
            Route::controller(ClientCommentController::class)->group(function () {
                Route::get("comments", "index");
                Route::get("comments/detail/{parentId}", "detail");
                Route::post("comments/create", "create");
                Route::put("comments/update/{id}", "update");
                Route::delete("comments/delete/{id}", "delete");
            });
        });
        Route::group(["prefix" => "file"], function () {
            Route::controller(ImageController::class)->group(function () {
                Route::post("upload_image", "uploadImage");
                Route::delete("remove_image", "removeImage");
            });
        });
        // Route::group(["prefix" => "notice"], function () {
            // Route::controller(NotificationController::class)->group(function () {
            //     Route::get("notification", "getNotification");
            // });
        // });
    });
});
