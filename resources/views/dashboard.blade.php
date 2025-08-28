<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">Welcome back, {{ Auth::user()->name }}!</h3>
                    
                    <h4 class="text-xl font-semibold mt-8 mb-4">Your Bookings</h4>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Service</th>
                                    <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Date</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm"></th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($bookings as $booking)
                                    <tr>
                                        <td class="w-1/3 text-left py-3 px-4">{{ $booking->service_name }}</td>
                                        <td class="w-1/3 text-left py-3 px-4">{{ $booking->booking_date->format('d M Y') }}</td>
                                        <td class="text-left py-3 px-4"><span class="bg-green-200 text-green-600 py-1 px-3 rounded-full text-xs">{{ $booking->status }}</span></td>
                                        <td class="text-left py-3 px-4"><a href="#" class="text-orange-500 hover:underline">View</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">You have no bookings.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
