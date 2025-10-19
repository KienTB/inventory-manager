<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductImportController extends Controller
{
    public function import(Request $request)
    {
        // ✅ Kiểm tra có file upload không
        if (!$request->hasFile('file')) {
            return redirect()->back()->with('error', 'Vui lòng chọn file để import!');
        }

        $file = $request->file('file');

        // ✅ Kiểm tra định dạng file
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['csv', 'xls', 'xlsx'])) {
            return redirect()->back()->with('error', 'Chỉ chấp nhận file Excel (.csv, .xls, .xlsx)');
        }

        // ✅ Đọc nội dung file CSV
        $filePath = $file->getRealPath();
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle); // Bỏ qua dòng tiêu đề đầu tiên
        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            // Giả định file có các cột:
            // Tên sản phẩm | Mã | Danh mục ID | Thương hiệu | Giá bán | Số lượng
            $name = $row[0] ?? null;
            $code = $row[1] ?? null;
            $category_id = $row[2] ?? null;
            $brand = $row[3] ?? null;
            $price = $row[4] ?? 0;
            $quantity = $row[5] ?? 0;

            if (!$name || !$code) {
                continue; // Bỏ qua dòng thiếu dữ liệu
            }

            // ✅ Sinh slug tự động
            $slug = Str::slug($name, '-');

            // ✅ Gán giá trị mặc định cho các trường không có trong file
            $buying_price = $price;           // giả sử giá nhập = giá bán
            $quantity_alert = 10;             // cảnh báo khi tồn < 10
            $unit_id = 1;                     // đơn vị mặc định
            $location = 'Kho chính';          // vị trí lưu kho mặc định
            $commission = 0;                  // hoa hồng mặc định

            // ✅ Ghi log để debug nếu cần
            Log::info("Importing product: $code - $name");

            // ✅ Tạo hoặc cập nhật sản phẩm
            Product::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'slug' => $slug,
                    'category_id' => $category_id,
                    'brand' => $brand,
                    'selling_price' => $price,
                    'buying_price' => $buying_price,
                    'quantity' => $quantity,
                    'quantity_alert' => $quantity_alert,
                    'unit_id' => $unit_id,
                    'location' => $location,
                    'commission' => $commission,
                ]
            );

            $count++;
        }

        fclose($handle);

        return redirect()->route('products.index')->with('success', "Import thành công {$count} sản phẩm!");
    }
}