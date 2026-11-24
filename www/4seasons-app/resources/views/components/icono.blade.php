
@props(['nombre'])

@php
    $trazos = [
        'mas' => '<path d="M12 5v14M5 12h14"/>',
        'flecha-izq' => '<path d="M19 12H5M11 18l-6-6 6-6"/>',
        'flecha-der' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'flecha-arriba' => '<path d="M12 19V5M6 11l6-6 6 6"/>',
        'flecha-abajo' => '<path d="M12 5v14M18 13l-6 6-6-6"/>',
        'check' => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
        'cerrar' => '<path d="M6 6l12 12M18 6L6 18"/>',
        'subir' => '<path d="M12 15V4M7.5 8.5L12 4l4.5 4.5"/><path d="M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>',
        'archivo' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>',
        'lapiz' => '<path d="M4 20h4L19 9a2.83 2.83 0 0 0-4-4L4 16z"/><path d="M13.5 6.5l4 4"/>',
        'basura' => '<path d="M4 7h16M10 11v6M14 11v6"/><path d="M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12M9 7V4h6v3"/>',
        'externo' => '<path d="M14 4h6v6M20 4l-9 9"/><path d="M18 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4"/>',
        'salir' => '<path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="M10 16l-4-4 4-4M6 12h10"/>',
        'alerta' => '<path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L2.4 17.5A2 2 0 0 0 4.1 20.5h15.8a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>',
        'entrar' => '<path d="M10 16l4-4-4-4M14 12H4"/><path d="M14 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'pointer-events-none shrink-0']) }}
     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    {!! $trazos[$nombre] ?? '' !!}
</svg>
