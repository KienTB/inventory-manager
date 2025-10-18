<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'nullable',
            'cart_instance' => 'nullable|string',
            'payment_type' => 'required',
            'pay' => 'required|numeric',
        ];
    }

    public function prepareForValidation(): void
    {
        $cartInstance = $this->get('cart_instance', 'order');
        $cart = Cart::instance($cartInstance);

        // Build numeric subtotal from cart content to avoid formatted strings
        $subtotal = 0.0;
        foreach ($cart->content() as $item) {
            $line = $item->options->sub_total ?? (((float) $item->price) * ((int) $item->qty));
            $subtotal += (float) $line;
        }

        // Áp dụng giảm giá từ session
        $discount = session('discount_' . $cartInstance, 0.0);
        $total = max(0, $subtotal - (float) $discount);

        // No VAT/shipping in POS new flow; keep 0 for consistency
        $vat = 0.0;

        // Default pay to total if not provided
        $pay = is_null($this->pay) || $this->pay === '' ? $total : (float) $this->pay;

        $due = max(0, $total - $pay);
        $status = $due <= 0 ? OrderStatus::COMPLETE->value : OrderStatus::PENDING->value;

        $this->merge([
            'order_date' => Carbon::now()->format('Y-m-d'),
            'order_status' => $status,
            'total_products' => $cart->count(),
            'sub_total' => $subtotal,
            'vat' => $vat,
            'total' => $total,
            'invoice_no' => IdGenerator::generate([
                'table' => 'orders',
                'field' => 'invoice_no',
                'length' => 10,
                'prefix' => 'INV-',
            ]),
            'pay' => $pay,
            'due' => $due,
        ]);
    }
}
