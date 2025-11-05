<?php

namespace App\Livewire;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PosManager extends Component
{
    public $activeTab = 'invoice-1';
    public $tabs = [];
    public $nextTabId = 2;
    
    // Payment Success Modal properties
    public $showPaymentSuccessModal = false;
    public $successOrderId = null;
    
    public $listeners = [
        'continueSelling' => 'handleContinueSelling',
        'refreshPosManager' => 'refreshAfterPayment',
        'showPaymentSuccess' => 'handleShowPaymentSuccess'
    ];
    
    public function refreshAfterPayment()
    {
        // Refresh tabs sau khi thanh toán
        $this->refreshTabTotals();
    }
    
    public function handleShowPaymentSuccess($orderId)
    {
        try {
            if (is_array($orderId)) {
                $orderId = $orderId['orderId'] ?? $orderId['order_id'] ?? null;
            }
            
            if ($orderId) {
                $this->successOrderId = $orderId;
                $this->showPaymentSuccessModal = true;
            }
        } catch (\Exception $e) {
            \Log::error('PosManager handleShowPaymentSuccess error: ' . $e->getMessage());
        }
    }
    
    public function closePaymentSuccessModal()
    {
        $this->showPaymentSuccessModal = false;
        $this->successOrderId = null;
        $this->refreshTabTotals();
    }
    
    public function continueSellingFromModal()
    {
        $this->dispatch('continueSelling');
        $this->closePaymentSuccessModal();
    }

    public function mount()
    {
        try {
            // Ưu tiên 1: Khôi phục từ session trước (nhanh hơn và an toàn hơn)
            $savedTabs = session('pos_tabs', []);
            $allTabsWithItems = [];
            
            // Kiểm tra các tabs đã lưu trong session
            foreach ($savedTabs as $tab) {
                try {
                    if (Cart::instance($tab)->count() > 0) {
                        $allTabsWithItems[] = $tab;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            // Ưu tiên 2: Nếu không tìm thấy từ session, quét trực tiếp
            // Giới hạn quét để tránh snapshot timeout issues
            if (empty($allTabsWithItems)) {
                $scanned = $this->scanForCartInstances(50);
                foreach ($scanned as $tab) {
                    if (!in_array($tab, $allTabsWithItems)) {
                        $allTabsWithItems[] = $tab;
                    }
                }
            }
            
            // Ưu tiên 3: Query từ database để bổ sung (backup)
            $dbTabs = $this->findAllCartInstancesWithItems();
            foreach ($dbTabs as $tab) {
                if (!in_array($tab, $allTabsWithItems)) {
                    $allTabsWithItems[] = $tab;
                }
            }
            
            $validTabs = $allTabsWithItems;
            
            if (!empty($validTabs)) {
                // Sắp xếp tabs theo thứ tự số
                usort($validTabs, function($a, $b) {
                    preg_match('/invoice-(\d+)/', $a, $matchesA);
                    preg_match('/invoice-(\d+)/', $b, $matchesB);
                    $numA = isset($matchesA[1]) ? (int)$matchesA[1] : 0;
                    $numB = isset($matchesB[1]) ? (int)$matchesB[1] : 0;
                    return $numA <=> $numB;
                });
                
                $this->tabs = $validTabs;
                
                // Khôi phục activeTab từ session nếu có và hợp lệ
                $savedActiveTab = session('pos_active_tab', null);
                if ($savedActiveTab && in_array($savedActiveTab, $validTabs)) {
                    $this->activeTab = $savedActiveTab;
                } else {
                    $this->activeTab = $validTabs[0];
                }
                
                // Cập nhật nextTabId dựa trên tabs hiện có
                $maxId = 1;
                foreach ($validTabs as $tab) {
                    if (preg_match('/invoice-(\d+)/', $tab, $matches)) {
                        $maxId = max($maxId, (int)$matches[1]);
                    }
                }
                $this->nextTabId = $maxId + 1;
            } else {
                // Khởi tạo tab đầu tiên nếu chưa có
                if (empty($this->tabs)) {
                    $this->tabs = ['invoice-1'];
                }
                $this->activeTab = $this->tabs[0];
                $this->nextTabId = 2;
            }

            // Đặt tab đầu tiên làm active nếu chưa có
            if (empty($this->activeTab) && !empty($this->tabs)) {
                $this->activeTab = $this->tabs[0];
            }
        } catch (\Exception $e) {
            // Nếu có lỗi, khởi tạo tab mặc định
            if (empty($this->tabs)) {
                $this->tabs = ['invoice-1'];
            }
            $this->activeTab = $this->tabs[0];
            $this->nextTabId = 2;
        }
        
        // Lưu trạng thái vào session sau khi mount hoàn tất
        // Sử dụng $this->skipRender() để tránh gây snapshot issues
        try {
            $this->saveTabsToSession();
        } catch (\Exception $e) {
            // Bỏ qua nếu có lỗi khi lưu session
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

        // Lưu tabs vào session ngay lập tức
        $this->saveTabsToSession();

        // Thông báo cho các component con biết tab đã thay đổi
        $this->dispatch('tabChanged', $newTabId);
    }
    
    public function updatedTabs()
    {
        // Tự động lưu vào session mỗi khi tabs thay đổi
        // Sử dụng skipRender để tránh re-render không cần thiết
        $this->skipRender();
        $this->saveTabsToSession();
    }

    public function switchToTab($tabId)
    {
        // Nếu tab chưa có trong danh sách, thêm vào trước
        if (!in_array($tabId, $this->tabs)) {
            try {
                // Kiểm tra xem tab có hàng không
                if (Cart::instance($tabId)->count() > 0) {
                    $this->tabs[] = $tabId;
                    // Sắp xếp lại tabs
                    usort($this->tabs, function($a, $b) {
                        preg_match('/invoice-(\d+)/', $a, $matchesA);
                        preg_match('/invoice-(\d+)/', $b, $matchesB);
                        $numA = isset($matchesA[1]) ? (int)$matchesA[1] : 0;
                        $numB = isset($matchesB[1]) ? (int)$matchesB[1] : 0;
                        return $numA <=> $numB;
                    });
                }
            } catch (\Exception $e) {
                // Bỏ qua nếu có lỗi
            }
        }
        
        // Chuyển sang tab đã chọn
        if (in_array($tabId, $this->tabs)) {
            $this->activeTab = $tabId;
            // Lưu tabs và activeTab vào session ngay lập tức
            $this->saveTabsToSession();
            // Thông báo cho các component con biết tab đã thay đổi
            $this->dispatch('tabChanged', $tabId);
        }
    }

    public function closeTab($tabId)
    {
        if (count($this->tabs) > 1) {
            // Xóa tab khỏi danh sách
            $this->tabs = array_values(array_filter($this->tabs, fn($tab) => $tab !== $tabId));

            // Nếu tab đang active bị đóng, chuyển sang tab đầu tiên còn lại
            if ($this->activeTab === $tabId) {
                $this->activeTab = $this->tabs[0];
            }

            // Xóa giỏ hàng của tab đã đóng
            $this->destroyCartForTab($tabId);
            
            // Lưu tabs vào session
            $this->saveTabsToSession();
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
    
    private function findAllCartInstancesWithItems()
    {
        // Query database để tìm tất cả cart instances có hàng
        // Cart được lưu trong bảng shoppingcart với cột 'instance'
        $tableName = config('cart.database.table', 'shoppingcart');
        $validInstances = [];
        
        try {
            // Lấy tất cả identifiers có thể có của user hiện tại
            $identifiers = [];
            if (Auth::check()) {
                $identifiers[] = (string) Auth::id();
            }
            $identifiers[] = session()->getId();
            
            // Query instances từ mỗi identifier
            foreach ($identifiers as $identifier) {
                $instances = DB::table($tableName)
                    ->where('identifier', $identifier)
                    ->where('instance', 'like', 'invoice-%')
                    ->distinct()
                    ->pluck('instance')
                    ->toArray();
                
                foreach ($instances as $instance) {
                    if (!in_array($instance, $validInstances)) {
                        try {
                            $count = Cart::instance($instance)->count();
                            if ($count > 0) {
                                $validInstances[] = $instance;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            }
            
            // Nếu vẫn không tìm thấy đủ, query tất cả invoice instances từ database
            if (count($validInstances) <= 1) {
                $allInstances = DB::table($tableName)
                    ->where('instance', 'like', 'invoice-%')
                    ->distinct()
                    ->pluck('instance')
                    ->unique()
                    ->toArray();
                
                foreach ($allInstances as $instance) {
                    if (!in_array($instance, $validInstances)) {
                        try {
                            $count = Cart::instance($instance)->count();
                            if ($count > 0) {
                                $validInstances[] = $instance;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            }
            
            return $validInstances;
        } catch (\Exception $e) {
            // Nếu có lỗi với database, trả về mảng rỗng
            return [];
        }
    }
    
    private function scanForCartInstances($maxScan = 100)
    {
        // Quét trực tiếp các cart instances bằng cách thử load chúng
        // Đây là cách đáng tin cậy nhất vì shopping cart library tự động
        // restore carts từ database khi gọi Cart::instance()
        $validInstances = [];
        
        for ($i = 1; $i <= $maxScan; $i++) {
            $tabId = 'invoice-' . $i;
            try {
                // Shopping cart library sẽ tự động load từ database nếu có
                $cart = Cart::instance($tabId);
                $count = $cart->count();
                if ($count > 0) {
                    $validInstances[] = $tabId;
                }
            } catch (\Exception $e) {
                // Bỏ qua nếu có lỗi (cart không tồn tại hoặc không thể load)
                continue;
            }
        }
        
        return $validInstances;
    }
    
    private function saveTabsToSession()
    {
        // Lưu tabs có hàng vào session
        $tabsWithItems = [];
        foreach ($this->tabs as $tab) {
            try {
                if (Cart::instance($tab)->count() > 0) {
                    $tabsWithItems[] = $tab;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        session([
            'pos_tabs' => $tabsWithItems,
            'pos_active_tab' => $this->activeTab, // Lưu tab đang active
        ]);
    }

    public function refreshTabTotals()
    {
        // Kiểm tra tabs hiện tại và loại bỏ các tabs đã trống
        $tabsWithItems = [];
        foreach ($this->tabs as $tab) {
            try {
                if (Cart::instance($tab)->count() > 0) {
                    $tabsWithItems[] = $tab;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // Nếu chỉ có 1 tab hoặc ít hơn, quét thêm các tabs khác có hàng
        if (count($tabsWithItems) <= 1) {
            $scannedTabs = $this->scanForCartInstances(50);
            foreach ($scannedTabs as $tab) {
                if (!in_array($tab, $tabsWithItems)) {
                    $tabsWithItems[] = $tab;
                }
            }
            
            // Sắp xếp lại
            if (count($tabsWithItems) > 1) {
                usort($tabsWithItems, function($a, $b) {
                    preg_match('/invoice-(\d+)/', $a, $matchesA);
                    preg_match('/invoice-(\d+)/', $b, $matchesB);
                    $numA = isset($matchesA[1]) ? (int)$matchesA[1] : 0;
                    $numB = isset($matchesB[1]) ? (int)$matchesB[1] : 0;
                    return $numA <=> $numB;
                });
            }
        }
        
        // Đảm bảo luôn có ít nhất 1 tab
        if (empty($tabsWithItems)) {
            $tabsWithItems = ['invoice-1'];
            $this->nextTabId = 2;
        }
        
        // Cập nhật tabs nếu có thay đổi
        if (count($tabsWithItems) !== count($this->tabs) || $tabsWithItems !== array_values($this->tabs)) {
            $this->tabs = $tabsWithItems;
            
            // Cập nhật nextTabId
            $maxId = 1;
            foreach ($tabsWithItems as $tab) {
                if (preg_match('/invoice-(\d+)/', $tab, $matches)) {
                    $maxId = max($maxId, (int)$matches[1]);
                }
            }
            $this->nextTabId = $maxId + 1;
            
            // Nếu tab active hiện tại không còn hàng, chuyển sang tab đầu tiên
            if (!in_array($this->activeTab, $tabsWithItems)) {
                $this->activeTab = $tabsWithItems[0];
                $this->dispatch('tabChanged', $this->activeTab);
            }
            
            // Lưu vào session (bao gồm cả activeTab)
            $this->saveTabsToSession();
        } else {
            // Vẫn lưu activeTab vào session ngay cả khi không có thay đổi tabs
            // để đảm bảo trạng thái được giữ nguyên
            session(['pos_active_tab' => $this->activeTab]);
        }
    }

    public function render()
    {
        $tabTotals = [];
        
        // Tính toán totals cho các tabs hiện tại
        foreach ($this->tabs as $tab) {
            $total = 0;
            $itemCount = 0;
            try {
                foreach (Cart::instance($tab)->content() as $item) {
                    $itemCount++;
                    $total += $item->options->sub_total ?? ($item->price * $item->qty);
                }
                $total = max(0, $total - session('discount_' . $tab, 0));
                $tabTotals[$tab] = [
                    'total' => $total,
                    'itemCount' => $itemCount
                ];
            } catch (\Exception $e) {
                $tabTotals[$tab] = [
                    'total' => 0,
                    'itemCount' => 0
                ];
            }
        }
        
        // Tìm thêm các tabs khác có hàng từ session để hiển thị đầy đủ
        // (không quét lại để tránh chậm khi render)
        $savedTabs = session('pos_tabs', []);
        foreach ($savedTabs as $tab) {
            if (!in_array($tab, $this->tabs) && !isset($tabTotals[$tab])) {
                // Tính toán cho tab này
                $total = 0;
                $itemCount = 0;
                try {
                    if (Cart::instance($tab)->count() > 0) {
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
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        // Load order data nếu modal đang hiển thị
        $successOrder = null;
        if ($this->showPaymentSuccessModal && $this->successOrderId) {
            try {
                $successOrder = \App\Models\Order::with(['details', 'customer'])->find($this->successOrderId);
            } catch (\Exception $e) {
                // Bỏ qua lỗi
            }
        }

        return view('livewire.pos-manager', [
            'activeCartItems' => $this->getCartItems(),
            'activeCartSubtotal' => $this->getCartSubtotal(),
            'activeCartTotal' => $this->getCartTotal(),
            'tabTotals' => $tabTotals,
            'allPendingInvoices' => $this->getAllPendingInvoices($tabTotals),
            'successOrder' => $successOrder,
        ]);
    }
    
    private function getAllPendingInvoices($tabTotals)
    {
        // Lấy tất cả tabs (bao gồm cả tabs có hàng và tabs chưa có hàng)
        $pendingInvoices = [];
        
        // Thêm tất cả tabs hiện tại (kể cả chưa có sản phẩm)
        foreach ($this->tabs as $tab) {
            $info = $tabTotals[$tab] ?? ['total' => 0, 'itemCount' => 0];
            preg_match('/invoice-(\d+)/', $tab, $matches);
            $num = isset($matches[1]) ? (int)$matches[1] : 0;
            $pendingInvoices[] = [
                'tab' => $tab,
                'number' => $num,
                'itemCount' => $info['itemCount'],
                'total' => $info['total'],
                'isActive' => $this->activeTab === $tab,
                'isInTabs' => true,
            ];
        }
        
        // Thêm các tabs khác có hàng nhưng chưa có trong tabs
        foreach ($tabTotals as $tab => $info) {
            if ($info['itemCount'] > 0 && !in_array($tab, $this->tabs)) {
                preg_match('/invoice-(\d+)/', $tab, $matches);
                $num = isset($matches[1]) ? (int)$matches[1] : 0;
                $pendingInvoices[] = [
                    'tab' => $tab,
                    'number' => $num,
                    'itemCount' => $info['itemCount'],
                    'total' => $info['total'],
                    'isActive' => false,
                    'isInTabs' => false,
                ];
            }
        }
        
        // Loại bỏ trùng lặp
        $uniqueInvoices = [];
        $seenTabs = [];
        foreach ($pendingInvoices as $invoice) {
            if (!in_array($invoice['tab'], $seenTabs)) {
                $uniqueInvoices[] = $invoice;
                $seenTabs[] = $invoice['tab'];
            }
        }
        
        // Sắp xếp theo số
        usort($uniqueInvoices, fn($a, $b) => $a['number'] <=> $b['number']);
        
        return $uniqueInvoices;
    }
    
    public function handleContinueSelling()
    {
        // Tìm hóa đơn tiếp theo có hàng
        $nextTab = null;
        
        // Loại bỏ tab hiện tại khỏi danh sách
        $remainingTabs = array_filter($this->tabs, fn($tab) => $tab !== $this->activeTab);
        $remainingTabs = array_values($remainingTabs);
        
        // Tìm tab đầu tiên có hàng
        foreach ($remainingTabs as $tab) {
            try {
                if (Cart::instance($tab)->count() > 0) {
                    $nextTab = $tab;
                    break;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // Nếu không có tab nào có hàng, quét thêm các tabs khác
        if (!$nextTab) {
            $scannedTabs = $this->scanForCartInstances(50);
            foreach ($scannedTabs as $tab) {
                if ($tab !== $this->activeTab && Cart::instance($tab)->count() > 0) {
                    $nextTab = $tab;
                    break;
                }
            }
        }
        
        // Nếu tìm thấy tab tiếp theo, chuyển sang
        if ($nextTab) {
            $this->switchToTab($nextTab);
        } else {
            // Nếu không có hóa đơn nào khác, tạo hóa đơn mới
            $this->createNewTab();
        }
    }
}