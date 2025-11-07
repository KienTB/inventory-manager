<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Component;

class SearchProduct extends Component
{
    public $query;

    public $search_results;

    public $how_many;

    public $activeCartInstance = 'order';
    
    protected $listeners = ['productAdded' => 'handleProductAdded'];
    
    public function handleProductAdded()
    {
        // Reset query và kết quả search sau khi thêm sản phẩm
        $this->resetQuery();
    }

    public function mount($activeCartInstance = 'order')
    {
        $this->activeCartInstance = $activeCartInstance;
        $this->query = '';
        $this->how_many = 5;
        $this->search_results = Collection::empty();
    }

    public function updatedActiveCartInstance()
    {
        // Khi cart instance thay đổi, không cần reset component
        // Component sẽ tự động cập nhật qua props
    }

    public function render()
    {
        return view('livewire.search-product');
    }

    public function updatedQuery()
    {
        // Chỉ search nếu query có ít nhất 2 ký tự để tránh query quá nhiều
        if (empty($this->query) || strlen(trim($this->query)) < 2) {
            $this->search_results = Collection::empty();
            return;
        }
        
        $searchTerm = trim($this->query);
        
        // Tối ưu query: sử dụng where với callback để nhóm conditions
        // Điều này giúp sử dụng index tốt hơn
        $this->search_results = Product::where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%')
                  ->orWhere('code', 'like', '%'.$searchTerm.'%');
            })
            ->when(strlen($searchTerm) >= 3, function($q) use ($searchTerm) {
                // Chỉ search theo category nếu query đủ dài (>= 3 ký tự)
                // để tránh query phức tạp khi query ngắn
                $q->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                    $categoryQuery->where('name', 'like', '%'.$searchTerm.'%');
                });
            })
            ->select('id', 'name', 'code', 'selling_price', 'quantity', 'category_id', 'unit_id')
            ->with('category:id,name', 'unit:id,name') // Eager load để tránh N+1 query
            ->take($this->how_many)
            ->get();
    }

    public function loadMore()
    {
        $this->how_many += 5;
        $this->updatedQuery();
    }

    public function resetQuery()
    {
        $this->query = '';
        $this->how_many = 5;
        $this->search_results = Collection::empty();
    }

    public function selectProduct($product)
    {
        $this->dispatch('productSelected', $product);
    }
}
