<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="@yield('description', 'Sistema de apoyo a la decisión de inversión de 4Seasons: evaluación contable y económica de sus alternativas de inversión.')">
        <meta name="theme-color" content="#1F2856">

        <title>@yield('title', '4Seasons | Sistema de apoyo a la decisión de inversión')</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600&family=Yellowtail&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-dvh bg-neutro font-sans text-base text-azul">
        <a href="#contenido"
           class="sr-only focus-visible:not-sr-only focus-visible:fixed focus-visible:top-4 focus-visible:left-4 focus-visible:z-50 focus-visible:rounded-marca-sm focus-visible:bg-blanco focus-visible:px-4 focus-visible:py-3">
            Saltar al contenido
        </a>

        <header>
            <nav aria-label="Principal"
                 class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-x-10 gap-y-4 px-4 py-5 sm:px-6 lg:px-8">
                <a href="{{ route('inicio') }}" class="shrink-0 rounded-marca-sm">
                    <img src="{{ asset('images/marca/4seasons-logo.svg') }}"
                         alt="4Seasons, ir al inicio" width="130" height="40" class="h-10 w-auto">
                </a>

                <ul class="order-3 flex w-full gap-5 text-[15px] whitespace-nowrap sm:gap-7 sm:order-none sm:mr-auto sm:w-auto">
                    <li>
                        <a href="{{ route('inicio') }}"
                           @if (request()->routeIs('inicio')) aria-current="page" @endif
                           class="rounded-sm py-1 text-tinta-suave transition-[color,background-color,border-color,text-decoration-color] duration-200 hover:text-azul aria-[current=page]:font-medium aria-[current=page]:text-azul aria-[current=page]:underline aria-[current=page]:decoration-aqua aria-[current=page]:decoration-2 aria-[current=page]:underline-offset-[0.25em]">
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"
                         @if (request()->routeIs('dashboard')) aria-current="page" @endif
                         class="rounded-sm py-1 text-tinta-suave transition-[color,background-color,border-color,text-decoration-color] duration-200 hover:text-azul aria-[current=page]:font-medium aria-[current=page]:text-azul aria-[current=page]:underline aria-[current=page]:decoration-aqua aria-[current=page]:decoration-2 aria-[current=page]:underline-offset-[0.25em]">
                         Tablero de evaluación
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('inicio') }}#metodologia"
                           class="rounded-sm py-1 text-tinta-suave transition-[color,background-color,border-color,text-decoration-color] duration-200 hover:text-azul">
                            Metodología
                        </a>
                    </li>
                </ul>

                <a href="{{ url('/admin') }}"
                   class="inline-flex min-h-11 items-center rounded-marca-sm border border-azul/25 px-4 text-[15px] font-medium transition-[color,background-color,border-color,text-decoration-color] duration-200 hover:border-azul hover:bg-blanco active:translate-y-px">
                    Acceso administrativo
                </a>
            </nav>
        </header>

        <main id="contenido">
            @yield('content')
        </main>

        <footer class="bg-azul-900 text-azul-100">
            <div class="mx-auto grid max-w-6xl gap-12 px-4 py-16 sm:px-6 lg:grid-cols-12 lg:px-8">
                <div class="lg:col-span-5">
                    <img src="{{ asset('images/marca/4seasons-logo-blanco.svg') }}"
                         alt="4Seasons" width="117" height="36" class="h-9 w-auto">
                    <p class="mt-5 font-script text-3xl text-aqua">Lo bello también puede ser cómodo</p>
                </div>

                <div class="text-[15px] lg:col-span-4 text-center">
                    <p class="font-medium text-blanco">Universidad de El Salvador Facultad Multidisciplinaria Oriental</p>
                </div>

                <div class="text-[15px] lg:col-span-3">
                    <p class="font-medium text-blanco">Equipo de desarrollo</p>
                    <ul class="mt-3 space-y-1">
                        <li>Luis Manuel Galeas Rosales</li>
                        <li>Carlos Alejandro Barrientos Lapa</li>
                        <li>Juan Anderson Granados Alvarez</li>
                        <li>Jairo Alexander Argueta Alvarenga</li>
                        <li>Jasson Amilcar Sánchez Pérez</li>
                    </ul>
                </div>
            </div>
        </footer>
    </body>
</html>
