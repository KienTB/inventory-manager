<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductExportController extends Controller
{
    public function create(Request $request)
    {
        try {
            $query = Product::with(['category', 'unit']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('code', 'like', '%' . $search . '%')
                      ->orWhere('product_code', 'like', '%' . $search . '%');
                });
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            if ($request->filled('brand')) {
                $brand = $request->brand;
                $query->where('brand', 'like', '%' . $brand . '%');
            }

            if ($request->filled('price_min')) {
                $query->where('selling_price', '>=', $request->price_min);
            }

            if ($request->filled('price_max')) {
                $query->where('selling_price', '<=', $request->price_max);
            }

            $products = $query->latest()->get();

            if ($products->isEmpty()) {
                return redirect()->route('products.index')
                    ->with('warning', 'Không có sản phẩm nào phù hợp để export!');
            }

            $filename = 'products_export_' . now()->format('d-m-Y_H-i-s') . '.xls';

            header("Content-Type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Pragma: no-cache");
            header("Expires: 0");

            echo "<table border='1' cellspacing='0' cellpadding='5'>";
            echo "<thead>
                    <tr style='background-color:#f2f2f2;'>
                        <th>ID</th>
                        <th>Tên sản phẩm</th>
                        <th>Mã sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Thương hiệu</th>
                        <th>Giá bán</th>
                        <th>Số lượng</th>
                    </tr>
                  </thead>
                  <tbody>";

            foreach ($products as $p) {
                echo "<tr>
                        <td>{$p->id}</td>
                        <td>{$p->name}</td>
                        <td>{$p->code}</td>
                        <td>" . ($p->category->name ?? '-') . "</td>
                        <td>{$p->brand}</td>
                        <td>" . number_format($p->selling_price, 0, ',', '.') . "</td>
                        <td>{$p->quantity}</td>
                      </tr>";
            }

            echo "</tbody></table>";
            exit; 

        } catch (\Exception $e) {
            return redirect()->route('products.index')
                ->with('error', 'Lỗi khi export: ' . $e->getMessage());
        }
    }
}