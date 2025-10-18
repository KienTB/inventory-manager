<?php

namespace App\Livewire;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CheckoutPanel extends Component
{
    public $listeners = ['tabChanged', 'cartUpdated'];

    public string $cartInstance = 'order';

    public string $payment_method = 'cash'; // cash|bank

    public ?float $received = null; // cash received

    public ?int $customer_id = null;

    public float $discount = 0.0; // giảm giá theo số tiền

    public function mount($cartInstance = 'order', $customer_id = null): void
    {
        $this->cartInstance = $cartInstance;
        $this->customer_id = $customer_id; // optional in new flow

        // Khôi phục discount từ session
        $this->discount = session('discount_' . $cartInstance, 0.0);
    }

    public function tabChanged($newTabId): void
    {
        $this->cartInstance = $newTabId;
        // Khôi phục discount từ session cho tab mới
        $this->discount = session('discount_' . $newTabId, 0.0);
    }

    public function cartUpdated(): void
    {
        // Phương thức này được gọi khi giỏ hàng được cập nhật
        // Livewire sẽ tự động cập nhật các computed properties
        // Không cần làm gì thêm vì các phương thức getSubtotalProperty, getTaxProperty, getTotalProperty sẽ được tính lại
    }

    public function updatedDiscount(): void
    {
        // Lưu discount vào session theo cart instance
        session(['discount_' . $this->cartInstance => $this->discount]);

        // Thông báo cập nhật tổng tiền
        $this->dispatch('cartUpdated');
    }

    public function getSubtotalProperty(): float
    {
        // Tính tổng tạm tính bằng sub_total đã lưu trong options để tránh chuỗi có dấu phẩy
        $sum = 0.0;
        foreach (Cart::instance($this->cartInstance)->content() as $item) {
            $line = $item->options->sub_total ?? (((float) $item->price) * ((int) $item->qty));
            $sum += (float) $line;
        }
        return $sum;
    }

    public function getTaxProperty(): float
    {
        return (float) Cart::instance($this->cartInstance)->tax();
    }

    public function getTotalProperty(): float
    {
        // Tổng cần trả = tổng tạm tính - giảm giá (không thuế, không phí giao hàng)
        $discount = session('discount_' . $this->cartInstance, 0.0);
        $total = max(0, $this->subtotal - (float) $discount);
        return $total;
    }

    public function quickFill($amount): void
    {
        $this->received = (float) $amount;

        // Thông báo cập nhật tổng tiền (để tính tiền thừa)
        $this->dispatch('cartUpdated');
    }

    public function getVietQrUrlProperty(): ?string
    {
        $user = Auth::user();
        if ($this->payment_method !== 'bank' || ! $user) {
            return null;
        }
        if (! $user->bank_bin || ! $user->bank_account_number) {
            return null;
        }
        $amount = max(0, (int) round($this->total));
        $accountName = urlencode($user->name ?? '');
        $info = urlencode('POS Payment');
        return sprintf(
            'https://img.vietqr.io/image/%s-%s-compact.png?amount=%d&addInfo=%s&accountName=%s',
            $user->bank_bin,
            $user->bank_account_number,
            $amount,
            $info,
            $accountName
        );
    }

    public function render()
    {
        return view('livewire.checkout-panel');
    }
}
