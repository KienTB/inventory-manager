<?php

namespace App\Livewire;

use Livewire\Component;

class PaymentSuccessModal extends Component
{
    public $orderId = null;
    public $orderData = null;
    
    protected $listeners = ['showPaymentSuccess' => 'display'];
    
    public function display($orderId)
    {
        try {
            if (is_array($orderId)) {
                $orderId = $orderId['orderId'] ?? $orderId['order_id'] ?? null;
            }
            
            if ($orderId) {
                // Chỉ lưu orderId, load order data trong render để tránh snapshot issues
                $this->orderId = $orderId;
            }
        } catch (\Exception $e) {
            \Log::error('PaymentSuccessModal display error: ' . $e->getMessage());
        }
    }
    
    public function close()
    {
        $this->orderId = null;
        $this->orderData = null;
        // Dispatch event để refresh PosManager
        $this->dispatch('refreshPosManager');
    }
    
    public function continueSelling()
    {
        // Dispatch event trước khi đóng
        $this->dispatch('continueSelling');
        
        // Đóng modal
        $this->close();
    }
    
    public function render()
    {
        // Load order data nếu có orderId
        $order = null;
        if ($this->orderId) {
            try {
                $order = \App\Models\Order::with(['details', 'customer'])->find($this->orderId);
            } catch (\Exception $e) {
                // Bỏ qua lỗi
            }
        }
        
        return view('livewire.payment-success-modal', [
            'order' => $order,
            'show' => $order !== null,
        ]);
    }
}
