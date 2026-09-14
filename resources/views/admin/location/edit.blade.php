@extends('layouts.app')
@section('content')
<div class="max-w-[600px] mx-auto school-panel p-6 sm:p-8">
<h1 class="school-display text-xl font-bold mb-6">Lokasi Sekolah</h1>
@if(session('ok'))<div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-2 text-sm text-green-700">{{ session('ok') }}</div>@endif
<form method="POST" action="{{ route('admin.location.update') }}" class="space-y-4">@csrf @method('PUT')
<div><label class="text-sm font-semibold">Nama Sekolah</label><input name="name" value="{{ old('name',$school->name) }}" required class="mt-1 w-full rounded-xl border border-school-line px-3 py-2.5 text-sm"></div>
<div><label class="text-sm font-semibold">Alamat</label><input name="address" value="{{ old('address',$school->address) }}" class="mt-1 w-full rounded-xl border border-school-line px-3 py-2.5 text-sm"></div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4"><div><label class="text-sm font-semibold">Latitude</label><input name="latitude" type="number" step="0.0000001" value="{{ old('latitude',$school->latitude) }}" required class="mt-1 w-full rounded-xl border px-3 py-2.5 text-sm"></div><div><label class="text-sm font-semibold">Longitude</label><input name="longitude" type="number" step="0.0000001" value="{{ old('longitude',$school->longitude) }}" required class="mt-1 w-full rounded-xl border px-3 py-2.5 text-sm"></div></div>
<div><label class="text-sm font-semibold">Radius (meter)</label><input name="radius_meters" type="number" value="{{ old('radius_meters',$school->radius_meters) }}" required class="mt-1 w-full rounded-xl border px-3 py-2.5 text-sm"></div>
<button class="school-button school-button-primary w-full">Simpan Lokasi</button>
</form>
</div>
@endsection
