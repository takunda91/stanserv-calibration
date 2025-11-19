@php use App\Livewire\CompartmentInterpolationAveragesChart; @endphp
<div>
    @livewire(CompartmentInterpolationAveragesChart::class, ['compartmentId' => $compartment->id])
</div>
