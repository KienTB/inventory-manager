@extends('layouts.tabler')

@section('content')
    <div class="page-body">
        @if($quotations->isEmpty())
            <x-empty
                title="Không tìm thấy báo giá nào"
                message="Hãy thử điều chỉnh tìm kiếm hoặc bộ lọc để tìm nội dung bạn cần."
                button_label="{{ __('Thêm báo giá đầu tiên của bạn') }}"
                button_route="{{ route('quotations.create') }}"
            />
        @else
            <div class="container-xl">
                <x-alert/>

                @livewire('tables.quotation-table')
            </div>
        @endif
    </div>
@endsection
