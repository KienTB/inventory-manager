@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <x-alert/>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cấu hình cửa hàng</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tên cửa hàng</label>
                                        <input type="text" class="form-control" name="store_name"
                                               value="{{ old('store_name', $settings->store_name) }}"
                                               placeholder="Nhập tên cửa hàng">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email cửa hàng</label>
                                        <input type="email" class="form-control" name="store_email"
                                               value="{{ old('store_email', $settings->store_email) }}"
                                               placeholder="email@cuahang.com">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Số điện thoại</label>
                                        <input type="text" class="form-control" name="store_phone"
                                               value="{{ old('store_phone', $settings->store_phone) }}"
                                               placeholder="0901 234 567">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Website</label>
                                        <input type="url" class="form-control" name="store_website"
                                               value="{{ old('store_website', $settings->store_website) }}"
                                               placeholder="https://cuahang.com">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Địa chỉ cửa hàng</label>
                                <textarea class="form-control" name="store_address" rows="3"
                                          placeholder="123 Đường ABC, Quận 1, TP.HCM">{{ old('store_address', $settings->store_address) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Slogan cửa hàng</label>
                                <input type="text" class="form-control" name="store_slogan"
                                       value="{{ old('store_slogan', $settings->store_slogan) }}"
                                       placeholder="Phục vụ 24/7">
                            </div>

                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/>
                                        <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                        <path d="M14 4l0 4l-6 0l0 -4"/>
                                    </svg>
                                    Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
