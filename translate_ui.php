<?php

/**
 * Laravel UI Translation Automation Script
 * Scans Blade templates and auto-translates to Vietnamese
 */

class UITranslator
{
    private $translations = [];
    private $processedFiles = 0;

    public function run()
    {
        echo "🔄 Starting UI Translation Automation...\n\n";

        // Scan all Blade files
        $this->scanBladeFiles();

        // Generate translation file
        $this->generateTranslationFile();

        // Apply translations to all files
        $this->applyTranslations();

        echo "\n✅ Translation completed!\n";
        echo "📊 Processed {$this->processedFiles} files\n";
        echo "🌐 Generated " . count($this->translations) . " translation keys\n";
    }

    private function scanBladeFiles()
    {
        $bladeFiles = $this->getAllBladeFiles();

        foreach ($bladeFiles as $file) {
            echo "📄 Scanning: " . basename($file) . "\n";
            $content = file_get_contents($file);

            // Extract strings that need translation
            $patterns = [
                // Text in quotes: "Hello World"
                '/(["\'])(.*?)\1/',

                // Blade directives with text
                '/@lang\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/',

                // HTML attributes with text
                '/(title|placeholder|alt|value)=["\']([^"\']+)["\']/',

                // Form labels and buttons
                '/>([^<]+)</',

                // Vue/Alpine directives
                '/x-text=["\']([^"\']+)["\']/',
                '/:placeholder=["\']([^"\']+)["\']/',
            ];

            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $text = trim($match[count($match) - 1]);
                        if ($this->isTranslatable($text)) {
                            $key = $this->generateKey($text);
                            $this->translations[$key] = $this->translateToVietnamese($text);
                        }
                    }
                }
            }

            $this->processedFiles++;
        }
    }

    private function getAllBladeFiles()
    {
        $files = [];
        $directories = [
            'resources/views',
            'resources/views/auth',
            'resources/views/categories',
            'resources/views/components',
            'resources/views/customers',
            'resources/views/layouts',
            'resources/views/livewire',
            'resources/views/orders',
            'resources/views/products',
            'resources/views/profile',
            'resources/views/purchases',
            'resources/views/quotations',
            'resources/views/suppliers',
            'resources/views/units',
            'resources/views/users',
        ];

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir)
                );

                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getExtension() === 'php') {
                        $files[] = $file->getPathname();
                    }
                }
            }
        }

        return $files;
    }

    private function isTranslatable($text)
    {
        // Skip if too short, contains only symbols, or is already a translation key
        return strlen($text) > 2
            && !preg_match('/^[0-9\s\W]+$/', $text)
            && !preg_match('/^ui\./', $text)
            && !preg_match('/^auth\./', $text)
            && !preg_match('/^validation\./', $text);
    }

    private function generateKey($text)
    {
        // Create a simple key from the text
        $key = strtolower($text);
        $key = preg_replace('/[^a-z0-9\s]/', '', $key);
        $key = preg_replace('/\s+/', '_', trim($key));
        $key = substr($key, 0, 50); // Limit length

        // Ensure uniqueness
        $originalKey = $key;
        $counter = 1;
        while (isset($this->translations[$key])) {
            $key = $originalKey . '_' . $counter;
            $counter++;
        }

        return $key;
    }

    private function translateToVietnamese($text)
    {
        // Simple translation mapping (expand as needed)
        $translations = [
            'Dashboard' => 'Bảng điều khiển',
            'Products' => 'Sản phẩm',
            'Orders' => 'Đơn hàng',
            'Customers' => 'Khách hàng',
            'Suppliers' => 'Nhà cung cấp',
            'Categories' => 'Danh mục',
            'Units' => 'Đơn vị',
            'Users' => 'Người dùng',
            'Settings' => 'Cài đặt',
            'Profile' => 'Hồ sơ',
            'Login' => 'Đăng nhập',
            'Register' => 'Đăng ký',
            'Logout' => 'Đăng xuất',
            'Name' => 'Tên',
            'Email' => 'Email',
            'Phone' => 'Số điện thoại',
            'Address' => 'Địa chỉ',
            'Description' => 'Mô tả',
            'Price' => 'Giá',
            'Quantity' => 'Số lượng',
            'Total' => 'Tổng cộng',
            'Subtotal' => 'Tổng tạm tính',
            'Tax' => 'Thuế',
            'Discount' => 'Giảm giá',
            'Shipping' => 'Phí giao hàng',
            'Status' => 'Trạng thái',
            'Date' => 'Ngày',
            'Action' => 'Thao tác',
            'Create' => 'Tạo',
            'Edit' => 'Sửa',
            'Delete' => 'Xóa',
            'Save' => 'Lưu',
            'Cancel' => 'Hủy',
            'Update' => 'Cập nhật',
            'Search' => 'Tìm kiếm',
            'Filter' => 'Lọc',
            'Export' => 'Xuất',
            'Import' => 'Nhập',
            'Download' => 'Tải xuống',
            'Upload' => 'Tải lên',
            'Loading' => 'Đang tải',
            'No data' => 'Không có dữ liệu',
            'No results' => 'Không có kết quả',
            'Confirm delete' => 'Xác nhận xóa',
            'Success' => 'Thành công',
            'Error' => 'Lỗi',
            'Warning' => 'Cảnh báo',
            'Info' => 'Thông tin',
            'Please select' => 'Vui lòng chọn',
            'Please enter' => 'Vui lòng nhập',
            'Required' => 'Bắt buộc',
            'Optional' => 'Tùy chọn',
        ];

        return $translations[$text] ?? $this->autoTranslate($text);
    }

    private function autoTranslate($text)
    {
        // For demo purposes, return Vietnamese equivalent
        // In production, you'd use a proper translation service
        $vietnameseWords = [
            'product' => 'sản phẩm',
            'order' => 'đơn hàng',
            'customer' => 'khách hàng',
            'supplier' => 'nhà cung cấp',
            'category' => 'danh mục',
            'unit' => 'đơn vị',
            'user' => 'người dùng',
            'setting' => 'cài đặt',
            'profile' => 'hồ sơ',
            'dashboard' => 'bảng điều khiển',
            'create' => 'tạo',
            'edit' => 'sửa',
            'delete' => 'xóa',
            'save' => 'lưu',
            'cancel' => 'hủy',
            'update' => 'cập nhật',
            'search' => 'tìm kiếm',
            'filter' => 'lọc',
            'export' => 'xuất',
            'import' => 'nhập',
            'download' => 'tải xuống',
            'upload' => 'tải lên',
            'loading' => 'đang tải',
            'success' => 'thành công',
            'error' => 'lỗi',
            'warning' => 'cảnh báo',
            'info' => 'thông tin',
        ];

        $words = explode(' ', strtolower($text));
        $translated = [];

        foreach ($words as $word) {
            $translated[] = $vietnameseWords[$word] ?? $word;
        }

        return ucfirst(implode(' ', $translated));
    }

    private function generateTranslationFile()
    {
        $content = "<?php\n\nreturn [\n\n";

        foreach ($this->translations as $key => $value) {
            $content .= "    '{$key}' => '{$value}',\n";
        }

        $content .= "\n];\n";

        file_put_contents('resources/lang/vi/ui.php', $content);
        echo "📝 Generated translation file: resources/lang/vi/ui.php\n";
    }

    private function applyTranslations()
    {
        // For demo, we'll just show what would be replaced
        // In a real implementation, you'd do the actual replacements
        echo "🔄 Would apply " . count($this->translations) . " translations to Blade files...\n";
    }
}

// Run the translator
$translator = new UITranslator();
$translator->run();

?>
