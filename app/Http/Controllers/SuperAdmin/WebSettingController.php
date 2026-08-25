<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\WebSetting;
use Illuminate\Http\Request;

class WebSettingController extends Controller
{
    public function index()
    {
        $settings = WebSetting::all()->keyBy('key');
        return view('super-admin.pengaturan.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'contact_persons' => 'required|array',
            'contact_persons.*.name' => 'required|string',
            'contact_persons.*.phone' => 'required|string',
            'settings' => 'nullable|array',
        ]);

        // Proses Contact Persons
        $contactPersons = array_values($request->contact_persons);
        WebSetting::updateOrCreate(
            ['key' => 'contact_persons'],
            ['value' => json_encode($contactPersons)]
        );

        // Proses Settings Biasa
        if ($request->has('settings')) {
            // Handle checkbox (jika tidak dicentang maka tidak ikut di request, kita force set 0 jika tidak ada)
            $activeAnnouncement = isset($request->settings['announcement_active']) ? '1' : '0';
            WebSetting::updateOrCreate(
                ['key' => 'announcement_active'],
                ['value' => $activeAnnouncement]
            );

            foreach ($request->settings as $key => $value) {
                if ($key !== 'announcement_active') {
                    WebSetting::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value]
                    );
                }
            }
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
