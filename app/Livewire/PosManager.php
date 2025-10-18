<?php

namespace App\Livewire;

use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;

class PosManager extends Component
{
    public $activeTab = 'invoice-1';
    public $tabs = [];
    public $nextTabId = 2;

    public function mount()
    {
        // Khởi tạo tab đầu tiên nếu chưa có
        if (empty($this->tabs)) {
            $this->tabs = ['invoice-1'];
        }

        // Đặt tab đầu tiên làm active
        if (empty($this->activeTab) && !empty($this->tabs)) {
            $this->activeTab = $this->tabs[0];
        }
    }

    public function createNewTab()
    {
        $newTabId = 'invoice-' . $this->nextTabId;
        $this->tabs[] = $newTabId;
        $this->activeTab = $newTabId;
        $this->nextTabId++;

        // Khởi tạo giỏ hàng mới cho tab này
        $this->initializeCartForTab($newTabId);

        // Thông báo cho các component con biết tab đã thay đổi
        $this->dispatch('tabChanged', $newTabId);
    }

    public function switchToTab($tabId)
    {
        if (in_array($tabId, $this->tabs)) {
            $this->activeTab = $tabId;
            // Thông báo cho các component con biết tab đã thay đổi
            $this->dispatch('tabChanged', $tabId);
        }
    }

    public function closeTab($tabId)
    {
        if (count($this->tabs) > 1) {
            // Xóa tab khỏi danh sách
            $this->tabs = array_filter($this->tabs, fn($tab) => $tab !== $tabId);

            // Nếu tab đang active bị đóng, chuyển sang tab đầu tiên còn lại
            if ($this->activeTab === $tabId) {
                $this->activeTab = $this->tabs[0];
            }

            // Xóa giỏ hàng của tab đã đóng
            $this->destroyCartForTab($tabId);
        }
    }

    public function getActiveCartInstanceProperty()
    {
        return $this->activeTab;
    }

    public function getCartItems()
    {
        return Cart::instance($this->activeTab)->content();
    }

    public function getCartSubtotal()
    {
        $subtotal = 0;
        foreach (Cart::instance($this->activeTab)->content() as $item) {
            $subtotal += $item->options->sub_total ?? ($item->price * $item->qty);
        }
        return $subtotal;
    }

    public function getCartTotal()
    {
        $cart = Cart::instance($this->activeTab);
        return max(0, $this->getCartSubtotal() - $this->getCartDiscount());
    }

    public function getCartDiscount()
    {
        // Lấy discount từ session hoặc trả về 0
        return session('discount_' . $this->activeTab, 0);
    }

    public function setCartDiscount($discount)
    {
        session(['discount_' . $this->activeTab => $discount]);
    }

    private function initializeCartForTab($tabId)
    {
        // Khởi tạo giỏ hàng mới nếu chưa tồn tại
        if (!Cart::instance($tabId)->count()) {
            Cart::instance($tabId)->destroy();
        }
    }

    private function destroyCartForTab($tabId)
    {
        Cart::instance($tabId)->destroy();
        // Xóa discount khỏi session
        session()->forget('discount_' . $tabId);
    }

    public function refreshTabTotals()
    {
        // Phương thức này được gọi bởi wire:poll để refresh giao diện
        // Không cần làm gì đặc biệt vì Livewire tự động cập nhật view
    }

    public function render()
    {
        $tabTotals = [];
        foreach ($this->tabs as $tab) {
            $total = 0;
            $itemCount = 0;
            foreach (Cart::instance($tab)->content() as $item) {
                $itemCount++;
                $total += $item->options->sub_total ?? ($item->price * $item->qty);
            }
            $total = max(0, $total - session('discount_' . $tab, 0));
            $tabTotals[$tab] = [
                'total' => $total,
                'itemCount' => $itemCount
            ];
        }

        return view('livewire.pos-manager', [
            'activeCartItems' => $this->getCartItems(),
            'activeCartSubtotal' => $this->getCartSubtotal(),
            'activeCartTotal' => $this->getCartTotal(),
            'tabTotals' => $tabTotals,
        ]);
    }
}