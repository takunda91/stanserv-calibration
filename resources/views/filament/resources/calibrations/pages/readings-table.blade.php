<table class="filament-tables-table w-full text-sm table table-column-group">
    <thead>
    <tr class="bg-gray-50">
        <th class="p-2 text-center">Dip (mm)</th>
        <th class="p-2 text-center">Volume (L)</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($readings as $reading)
        <tr class="border-b">
            <td class="p-2 text-center">{{ $reading->dip_mm }}</td>
            <td class="p-2 text-center">{{ number_format($reading->volume, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
