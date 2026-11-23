@extends('layouts.app')

@section('title', '4Seasons | Sistema de apoyo a la decisión de inversión')

@section('content')
    <section aria-labelledby="titulo-inicio"
             class="mx-auto grid max-w-6xl items-center gap-12 px-4 pt-8 pb-20 sm:px-6 lg:grid-cols-12 lg:gap-12 lg:min-h-[calc(100svh-5.25rem)] lg:px-8 lg:pt-10 lg:pb-24">
        <div class="min-w-0 lg:col-span-7">
            <h1 id="titulo-inicio"
                class="max-w-[14ch] text-5xl leading-[1.02] font-semibold tracking-[-0.025em] sm:text-6xl lg:text-[4.5rem]">
                Decisiones de inversión con cada cifra respaldada.
            </h1>
            <p class="mt-6 max-w-[48ch] text-lg leading-relaxed text-tinta-suave sm:text-xl">
                Sistema de evaluación económica y contable de inversiones: una herramienta automatizada de apoyo a la toma de decisiones que compara las alternativas de inversión de 4Seasons de forma automatizada, auditable y trazable.
            </p>
            <div class="mt-9 flex flex-wrap items-center gap-x-7 gap-y-4">
                {{-- Pendiente de migrar: vista dashboard de Django --}}
                <a href="{{ url('/dashboard') }}"
                   class="inline-flex min-h-12 items-center justify-center rounded-marca-sm bg-aqua px-6 text-base font-medium text-azul-900 transition-[color,background-color,border-color,text-decoration-color] duration-200 hover:bg-aqua-600 active:translate-y-px">
                    Ir al tablero de evaluación
                </a>
                <a href="#metodologia"
                   class="rounded-sm py-1 font-medium underline decoration-azul/30 decoration-1 underline-offset-[0.2em] transition-[color,background-color,border-color,text-decoration-color] duration-200 hover:decoration-azul">
                    Ver la metodología
                </a>
            </div>
        </div>

        <div class="min-w-0 rounded-marca-lg bg-azul p-7 text-blanco sm:p-10 lg:col-span-5">
            <p class="text-[15px] text-azul-100">Los indicadores que calcula el sistema para cada alternativa</p>

            <dl class="mt-7 grid grid-cols-2 gap-x-6 gap-y-8">
                <div class="flex flex-col border-t border-blanco/15 pt-5">
                    <dt class="order-2 mt-2 text-azul-100">Costo anual uniforme equivalente</dt>
                    <dd class="order-1 text-4xl leading-none font-semibold text-aqua">CAUE</dd>
                </div>
                <div class="flex flex-col border-t border-blanco/15 pt-5">
                    <dt class="order-2 mt-2 text-azul-100">Valor presente neto</dt>
                    <dd class="order-1 text-4xl leading-none font-semibold text-aqua">VPN</dd>
                </div>
                <div class="flex flex-col border-t border-blanco/15 pt-5">
                    <dt class="order-2 mt-2 text-azul-100">Tasa interna de retorno</dt>
                    <dd class="order-1 text-4xl leading-none font-semibold text-aqua">TIR</dd>
                </div>
                <div class="flex flex-col border-t border-blanco/15 pt-5">
                    <dt class="order-2 mt-2 text-azul-100">Relación beneficio/costo</dt>
                    <dd class="order-1 text-4xl leading-none font-semibold text-aqua">B/C</dd>
                </div>
            </dl>
        </div>
    </section>

    <section id="metodologia" aria-labelledby="titulo-metodologia" class="bg-blanco">
        <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
            <h2 id="titulo-metodologia" class="text-4xl leading-tight font-semibold tracking-[-0.02em] sm:text-5xl lg:text-[3.25rem]">
                ¿Qué resuelve esta aplicación?
            </h2>
            <p class="mt-5 max-w-[60ch] text-lg text-tinta-suave">
                Integra el registro contable y el sistema de costos de 4Seasons con técnicas de evaluación financiera de Ingeniería Económica, de forma automatizada, auditable y trazable.
            </p>

            <ol class="mt-12 border-b border-linea">
                <li class="grid gap-3 border-t border-linea py-8 sm:grid-cols-12 sm:gap-8">
                    <span class="text-3xl leading-none font-semibold text-aqua-600 tabular-nums sm:col-span-1">01</span>
                    <h3 class="text-2xl leading-tight font-semibold sm:col-span-4">Base contable real</h3>
                    <p class="max-w-[58ch] text-lg text-tinta-suave sm:col-span-7">
                        Integra los costos operativos reales, ahorros estimados, depreciación por línea recta y suma de dígitos, y el cálculo del flujo de efectivo después de impuestos (FEDI).
                    </p>
                </li>
                <li class="grid gap-3 border-t border-linea py-8 sm:grid-cols-12 sm:gap-8">
                    <span class="text-3xl leading-none font-semibold text-aqua-600 tabular-nums sm:col-span-1">02</span>
                    <h3 class="text-2xl leading-tight font-semibold sm:col-span-4">Indicadores económicos</h3>
                    <p class="max-w-[58ch] text-lg text-tinta-suave sm:col-span-7">
                        Calcula automáticamente el CAUE, VPN, TIR y la relación beneficio/costo (B/C), además de incluir un simulador de sensibilidad en tiempo real.
                    </p>
                </li>
                <li class="grid gap-3 border-t border-linea py-8 sm:grid-cols-12 sm:gap-8">
                    <span class="text-3xl leading-none font-semibold text-aqua-600 tabular-nums sm:col-span-1">03</span>
                    <h3 class="text-2xl leading-tight font-semibold sm:col-span-4">Trazabilidad documental</h3>
                    <p class="max-w-[58ch] text-lg text-tinta-suave sm:col-span-7">
                        Cada cifra ingresada está vinculada a su justificación operativa, proveedores cotizados, cotizaciones escaneadas y respaldos contables auditables.
                    </p>
                </li>
            </ol>
        </div>
    </section>

    <section aria-labelledby="titulo-modulos">
        <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
            <h2 id="titulo-modulos" class="text-4xl leading-tight font-semibold tracking-[-0.02em] sm:text-5xl lg:text-[3.25rem]">
                Lo que encontrará en el sistema
            </h2>

            <div class="mt-12 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                <div class="border-t-2 border-azul pt-5">
                    <h3 class="text-xl leading-snug font-semibold">Indicadores financieros automatizados</h3>
                    <p class="mt-3 text-tinta-suave">Cálculo exacto de CAUE, VPN sobre el flujo después de impuestos (FEDI), TIR y relación beneficio/costo para cada alternativa.</p>
                </div>
                <div class="border-t-2 border-azul pt-5">
                    <h3 class="text-xl leading-snug font-semibold">Simulador de sensibilidad</h3>
                    <p class="mt-3 text-tinta-suave">Modifique en vivo la inversión, el ingreso o ahorro, el costo anual y la vida útil, con validación contra valores negativos.</p>
                </div>
                <div class="border-t-2 border-azul pt-5">
                    <h3 class="text-xl leading-snug font-semibold">Diagrama de flujo de efectivo</h3>
                    <p class="mt-3 text-tinta-suave">Línea de tiempo que clasifica los flujos anuales en salida, neutro y entrada.</p>
                </div>
                <div class="border-t-2 border-azul pt-5">
                    <h3 class="text-xl leading-snug font-semibold">Planificación de depreciación</h3>
                    <p class="mt-3 text-tinta-suave">Comparativa automática entre línea recta y suma de los dígitos de los años (SDA) para activos propios.</p>
                </div>
                <div class="border-t-2 border-azul pt-5">
                    <h3 class="text-xl leading-snug font-semibold">Ficha de trazabilidad documental</h3>
                    <p class="mt-3 text-tinta-suave">Una ventana por alternativa con el origen de cada cifra, los proveedores cotizados y acceso directo a las evidencias adjuntas.</p>
                </div>
                <div class="border-t-2 border-azul pt-5">
                    <h3 class="text-xl leading-snug font-semibold">Panel administrativo y auditoría</h3>
                    <p class="mt-3 text-tinta-suave">Módulo protegido para cargar los datos reales, las justificaciones, el desglose de proveedores y seguros, y los archivos de respaldo.</p>
                </div>
            </div>
        </div>
    </section>

    <section aria-labelledby="titulo-cierre" class="bg-azul text-blanco">
        <div class="mx-auto grid max-w-6xl items-end gap-10 px-4 py-20 sm:px-6 lg:grid-cols-12 lg:px-8">
            <div class="lg:col-span-8">
                <h2 id="titulo-cierre" class="max-w-[18ch] text-4xl leading-tight font-semibold tracking-[-0.02em] sm:text-5xl lg:text-[3.25rem]">
                    Abra el tablero y compare las alternativas.
                </h2>
                <p class="mt-5 max-w-[52ch] text-lg text-azul-100">
                    Los indicadores, el simulador, la depreciación y las evidencias de cada alternativa, en un mismo lugar.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-x-7 gap-y-4 lg:col-span-4 lg:justify-end">
                <a href="{{ url('/dashboard') }}"
                   class="inline-flex min-h-12 items-center justify-center rounded-marca-sm bg-aqua px-6 text-base font-medium text-azul-900 transition-[color,background-color,border-color,text-decoration-color] duration-200 hover:bg-aqua-600 focus-visible:outline-aqua active:translate-y-px">
                    Ir al tablero de evaluación
                </a>
                <a href="{{ url('/admin') }}"
                   class="rounded-sm py-1 font-medium underline decoration-blanco/40 decoration-1 underline-offset-[0.2em] transition-[color,background-color,border-color,text-decoration-color] duration-200 hover:decoration-blanco focus-visible:outline-aqua">
                    Gestionar datos en admin
                </a>
            </div>
        </div>
    </section>
@endsection
