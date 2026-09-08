@extends('layouts.app')
@section('title', 'Business Settings')
@section('page_title', 'System Settings')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">⚙️ Business Settings</div>
        <div class="page-subtitle">Configure your business profile, invoices, and financial defaults</div>
    </div>
</div>

<form method="POST" action="{{ route('settings.update') }}">
@csrf @method('POST')

@foreach($settings as $group => $groupSettings)
<div class="card" style="margin-bottom:24px;max-width:780px;">
    <div class="card-header">
        <span class="card-title">
            @if($group === 'general')   🏢 General Information
            @elseif($group === 'invoice') 🧾 Invoice Settings
            @elseif($group === 'finance') 💰 Financial Settings
            @else {{ ucfirst($group) }}
            @endif
        </span>
    </div>
    <div class="card-body">
        @foreach($groupSettings as $setting)
        <div class="form-group" style="display:grid;grid-template-columns:220px 1fr;gap:16px;align-items:start;padding:12px 0;border-bottom:1px solid var(--gray-100);">
            <div>
                <label class="form-label" for="setting_{{ $setting['key'] }}" style="margin-bottom:2px;">
                    {{ $setting['label'] }}
                </label>
                @if($setting['description'])
                    <div style="font-size:11px;color:var(--gray-400);margin-top:2px;line-height:1.4;">{{ $setting['description'] }}</div>
                @endif
            </div>
            <div>
                @if($setting['type'] === 'textarea')
                    <textarea name="{{ $setting['key'] }}" id="setting_{{ $setting['key'] }}" class="form-control"
                              rows="3">{{ old($setting['key'], $setting['value']) }}</textarea>
                @elseif($setting['type'] === 'boolean')
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-top:4px;">
                        <input type="checkbox"
                               name="{{ $setting['key'] }}"
                               id="setting_{{ $setting['key'] }}"
                               value="1"
                               style="width:16px;height:16px;accent-color:var(--primary);"
                               {{ old($setting['key'], $setting['value']) == '1' ? 'checked' : '' }}>
                        <span style="font-size:13px;">Enabled</span>
                    </label>
                @elseif($setting['type'] === 'number')
                    <input type="number" name="{{ $setting['key'] }}" id="setting_{{ $setting['key'] }}"
                           class="form-control" value="{{ old($setting['key'], $setting['value']) }}"
                           min="0" step="0.01">
                @elseif($setting['type'] === 'email')
                    <input type="email" name="{{ $setting['key'] }}" id="setting_{{ $setting['key'] }}"
                           class="form-control" value="{{ old($setting['key'], $setting['value']) }}">
                @else
                    <input type="text" name="{{ $setting['key'] }}" id="setting_{{ $setting['key'] }}"
                           class="form-control" value="{{ old($setting['key'], $setting['value']) }}">
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach

<div style="max-width:780px;">
    <button type="submit" class="btn btn-primary" style="padding:12px 32px;font-size:15px;">
        💾 Save All Settings
    </button>
</div>

</form>

@endsection
