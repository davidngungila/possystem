@php
    $favLogo = \App\Models\Setting::getValue('shop_logo', \App\Models\Setting::getValue('university_logo'));
    $favAcro = substr(\App\Models\Setting::getValue('shop_acronym', \App\Models\Setting::getValue('university_acronym','SP')),0,1);
@endphp
@if($favLogo)
    <link rel="icon" href="{{ asset($favLogo) }}">
@else
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%230066CC'/%3E%3Ctext x='32' y='42' font-family='Arial' font-size='30' font-weight='bold' fill='%23ffffff' text-anchor='middle'%3E{{ $favAcro }}%3C/text%3E%3C/svg%3E">
@endif
