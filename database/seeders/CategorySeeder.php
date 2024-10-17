<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory(50)->create();
    //     $data = [
    //         [
    //             'id' => 1,
    //             'category_name' => 'Phát triển',
    //             'description' => 'Phát triển',
    //             'status' => 1,
    //             'parent_id' => 0
    //         ],
    //         [
    //             'id' => 2,
    //             'category_name' => 'Kinh doanh',
    //             'description' => 'Kinh doanh',
    //             'status' => 1,
    //             'parent_id' => 0
    //         ],
    //         [
    //             'id' => 3,
    //             'category_name' => 'Tài chính & Kế toán',
    //             'description' => 'Tài chính & Kế toán',
    //             'status' => 1,
    //             'parent_id' => 0
    //         ],
    //         [
    //             'id' => 4,
    //             'category_name' => 'CNTT & Phần mềm',
    //             'description' => 'CNTT & Phần mềm',
    //             'status' => 1,
    //             'parent_id' => 0
    //         ],
    //         [
    //             'id' => 5,
    //             'category_name' => 'Phát triển cá nhân',
    //             'description' => 'Phát triển cá nhân',
    //             'status' => 1,
    //             'parent_id' => 0
    //         ],
    //         [
    //             'id' => 6,
    //             'category_name' => 'Thiết kế',
    //             'description' => 'Thiết kế',
    //             'status' => 1,
    //             'parent_id' => 0
    //         ],
    //         [
    //             'id' => 7,
    //             'category_name' => 'Marketing',
    //             'description' => 'Marketing',
    //             'status' => 1,
    //             'parent_id' => 0
    //         ],
    //         [
    //             'id' => 8,
    //             'category_name' => 'Phong cách sống',
    //             'description' => 'Phong cách sống',
    //             'status' => 1,
    //             'parent_id' => 0
    //         ],
    //         [
    //             'id' => 9,
    //             'category_name' => 'Nhiếp ảnh & Video',
    //             'description' => 'Nhiếp ảnh và Video',
    //             'status' => 1,
    //             'parent_id' => 0
    //         ],
    //         [
    //             'id' => 10,
    //             'category_name' => 'Sức khỏe & Thể dục',
    //             'description' => 'Nhiếp ảnh và Video',
    //             'status' => 1,
    //             'parent_id' => 0
    //         ],
    //         [
    //             'id' => 11,
    //             'category_name' => 'Âm nhạc',
    //             'description' => 'Âm nhạc',
    //             'status' => 1,
    //             'parent_id' => 0
    //         ],
    //         [
    //             'id' => 12,
    //             'category_name' => 'Giảng dạy & Học thuật',
    //             'description' => 'Giảng dạy & Học thuật',
    //             'status' => 1,
    //             'parent_id' => 0
    //         ],
    //         [
    //             'id' => 13,
    //             'category_name' => 'Phát triển Web',
    //             'description' => 'Phát triển Web',
    //             'status' => 1,
    //             'parent_id' => 1
    //         ],
    //         [
    //             'id' => 14,
    //             'category_name' => 'Khoa học dữ liệu',
    //             'description' => 'Khoa học dữ liệu',
    //             'status' => 1,
    //             'parent_id' => 1
    //         ],
    //         [
    //             'id' => 15,
    //             'category_name' => 'Phát triển ứng dụng di động',
    //             'description' => 'Phát triển ứng dụng di động',
    //             'status' => 1,
    //             'parent_id' => 1
    //         ],
    //         [
    //             'id' => 16,
    //             'category_name' => 'Ngôn ngữ lập trình',
    //             'description' => 'Ngôn ngữ lập trình',
    //             'status' => 1,
    //             'parent_id' => 1
    //         ],
    //         [
    //             'id' => 17,
    //             'category_name' => 'Thiết kế & Phát triển cơ sở dữ liệu',
    //             'description' => 'Thiết kế & Phát triển cơ sở dữ liệu',
    //             'status' => 1,
    //             'parent_id' => 1
    //         ],
    //         [
    //             'id' => 18,
    //             'category_name' => 'Kiểm tra phần mềm',
    //             'description' => 'Kiểm tra phần mềm',
    //             'status' => 1,
    //             'parent_id' => 1
    //         ],
    //         [
    //             'id' => 19,
    //             'category_name' => 'Kỹ thuật phần mềm',
    //             'description' => 'Kỹ thuật phần mềm',
    //             'status' => 1,
    //             'parent_id' => 1
    //         ],
    //         [
    //             'id' => 20,
    //             'category_name' => 'Tinh thần khởi nghiệp',
    //             'description' => 'Tinh thần khởi nghiệp',
    //             'status' => 1,
    //             'parent_id' => 2
    //         ],
    //         [
    //             'id' => 111,
    //             'category_name' => 'Giao tiếp',
    //             'description' => 'Giao tiếp',
    //             'status' => 1,
    //             'parent_id' => 2
    //         ],
    //         [
    //             'id' => 21,
    //             'category_name' => 'Quản lý',
    //             'description' => 'Quản lý',
    //             'status' => 1,
    //             'parent_id' => 2
    //         ],
    //         [
    //             'id' => 22,
    //             'category_name' => 'Bán hàng',
    //             'description' => 'Bán hàng',
    //             'status' => 1,
    //             'parent_id' => 2
    //         ],
    //         [
    //             'id' => 23,
    //             'category_name' => 'Chiến lược kinh doanh',
    //             'description' => 'Chiến lược kinh doanh',
    //             'status' => 1,
    //             'parent_id' => 2
    //         ],
    //         [
    //             'id' => 24,
    //             'category_name' => 'Hoạt động',
    //             'description' => 'Hoạt động',
    //             'status' => 1,
    //             'parent_id' => 2
    //         ],
    //         [
    //             'id' => 25,
    //             'category_name' => 'Quản lý dự án',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 2
    //         ],
    //         [
    //             'id' => 26,
    //             'category_name' => 'Luật doanh nghiệp',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 2
    //         ],
    //         [
    //             'id' => 27,
    //             'category_name' => 'Nhân sự',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 2
    //         ],
    //         [
    //             'id' => 28,
    //             'category_name' => 'Thương mại điện tử',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 2
    //         ],
    //         [
    //             'id' => 29,
    //             'category_name' => 'Truyền thông',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 2
    //         ],
    //         [
    //             'id' => 30,
    //             'category_name' => 'Bất động sản',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 2
    //         ],
    //         [
    //             'id' => 31,
    //             'category_name' => 'Quản lý rủi ro',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 3
    //         ],
    //         [
    //             'id' => 32,
    //             'category_name' => 'Tiền ảo & Blockchain',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 3
    //         ],
    //         [
    //             'id' => 33,
    //             'category_name' => 'Kinh tế học',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 3
    //         ],
    //         [
    //             'id' => 34,
    //             'category_name' => 'Tài chính',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 3
    //         ],
    //         [
    //             'id' => 35,
    //             'category_name' => 'Đầu tư & Giao dịch',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 3
    //         ],
    //         [
    //             'id' => 36,
    //             'category_name' => 'Thuế',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 3
    //         ],
    //         [
    //             'id' => 37,
    //             'category_name' => 'Chứng chỉ tài chính',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 3
    //         ],
    //         [
    //             'id' => 38,
    //             'category_name' => 'Công cụ quản lý tài chính',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 3
    //         ],
    //         [
    //             'id' => 39,
    //             'category_name' => 'Chứng chỉ CNTT',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 4
    //         ],
    //         [
    //             'id' => 40,
    //             'category_name' => 'Mạng & Bảo mật',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 4
    //         ],
    //         [
    //             'id' => 41,
    //             'category_name' => 'Phần cứng',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 4
    //         ],
    //         [
    //             'id' => 42,
    //             'category_name' => 'Hệ điều hành & Máy chủ',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 4
    //         ],
    //         [
    //             'id' => 43,
    //             'category_name' => 'DevOps',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 4
    //         ],
    //         [
    //             'id' => 44,
    //             'category_name' => 'Chuyển hóa bản thân',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 5
    //         ],
    //         [
    //             'id' => 45,
    //             'category_name' => 'Năng suất cá nhân',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 5
    //         ],
    //         [
    //             'id' => 46,
    //             'category_name' => 'Năng lực lãnh đạo',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 5
    //         ],
    //         [
    //             'id' => 47,
    //             'category_name' => 'Phát triển sự nghiệp',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 5
    //         ],
    //         [
    //             'id' => 48,
    //             'category_name' => 'Tôn giáo & Tâm linh',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 5
    //         ],
    //         [
    //             'id' => 49,
    //             'category_name' => 'Khả năng sáng tạo',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 5
    //         ],
    //         [
    //             'id' => 50,
    //             'category_name' => 'Quản lý học tập & Ghi nhớ',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 5
    //         ],
    //         [
    //             'id' => 51,
    //             'category_name' => 'Gây ảnh hưởng',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 5
    //         ],
    //         [
    //             'id' => 52,
    //             'category_name' => 'Quản lý căng thẳng',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 5
    //         ],
    //         [
    //             'id' => 53,
    //             'category_name' => 'Thiết kế Web',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 6
    //         ],
    //         [
    //             'id' => 54,
    //             'category_name' => 'Thiết kế & Minh họa đồ họa',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 6
    //         ],
    //         [
    //             'id' => 55,
    //             'category_name' => 'Công cụ thiết kế',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 6
    //         ],
    //         [
    //             'id' => 56,
    //             'category_name' => 'Thiết kế trải nghiệm người dùng',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 6
    //         ],
    //         [
    //             'id' => 57,
    //             'category_name' => 'Thiết kế trò chơi',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 6
    //         ],
    //         [
    //             'id' => 58,
    //             'category_name' => '3D & Hoạt hình',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 6
    //         ],
    //         [
    //             'id' => 59,
    //             'category_name' => 'Thiết kế thời trang',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 6
    //         ],
    //         [
    //             'id' => 60,
    //             'category_name' => 'Thiết kế kiến trúc',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 6
    //         ],
    //         [
    //             'id' => 61,
    //             'category_name' => 'Thiết kế nội thất',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 6
    //         ],
    //         [
    //             'id' => 62,
    //             'category_name' => 'Marketing kỹ thuật số',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 7
    //         ],
    //         [
    //             'id' => 63,
    //             'category_name' => 'SEO',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 7
    //         ],
    //         [
    //             'id' => 64,
    //             'category_name' => 'Marketing trên mạng xã hội',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 7
    //         ],
    //         [
    //             'id' => 65,
    //             'category_name' => 'Xây dựng thương hiệu',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 7
    //         ],
    //         [
    //             'id' => 112,
    //             'category_name' => 'Quan hệ công chúng',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 7
    //         ],
    //         [
    //             'id' => 67,
    //             'category_name' => 'Quảng cáo có trả phí',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 7
    //         ],
    //         [
    //             'id' => 68,
    //             'category_name' => 'Quảng cáo qua nội dung',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 7
    //         ],
    //         [
    //             'id' => 69,
    //             'category_name' => 'Marketing liên kết',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 7
    //         ],
    //         [
    //             'id' => 70,
    //             'category_name' => 'Marketing sản phẩm',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 7
    //         ],
    //         [
    //             'id' => 71,
    //             'category_name' => 'Nghê thuật & Đồ thủ công',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 8
    //         ],
    //         [
    //             'id' => 72,
    //             'category_name' => 'Làm đẹp & Trang điểm',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 8
    //         ],
    //         [
    //             'id' => 73,
    //             'category_name' => 'Các phương pháp bí truyền',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 8
    //         ],
    //         [
    //             'id' => 74,
    //             'category_name' => 'Thực phẩm & Đồ uống',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 8
    //         ],
    //         [
    //             'id' => 75,
    //             'category_name' => 'Chơi game',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 8
    //         ],
    //         [
    //             'id' => 76,
    //             'category_name' => 'Cải tạo nhà & Làm vườn',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 8
    //         ],
    //         [
    //             'id' => 77,
    //             'category_name' => 'Chăm sóc & Huấn luyện thú cưng',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 8
    //         ],
    //         [
    //             'id' => 78,
    //             'category_name' => 'Du lịch',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 8
    //         ],
    //         [
    //             'id' => 79,
    //             'category_name' => 'Nhiếp ảnh kỹ thuật số',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 9
    //         ],
    //         [
    //             'id' => 80,
    //             'category_name' => 'Nhiếp ảnh',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 9
    //         ],
    //         [
    //             'id' => 81,
    //             'category_name' => 'Nghệ thuật chụp ảnh chân dung',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 9
    //         ],
    //         [
    //             'id' => 82,
    //             'category_name' => 'Công cụ nhiếp ảnh',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 9
    //         ],
    //         [
    //             'id' => 83,
    //             'category_name' => 'Nhiếp ảnh thương mại',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 9
    //         ],
    //         [
    //             'id' => 84,
    //             'category_name' => 'Thiết kế video',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 9
    //         ],
    //         [
    //             'id' => 85,
    //             'category_name' => 'Thể dục',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 10
    //         ],
    //         [
    //             'id' => 86,
    //             'category_name' => 'Sức khỏe tổng quát',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 10
    //         ],
    //         [
    //             'id' => 87,
    //             'category_name' => 'Thể thao',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 10
    //         ],
    //         [
    //             'id' => 88,
    //             'category_name' => 'Dinh dưỡng & Ăn kiêng',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 10
    //         ],
    //         [
    //             'id' => 89,
    //             'category_name' => 'Yoga',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 10
    //         ],
    //         [
    //             'id' => 90,
    //             'category_name' => 'Sức khỏe tinh thần',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 10
    //         ],
    //         [
    //             'id' => 91,
    //             'category_name' => 'Võ thuật & Tự vệ',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 10
    //         ],
    //         [
    //             'id' => 92,
    //             'category_name' => 'An toàn & Sơ cứu',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 10
    //         ],
    //         [
    //             'id' => 93,
    //             'category_name' => 'Khiêu vũ',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 10
    //         ],
    //         [
    //             'id' => 94,
    //             'category_name' => 'Thiền định',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 10
    //         ],
    //         [
    //             'id' => 95,
    //             'category_name' => 'Nhạc cụ',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 11
    //         ],
    //         [
    //             'id' => 96,
    //             'category_name' => 'Sản xuất nhạc',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 11
    //         ],
    //         [
    //             'id' => 97,
    //             'category_name' => 'Nguyên tắc cơ bản về âm nhạc',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 11
    //         ],
    //         [
    //             'id' => 98,
    //             'category_name' => 'Thanh nhạc',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 11
    //         ],
    //         [
    //             'id' => 99,
    //             'category_name' => 'Kỹ thuật âm nhạc',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 11
    //         ],
    //         [
    //             'id' => 100,
    //             'category_name' => 'Phần mềm âm nhạc',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 11
    //         ],
    //         [
    //             'id' => 101,
    //             'category_name' => 'Kỹ thuật',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 12
    //         ],
    //         [
    //             'id' => 102,
    //             'category_name' => 'Nhân văn',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 12
    //         ],
    //         [
    //             'id' => 103,
    //             'category_name' => 'Toán học',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 12
    //         ],
    //         [
    //             'id' => 104,
    //             'category_name' => 'Khoa học',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 12
    //         ],
    //         [
    //             'id' => 105,
    //             'category_name' => 'Giáo dục online',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 12
    //         ],
    //         [
    //             'id' => 106,
    //             'category_name' => 'Khoa học xã hội',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 12
    //         ],
    //         [
    //             'id' => 107,
    //             'category_name' => 'Học ngôn ngữ',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 12
    //         ],
    //         [
    //             'id' => 108,
    //             'category_name' => 'Đào tạo giảng viên',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 12
    //         ],
    //         [
    //             'id' => 109,
    //             'category_name' => 'Luyện thi',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 12
    //         ],
    //         [
    //             'id' => 110,
    //             'category_name' => 'Đào tạo cử nhân',
    //             'description' => '',
    //             'status' => 1,
    //             'parent_id' => 12
    //         ],
    //     ];
    //     DB::table('categories')->insert($data);

    //     foreach ($data as $category) {
    //         if ($category['id'] < 13)  {
    //             $slug = generateSlugCategory($category['category_name'], null);
    //         } else {
    //             $code = Str::lower(Str::random(5));
    //             $slug = generateSlugCategory($category['category_name'], $code);
    //         }
    //         DB::table('categories')
    //             ->where('id', $category['id'])
    //             ->update(['slug' => $slug, 'description' => $category['category_name']]);

    //     }
    }
}
