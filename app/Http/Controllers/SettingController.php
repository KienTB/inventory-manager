<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::getSettings();

        return view('settings.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'nullable|string|max:255',
            'store_email' => 'nullable|email|max:255',
            'store_phone' => 'nullable|string|max:20',
            'store_address' => 'nullable|string',
            'store_website' => 'nullable|url|max:255',
            'store_slogan' => 'nullable|string|max:255',
        ]);

        $settings = Setting::first();

        if ($settings) {
            $settings->update($request->all());
        } else {
            $settings = Setting::create($request->all());
        }

        // Clear cache
        cache()->forget('settings');

        return redirect()->back()->with('success', 'Cập nhật thông tin cửa hàng thành công!');
    }
}
