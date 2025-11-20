<div xmlns:x-filament-tables="http://www.w3.org/1999/html">
    @php
        $manualCount = $readings->count();
        $interpolatedCount = $readings->count();
    @endphp

    {{-- Stats Cards --}}
    @if($hasWidget)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- Manual Readings Card --}}
            <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Manual Readings</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $manualCount }}</p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Calibration data points</p>
            </div>

            {{-- Interpolated Card --}}
            <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Interpolated</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $interpolatedCount }}</p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Calculated points</p>
            </div>

            {{-- Total Points Card --}}
            <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Points</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $readings->count() }}</p>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Complete dataset</p>
            </div>
        </div>
    @endif

    {{-- Readings Table --}}
    <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
        <thead class="divide-y divide-gray-200 dark:divide-white/5">
        <tr class="bg-gray-50 dark:bg-white/5">
            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start">
                <span class="text-sm font-semibold text-gray-950 dark:text-white">Dip (mm)</span>
            </th>
            <th class="fi-ta-header-cell px-3 py-3.5 text-start">
                <span class="text-sm font-semibold text-gray-950 dark:text-white">Volume (L)</span>
            </th>
            @if($hasWidget)
                <th class="fi-ta-header-cell px-3 py-3.5 text-start">
                    <span class="text-sm font-semibold text-gray-950 dark:text-white">Recorded By</span>
                </th>
                <th class="fi-ta-header-cell px-3 py-3.5 text-start">
                    <span class="text-sm font-semibold text-gray-950 dark:text-white">Type</span>
                </th>
            @endif
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-white/5">

        @php
            $prev = null;
        @endphp

        @foreach($readings as $reading)

            @php
                $isLower = $prev !== null && $reading->volume <= $prev;
            @endphp

            <tr class="fi-ta-row  transition-colors hover:bg-gray-50 dark:hover:bg-white/5 {{ $isLower  ? 'bg-purple-100'  : '' }}">
                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                    <div class="px-3 py-4">
                        <span
                                class="text-sm text-gray-950 dark:text-white">{{ number_format($reading->dip_mm, 2) }}</span>
                    </div>
                </td>
                <td class="fi-ta-cell p-0">
                    <div class="px-3 py-4">
                        <span
                                class="text-sm text-gray-950 dark:text-white">{{ number_format($reading->volume, 2) }}</span>
                    </div>
                </td>
                @if($hasWidget)
                    <td class="fi-ta-cell p-0">
                        <div class="px-3 py-4">
                            <span class="text-sm text-gray-950 dark:text-white">{{ $reading->capturedBy->name }}</span>
                        </div>
                    </td>
                    <td class="fi-ta-cell p-0">
                        <div class="px-3 py-4">
                            <x-filament::badge :color="$reading->volume === 'manual' ? 'success' : 'info'">
                                {{ ucfirst('manual') }}
                            </x-filament::badge>
                        </div>
                    </td>
                @endif
            </tr>

            @php
                $prev = $reading->volume;
            @endphp

        @endforeach
        </tbody>
    </table>
</div>
