@extends('layouts.tabler')

@section('content')
<div class="page-body">
    @if($orders->isEmpty())
    <x-empty
        title="Không tìm thấy đơn hàng nào"
        message="Hãy thử điều chỉnh bộ lọc hoặc tìm kiếm để tìm đơn hàng bạn muốn."
        button_label="Thêm đơn hàng đầu tiên"
        button_route="{{ route('orders.create') }}"
    />
    @else
    <div class="container-xl">
        <x-alert/>
        <livewire:tables.order-table />
    </div>
    @endif
</div>
@endsection
