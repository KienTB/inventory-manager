<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->get();

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    public function create()
    {
        Cart::instance('order')
            ->destroy();

        return view('pos.index');
    }

    public function store(OrderStoreRequest $request)
    {
        $cartInstance = $request->get('cart_instance', 'order');

        $order = Order::create($request->all());

        // Create Order Details
        $contents = Cart::instance($cartInstance)->content();
        $oDetails = [];

        foreach ($contents as $content) {
            $oDetails['order_id'] = $order['id'];
            $oDetails['product_id'] = $content->id;
            $oDetails['quantity'] = $content->qty;
            $oDetails['unitcost'] = $content->price;
            $oDetails['total'] = $content->subtotal;
            $oDetails['created_at'] = Carbon::now();

            OrderDetails::insert($oDetails);

            // Giảm tồn kho ngay sau khi tạo chi tiết đơn
            Product::where('id', $content->id)
                ->update(['quantity' => DB::raw('quantity - ' . (int) $content->qty)]);
        }

        // Delete Cart Shopping History for this cart instance
        Cart::instance($cartInstance)->destroy();

        // Xóa discount khỏi session
        session()->forget('discount_' . $cartInstance);

        return redirect()->route('pos.index')->with('success', 'Đã tạo đơn hàng thành công!');
    }

    public function show(Order $order)
    {
        $order->loadMissing(['details']);

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    public function update(Order $order, Request $request)
    {
        // TODO refactoring

        // Reduce the stock
        $products = OrderDetails::where('order_id', $order)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                ->update(['quantity' => DB::raw('quantity-' . $product->quantity)]);
        }

        $order->update([
            'order_status' => OrderStatus::COMPLETE,
        ]);

        return redirect()
            ->route('orders.complete')
            ->with('success', 'Order has been completed!');
    }

    public function destroy(Order $order)
    {
        $order->delete();
    }

    public function downloadInvoice($order)
    {
        $order = Order::with(['details'])
            ->where('id', $order)
            ->firstOrFail();

        return view('orders.print-invoice', [
            'order' => $order,
        ]);
    }
}
