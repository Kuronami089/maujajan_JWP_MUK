<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class MakeCrudCommand extends Command
{
    /**
     * Nama perintah artisan:
     * Contoh penggunaan: php artisan make:crud Event
     *                    php artisan make:crud Konser
     */
    protected $signature = 'make:crud {name : Nama model/CRUD (misal: Event, Konser, Kategori)}';

    /**
     * Deskripsi perintah artisan
     */
    protected $description = 'Generate otomatis Model, Migration, Controller, Views (Blade), dan Route untuk CRUD';

    public function handle()
    {
        $rawName = trim($this->argument('name'));
        $name = Str::studly(Str::singular($rawName)); // Contoh: Event, Konser
        $table = Str::snake(Str::pluralStudly($name)); // Contoh: events, konsers
        $viewFolder = Str::plural(Str::kebab($name));   // Contoh: events, konsers
        $varPlural = Str::camel(Str::pluralStudly($name)); // Contoh: events, konsers
        $varSingle = Str::camel($name);                 // Contoh: event, konser
        $controllerName = $name . 'Controller';

        $this->info("🚀 Memulai pembuatan CRUD untuk: {$name}...");

        // 1. Buat Model
        $this->createModel($name);

        // 2. Buat Migration
        $this->createMigration($table);

        // 3. Buat Controller
        $this->createController($name, $controllerName, $viewFolder, $varPlural, $varSingle);

        // 4. Buat Views (index, create, edit)
        $this->createViews($name, $viewFolder, $varPlural, $varSingle);

        // 5. Daftarkan Route ke routes/web.php
        $this->appendRoute($controllerName, $viewFolder);

        $this->newLine();
        $this->info("✅ BERHASIL! CRUD untuk {$name} sudah siap 100%!");
        $this->line("📍 URL Akses: http://localhost:8000/admin/{$viewFolder}");
        $this->comment("👉 Jangan lupa cek migration di database/migrations/ lalu jalankan: php artisan migrate");
    }

    protected function createModel($name)
    {
        $path = app_path("Models/{$name}.php");
        if (File::exists($path)) {
            $this->warn("⚠️  Model {$name}.php sudah ada, dilewati.");
            return;
        }

        $template = <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    use HasFactory;

    // Menjaga ID, kolom lain otomatis diizinkan untuk diisi
    protected \$guarded = ['id'];
}
PHP;

        File::put($path, $template);
        $this->info("✔ Model dibuat: app/Models/{$name}.php");
    }

    protected function createMigration($table)
    {
        Artisan::call('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table
        ]);
        $this->info("✔ Migration dibuat untuk tabel: {$table}");
    }

    protected function createController($name, $controllerName, $viewFolder, $varPlural, $varSingle)
    {
        $path = app_path("Http/Controllers/{$controllerName}.php");
        if (File::exists($path)) {
            $this->warn("⚠️  Controller {$controllerName}.php sudah ada, dilewati.");
            return;
        }

        $template = <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\\{$name};
use Illuminate\Http\Request;

class {$controllerName} extends Controller
{
    // Menampilkan daftar data
    public function index()
    {
        \${$varPlural} = {$name}::latest()->paginate(10);
        return view('admin.{$viewFolder}.index', compact('{$varPlural}'));
    }

    // Menampilkan form tambah data
    public function create()
    {
        return view('admin.{$viewFolder}.create');
    }

    // Menyimpan data baru
    public function store(Request \$request)
    {
        \$request->validate([
            'nama' => 'required|string|max:255',
            // Tambahkan validasi kolom lainnya di sini jika ada
        ]);

        {$name}::create(\$request->all());

        return redirect()->route('{$viewFolder}.index')->with('success', 'Data {$name} berhasil ditambahkan!');
    }

    public function show({$name} \${$varSingle})
    {
        //
    }

    // Menampilkan form edit data
    public function edit({$name} \${$varSingle})
    {
        return view('admin.{$viewFolder}.edit', compact('{$varSingle}'));
    }

    // Menyimpan perubahan data
    public function update(Request \$request, {$name} \${$varSingle})
    {
        \$request->validate([
            'nama' => 'required|string|max:255',
            // Tambahkan validasi kolom lainnya di sini jika ada
        ]);

        \${$varSingle}->update(\$request->all());

        return redirect()->route('{$viewFolder}.index')->with('success', 'Data {$name} berhasil diperbarui!');
    }

    // Menghapus data
    public function destroy({$name} \${$varSingle})
    {
        \${$varSingle}->delete();
        return redirect()->route('{$viewFolder}.index')->with('success', 'Data {$name} berhasil dihapus!');
    }
}
PHP;

        File::put($path, $template);
        $this->info("✔ Controller dibuat: app/Http/Controllers/{$controllerName}.php");
    }

    protected function createViews($name, $viewFolder, $varPlural, $varSingle)
    {
        $dir = resource_path("views/admin/{$viewFolder}");
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        // 1. Index View
        $indexTemplate = <<<BLADE
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Data {$name}</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('{$viewFolder}.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mb-4 inline-block font-semibold">+ Tambah {$name}</a>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <table class="w-full bg-white border mt-4 shadow-sm rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-100 border-b text-gray-600 text-sm">
                    <th class="p-3 text-left">#</th>
                    <th class="p-3 text-left">Nama</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse(\${$varPlural} as \$item)
                <tr class="border-b text-center text-sm">
                    <td class="p-3 text-left">{{ \$loop->iteration }}</td>
                    <td class="p-3 text-left font-semibold">{{ \$item->nama ?? \$item->name ?? '-' }}</td>
                    <td class="p-3">
                        <a href="{{ route('{$viewFolder}.edit', \$item->id) }}" class="text-blue-600 hover:underline mr-3 font-semibold">Edit</a>
                        <form action="{{ route('{$viewFolder}.destroy', \$item->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Yakin hapus data ini?')" class="text-red-600 hover:underline font-semibold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="p-4 text-center text-gray-500">Belum ada data {$name}.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ \${$varPlural}->links() }}</div>
    </div>
</x-app-layout>
BLADE;
        File::put("{$dir}/index.blade.php", $indexTemplate);

        // 2. Create View
        $createTemplate = <<<BLADE
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah {$name}</h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('{$viewFolder}.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-sm border">
            @csrf
            <div class="mb-4">
                <label class="block font-medium mb-1">Nama {$name}</label>
                <input type="text" name="nama" class="w-full border rounded p-2" required>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded font-semibold">Simpan Data</button>
                <a href="{{ route('{$viewFolder}.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded font-semibold">Kembali</a>
            </div>
        </form>
    </div>
</x-app-layout>
BLADE;
        File::put("{$dir}/create.blade.php", $createTemplate);

        // 3. Edit View
        $editTemplate = <<<BLADE
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit {$name}</h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('{$viewFolder}.update', \${$varSingle}->id) }}" method="POST" class="bg-white p-6 rounded-lg shadow-sm border">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block font-medium mb-1">Nama {$name}</label>
                <input type="text" name="nama" value="{{ old('nama', \${$varSingle}->nama ?? \${$varSingle}->name) }}" class="w-full border rounded p-2" required>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded font-semibold">Update Data</button>
                <a href="{{ route('{$viewFolder}.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded font-semibold">Kembali</a>
            </div>
        </form>
    </div>
</x-app-layout>
BLADE;
        File::put("{$dir}/edit.blade.php", $editTemplate);

        $this->info("✔ 3 File View dibuat di: resources/views/admin/{$viewFolder}/");
    }

    protected function appendRoute($controllerName, $viewFolder)
    {
        $routePath = base_path('routes/web.php');
        $content = File::get($routePath);

        $routeLine = "    Route::resource('/admin/{$viewFolder}', \\App\\Http\\Controllers\\{$controllerName}::class);";

        if (str_contains($content, $routeLine)) {
            $this->warn("⚠️  Route /admin/{$viewFolder} sudah terdaftar, dilewati.");
            return;
        }

        // Cari penutup grup Route::middleware('auth')
        $target = "    Route::resource('/admin/users', UserController::class);";

        if (str_contains($content, $target)) {
            $replacement = $target . "\n" . $routeLine;
            $newContent = str_replace($target, $replacement, $content);
            File::put($routePath, $newContent);
            $this->info("✔ Route otomatis ditambahkan ke routes/web.php: /admin/{$viewFolder}");
        } else {
            File::append($routePath, "\n" . $routeLine);
            $this->info("✔ Route di-append ke routes/web.php");
        }
    }
}
