<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::orderBy('group')->orderBy('id')->get()->groupBy('group');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
                Cache::forget("setting:{$key}");
            }
        }

        // Handle unchecked checkboxes (booleans default to 0 if missing)
        $booleanSettings = Setting::where('type', 'boolean')->pluck('key');
        foreach ($booleanSettings as $key) {
            if (!array_key_exists($key, $data)) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->update(['value' => '0']);
                    Cache::forget("setting:{$key}");
                }
            }
        }

        return redirect()->route('settings.index')
            ->with('success', 'Settings saved successfully.');
    }
}
