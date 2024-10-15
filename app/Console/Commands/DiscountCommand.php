<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\Discount;
use App\Models\DiscountCategory;
use App\Models\Category;
use Carbon\Carbon;

class DiscountCommand extends Command
{
    protected $discount;
    protected $discountCategory;
    protected $category;
    protected $course;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PostDiscount:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to process discounts based on date criteria';

    /**
     * Create a new command instance.
     *
     * @param Discount $discount
     * @param DiscountCategory $discountCategory
     * @param Category $category
     */
    public function __construct(Discount $discount, DiscountCategory $discountCategory, Category $category, Course $course)
    {
        parent::__construct();
        $this->discount = $discount;
        $this->discountCategory = $discountCategory;
        $this->category = $category;
        $this->course = $course;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currentDate = Carbon::now();

        $discountCategories = $this->discountCategory->select(['*'])->get();

        foreach ($discountCategories as $discountCategory) {
            $discount = $this->discount->find($discountCategory->discount_id);
            $category = $this->category->find($discountCategory->category_id);

            if ($discount && $category) {
                $courses = $category->courses;
                if ($discount->start_date <= $currentDate && $discount->end_date >= $currentDate && $discount->status == 'active') {
                    foreach ($courses as $course) {
                        if ($discount->discount_type == 'fixed') {
                            $value = $course->price - $discount->discount_value;
                        } else {
                            $value = $course->price - ($course->price * $discount->discount_value / 100);
                        }

                        $course->update([
                            'discount_id' => $discount->id,
                            'price_sale' => $value
                        ]);
                    }
                } else {
                    foreach ($courses as $course) {
                        if ($discount->discount_type == 'fixed') {
                            $value = $course->price - $discount->discount_value;
                        } else {
                            $value = $course->price - ($course->price * $discount->discount_value / 100);
                        }

                        $course->update([
                            'discount_id' => null,
                            'price_sale' => null
                        ]);
                    }
                }
            }
        }
        return 0;
    }
}
