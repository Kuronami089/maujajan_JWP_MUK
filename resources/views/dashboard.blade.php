<x-app-layout>
        <x-slot name="header">
        <div class="flex justify-between items-center">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Pesanan Masuk') }}
            </h2>

                        <div class="flex items-center gap-3">
                                <a href="{{ route('foods.index') }}" style="background-color: #16a34a; color: #ffffff;" class="inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest hover:opacity-90 shadow-sm transition">
                    Kelola Makanan
                </a>
                                <a href="{{ route('users.index') }}" style="background-color: #2563eb; color: #ffffff;" class="inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest hover:opacity-90 shadow-sm transition">
                    Kelola User
                </a>
                                <a href="{{ route('customer.index') }}" target="_blank" style="background-color: #1f2937; color: #ffffff;" class="inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest hover:opacity-90 shadow-sm transition">
                    Lihat Menu Customer
                </a>
            </div>
        </div>
    </x-slot>

        <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
                        @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

                        @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm font-medium">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="p-4 border-b"># ID</th>
                                <th class="p-4 border-b">Pelanggan</th>
                                <th class="p-4 border-b">No. Meja</th>
                                <th class="p-4 border-b">Rincian Pesanan</th>
                                <th class="p-4 border-b">Total Harga</th>
                                <th class="p-4 border-b">Status</th>
                                <th class="p-4 border-b text-center">Aksi Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 font-bold text-gray-700">#{{ $order->id }}</td>
                                    <td class="p-4 font-medium">{{ $order->customer_name }}</td>
                                    <td class="p-4">
                                        <span class="bg-blue-100 text-blue-800 font-bold px-2.5 py-1 rounded-full text-xs">
                                            Meja {{ $order->table_number }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                                            @foreach($order->orderDetails as $detail)
                                                <li>
                                                    <strong>{{ $detail->food->name ?? 'Menu' }}</strong>
                                                    x{{ $detail->quantity }}
                                                    <span class="text-xs text-gray-400">(Rp {{ number_format($detail->subtotal) }})</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="p-4 font-bold text-green-600">
                                        Rp {{ number_format($order->total_price) }}
                                    </td>
                                    <td class="p-4">
                                        @if($order->status == 'Pending')
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-1 rounded">PENDING</span>
                                        @elseif($order->status == 'Selesai')
                                            <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-1 rounded">SELESAI / LUNAS</span>
                                        @elseif($order->status == 'Dibatalkan')
                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-1 rounded">DIBATALKAN</span>
                                        @elseif($order->status == 'Diproses')
                                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded">DIPROSES</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-1 rounded">{{ strtoupper($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1.5 bg-white shadow-sm font-semibold cursor-pointer hover:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                                                <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="Selesai" {{ $order->status == 'Selesai' ? 'selected' : '' }}>Selesai / Lunas</option>
                                                <option value="Dibatalkan" {{ $order->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                            </select>  
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-gray-500">Belum ada pesanan masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
