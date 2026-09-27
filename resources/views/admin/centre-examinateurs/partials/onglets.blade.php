{{-- Onglets : examinateurs déclarés par les centres de formation / d'expertise médicale --}}
<ul class="nav nav-tabs mb-3">
    @foreach (['formation' => 'trans.training_center', 'medical' => 'trans.medical_expertise_centre'] as $ongletType => $ongletLibelle)
        <li class="nav-item">
            <a class="nav-link {{ $type === $ongletType ? 'active' : '' }}"
               href="{{ route($routeOnglet, ['type' => $ongletType]) }}">
                <i class="fas {{ $ongletType === 'medical' ? 'fa-hospital' : 'fa-school' }} mr-1"></i> @lang($ongletLibelle)
            </a>
        </li>
    @endforeach
</ul>
