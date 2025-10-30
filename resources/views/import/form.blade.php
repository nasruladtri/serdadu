<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Impor Data Dukcapil (Satu File .xlsx)
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Impor Dataset Terbaru</h3>
                    <form method="POST" action="{{ route('import.reset') }}"
                          onsubmit="return confirm('Semua tabel hasil impor dan file unggahan akan dihapus. Lanjutkan?');">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded border border-red-600 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50">
                            Bersihkan Data Impor
                        </button>
                    </form>
                </div>

                <hr class="my-4">

                <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Tahun --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Tahun</label>
                            <input type="number" name="year" min="2000" max="2100" required
                                   value="{{ old('year') }}"
                                   class="border rounded w-full p-2">
                            @error('year') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- Semester --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Semester</label>
                            <select name="semester" required class="border rounded w-full p-2">
                                <option value="">-- pilih --</option>
                                <option value="1" {{ old('semester')=='1'?'selected':'' }}>1 (Januari–Juni)</option>
                                <option value="2" {{ old('semester')=='2'?'selected':'' }}>2 (Juli–Desember)</option>
                            </select>
                            @error('semester') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- File --}}
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium mb-1">File Excel (.xlsx)</label>
                            <input type="file" name="file" accept=".xlsx" required class="border rounded w-full p-2" />
                            <p class="text-xs text-gray-500 mt-1">
                                Gunakan file rekap (satu file berisi banyak sheet).
                            </p>
                            @error('file') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <button type="submit"
                            class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                        Unggah & Proses
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
