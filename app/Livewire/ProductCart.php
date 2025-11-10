<?php

namespace App\Livewire;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;

class ProductCart extends Component
{
    public $listeners = ['productSelected', 'discountModalRefresh', 'tabChanged'];

    /**
     * The cart instance name (e.g., 'sale', 'purchase', etc.)
     * This is the main property that should be used to reference the cart instance
     *
     * @var string
     */
    public $cartInstanceName = 'order';

    /**
     * @deprecated Use $cartInstanceName instead
     * @var string
     */
    public $cart_instance;

    /**
     * @deprecated Use $cartInstanceName instead
     * @var string
     */
    public $activeCartInstance = 'order';
    
    /**
     * The Livewire component instance
     *
     * @var \Livewire\Component
     */
    protected $component;

    public $global_discount;

    public $global_tax;

    public $shipping;

    public $quantity;

    public $check_quantity;

    public $discount_type;

    public $item_discount;

    public $unit_price;

    public $data;

    /**
     * Mount the component.
     *
     * @param string $cartInstance The cart instance name (e.g., 'sale', 'purchase')
     * @param mixed $data Optional data to initialize the cart
     * @return void
     */
    public function mount($cartInstance = 'order', $data = null): void
    {
        try {
            // Initialize cart instance name
            $this->cartInstanceName = $cartInstance;
            
            // For backward compatibility with legacy code
            $this->cart_instance = $this->cartInstanceName;
            $this->activeCartInstance = $this->cartInstanceName;

            // Initialize cart instance
            Cart::instance($this->cartInstanceName);
            
            // Ensure the cart is properly initialized
            if (!isset($this->cartInstanceName)) {
                $this->cartInstanceName = 'order';
            }
        } catch (\Exception $e) {
            \Log::error('Error initializing cart: ' . $e->getMessage());
            $this->cartInstanceName = 'order';
            Cart::instance($this->cartInstanceName);
        }

        // Load data if provided
        if ($data) {
            $this->data = $data;

            // Set global values from data
            $this->global_discount = $data->discount_percentage ?? 0;
            $this->global_tax = $data->tax_percentage ?? 0;
            $this->shipping = $data->shipping_amount ?? 0;

            // Update tax calculations
            $this->updatedGlobalTax();
            $this->updatedGlobalDiscount();

            $cart_items = Cart::instance($this->cart_instance)->content();

            foreach ($cart_items as $cart_item) {
                $this->check_quantity[$cart_item->id] = [$cart_item->options->stock];
                $this->quantity[$cart_item->id] = $cart_item->qty;
                $this->unit_price[$cart_item->id] = $cart_item->price;
                $this->discount_type[$cart_item->id] = $cart_item->options->product_discount_type;

                if ($cart_item->options->product_discount_type == 'fixed') {
                    $this->item_discount[$cart_item->id] = $cart_item->options->product_discount;
                } elseif ($cart_item->options->product_discount_type == 'percentage') {
                    $this->item_discount[$cart_item->id] = round(100 * ($cart_item->options->product_discount / $cart_item->price));
                }
            }
        } else {
            $this->global_discount = 0;
            $this->global_tax = 0;
            $this->shipping = 0.00;
            $this->check_quantity = [];
            $this->quantity = [];
            $this->unit_price = [];
            $this->discount_type = [];
            $this->item_discount = [];
        }
    }

    public function updatedCartInstance()
    {
        // When cartInstance changes from the parent component
        if ($this->cartInstance && $this->cartInstance !== $this->cartInstanceName) {
            $this->cartInstanceName = $this->cartInstance;
$this->cart_instance = $this->cartInstance; // For backward compatibility
            $this->activeCartInstance = $this->cartInstance; // For backward compatibility
            $this->cartInstanceName = $this->cartInstance; // Keep the main property in sync
            
            // Reinitialize cart with the new instance name
            if (!Cart::instance($this->cartInstanceName)->initialized ?? true) {
                Cart::instance($this->cartInstanceName);
            }
            
            $this->syncCartData();
        }
    }

    public function tabChanged($newTabId): void
    {
        $this->cartInstanceName = $newTabId; // Main property
        $this->cart_instance = $newTabId; // Legacy
        $this->activeCartInstance = $newTabId; // Legacy
        $this->cartInstance = $newTabId; // For Livewire property binding
        $this->syncCartData();
    }

    public function syncCartData(): void
    {
        $cart_items = Cart::instance($this->cart_instance)->content();

        // Khởi tạo lại các mảng thuộc tính
        $this->check_quantity = [];
        $this->quantity = [];
        $this->unit_price = [];
        $this->discount_type = [];
        $this->item_discount = [];

        foreach ($cart_items as $cart_item) {
            $this->check_quantity[$cart_item->id] = [$cart_item->options->stock];
            $this->quantity[$cart_item->id] = $cart_item->qty;
            $this->unit_price[$cart_item->id] = $cart_item->price;
            $this->discount_type[$cart_item->id] = $cart_item->options->product_discount_type;

            if ($cart_item->options->product_discount_type == 'fixed') {
                $this->item_discount[$cart_item->id] = $cart_item->options->product_discount;
            } elseif ($cart_item->options->product_discount_type == 'percentage') {
                $this->item_discount[$cart_item->id] = round(100 * ($cart_item->options->product_discount / $cart_item->price));
            }
        }
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        try {
            // Ensure we have a valid cart instance
            $cartInstance = $this->cartInstanceName ?? 'order';
            
            // Initialize cart instance
            $cart = Cart::instance($cartInstance);
            
            // Get cart items
            $cart_items = $cart->content();

            return view('livewire.product-cart', [
                'cart_items' => $cart_items,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error in ProductCart render: ' . $e->getMessage());
            
            // Return an empty cart view if there's an error
            return view('livewire.product-cart', [
                'cart_items' => collect(),
            ]);
        }
    }

    public function productSelected($product): void
    {
        $cart = Cart::instance($this->cart_instance);

        $exists = $cart->search(function ($cartItem, $rowId) use ($product) {
            return $cartItem->id == $product['id'];
        });

        if ($exists->isNotEmpty()) {
            session()->flash('message', 'Product exists in the cart!');

            return;
        }

        $this->product = $product;

        $cart->add([
            'id' => $product['id'],
            'name' => $product['name'],
            'qty' => 1,
            'price' => $this->calculate($product)['price'],
            'weight' => 1,
            'options' => [
                'product_discount' => 0.00,
                'product_discount_type' => 'fixed',
                'sub_total' => $this->calculate($product)['sub_total'],
                'code' => $product['code'],
                'stock' => $product['quantity'],
                //'unit'                  => $product['product_unit'],
                'unit' => $product['unit_id'],
                'product_tax' => $this->calculate($product)['tax'],
                'unit_price' => $this->calculate($product)['unit_price'],
            ],
        ]);

        $this->check_quantity[$product['id']] = $product['quantity'];
        $this->quantity[$product['id']] = 1;
        $this->discount_type[$product['id']] = 'fixed';
        $this->item_discount[$product['id']] = 0;

        // Thông báo cập nhật tổng tiền
        $this->dispatch('cartUpdated');
        
        // Thông báo để reset search query
        $this->dispatch('productAdded');
    }

    public function removeItem($row_id): void
    {
        Cart::instance($this->cart_instance)->remove($row_id);

        // Đồng bộ lại dữ liệu sau khi xóa sản phẩm
        $this->syncCartData();

        // Thông báo cho các component khác cập nhật tổng tiền
        $this->dispatch('cartUpdated');
    }

    public function updatedGlobalTax(): void
    {
        Cart::instance($this->cart_instance)->setGlobalTax((int) $this->global_tax);

        // Thông báo cập nhật tổng tiền
        $this->dispatch('cartUpdated');
    }

    public function updatedGlobalDiscount(): void
    {
        Cart::instance($this->cart_instance)->setGlobalDiscount((int) $this->global_discount);

        // Thông báo cập nhật tổng tiền
        $this->dispatch('cartUpdated');
    }

    public function updateQuantity($row_id, $product_id): void
    {
        if ($this->cart_instance == 'sale' || $this->cart_instance == 'purchase_return' || $this->cart_instance == 'quotation') {
            if ($this->check_quantity[$product_id] < $this->quantity[$product_id]) {
                session()->flash('message', 'The requested quantity is not available in stock.');

                return;
            }
        }

        Cart::instance($this->cart_instance)->update($row_id, $this->quantity[$product_id]);

        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        Cart::instance($this->cart_instance)->update($row_id, [
            'options' => [
                'sub_total' => $cart_item->price * $cart_item->qty,
                'code' => $cart_item->options->code,
                'stock' => $cart_item->options->stock,
                'unit' => $cart_item->options->unit,
                'product_tax' => $cart_item->options->product_tax,
                'unit_price' => $cart_item->options->unit_price,
                'product_discount' => $cart_item->options->product_discount,
                'product_discount_type' => $cart_item->options->product_discount_type,
            ],
        ]);

        // Thông báo cập nhật tổng tiền
        $this->dispatch('cartUpdated');
    }

    public function updatedDiscountType($value, $name): void
    {
        $this->item_discount[$name] = 0;

        // Thông báo cập nhật tổng tiền
        $this->dispatch('cartUpdated');
    }

    public function discountModalRefresh($product_id, $row_id): void
    {
        $this->updateQuantity($row_id, $product_id);

        // Khởi tạo discount type mặc định nếu chưa có
        if (!isset($this->discount_type[$product_id])) {
            $this->discount_type[$product_id] = 'fixed';
            $this->item_discount[$product_id] = 0;
        }

        // Thông báo cập nhật tổng tiền
        $this->dispatch('cartUpdated');
    }

    public function setProductDiscount($row_id, $product_id): void
    {
        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        if ($this->discount_type[$product_id] == 'fixed') {
            Cart::instance($this->cart_instance)
                ->update($row_id, [
                    'price' => ($cart_item->price + $cart_item->options->product_discount) - $this->item_discount[$product_id],
                ]);

            $discount_amount = $this->item_discount[$product_id];

            $this->updateCartOptions($row_id, $product_id, $cart_item, $discount_amount);
        } elseif ($this->discount_type[$product_id] == 'percentage') {
            $discount_amount = ($cart_item->price + $cart_item->options->product_discount) * ($this->item_discount[$product_id] / 100);

            Cart::instance($this->cart_instance)
                ->update($row_id, [
                    'price' => ($cart_item->price + $cart_item->options->product_discount) - $discount_amount,
                ]);

            $this->updateCartOptions($row_id, $product_id, $cart_item, $discount_amount);
        }

        session()->flash('discount_message'.$product_id, 'Discount added to the product!');

        // Thông báo cập nhật tổng tiền
        $this->dispatch('cartUpdated');
    }

    public function updatePrice($row_id, $product_id): void
    {
        $product = Product::findOrFail($product_id);

        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        Cart::instance($this->cart_instance)->update($row_id, ['price' => $this->unit_price[$product['id']]]);

        Cart::instance($this->cart_instance)->update($row_id, [
            'options' => [
                'sub_total' => $this->calculate($product, $this->unit_price[$product['id']])['sub_total'],
                'code' => $cart_item->options->code,
                'stock' => $cart_item->options->stock,
                'unit' => $cart_item->options->unit,
                //                'product_tax'           => $this->calculate($product, $this->unit_price[$product['id']])['product_tax'],
                'product_tax' => $this->calculate($product, $this->unit_price[$product['id']])['tax'],
                'unit_price' => $this->calculate($product, $this->unit_price[$product['id']])['unit_price'],
                'product_discount' => $cart_item->options->product_discount,
                'product_discount_type' => $cart_item->options->product_discount_type,
            ],
        ]);

        // Thông báo cập nhật tổng tiền
        $this->dispatch('cartUpdated');
    }

    public function calculate($product, $new_price = null): array
    {
        if ($new_price) {
            $product_price = $new_price;
        } else {
            $this->unit_price[$product['id']] = $product['selling_price'];

            if ($this->cart_instance == 'purchase' || $this->cart_instance == 'purchase_return') {
                $this->unit_price[$product['id']] = $product['product_cost'];
            }

            $product_price = $this->unit_price[$product['id']];
        }

        $price = 0;
        $unit_price = 0;
        $product_tax = 0;
        $sub_total = 0;

        // ✅ Nếu sản phẩm có thuế thì cộng thêm phần trăm thuế, nếu không thì giữ nguyên
        $tax_rate = $product['tax'] ?? 0;
        if ($tax_rate > 0) {
            $product_tax = $product_price * ($tax_rate / 100);
            $price = $product_price + $product_tax;
            $unit_price = $product_price;
            $sub_total = $price;
        } else {
            $price = $product_price;
            $unit_price = $product_price;
            $product_tax = 0.00;
            $sub_total = $product_price;
        }

        return [
            'price' => $price,
            'unit_price' => $unit_price,
            'tax' => $product_tax,
            'sub_total' => $sub_total,
        ];
    }


    public function updateCartOptions($row_id, $product_id, $cart_item, $discount_amount): void
    {
        Cart::instance($this->cart_instance)->update($row_id, ['options' => [
            'sub_total' => $cart_item->price * $cart_item->qty,
            'code' => $cart_item->options->code,
            'stock' => $cart_item->options->stock,
            'unit' => $cart_item->options->unit,
            'product_tax' => $cart_item->options->product_tax,
            'unit_price' => $cart_item->options->unit_price,
            'product_discount' => $discount_amount,
            'product_discount_type' => $this->discount_type[$product_id],
        ]]);

        // Thông báo cập nhật tổng tiền
        $this->dispatch('cartUpdated');
    }
}
