<?php

declare(strict_types=1);

function hero_slides(): array
{
    return [
        [
            'eyebrow' => 'Nueva colección',
            'title' => "TU ESTILO,\nSIN LÍMITES.",
            'text' => 'Ropa versátil para cada momento de tu historia.',
            'script' => 'Más que ropa',
            'script_class' => 'text-slate-800',
            'image' => 'img/hero/estilo.jpg',
            'alt' => 'Persona sentada con polo claro y abrigo camel frente a un muro liso',
            'cta' => 'Explorar catálogo',
            'href' => 'catalogo.php',
            'note' => 'Descubre lo nuevo',
        ],
        [
            'eyebrow' => 'Esenciales',
            'title' => "PIEZAS QUE\nSE QUEDAN.",
            'text' => 'Diseños atemporales y calidad que se nota desde el primer uso.',
            'script' => 'Hecho para ti',
            'script_class' => 'text-slate-800',
            'image' => 'img/hero/piezas.jpg',
            'alt' => 'Persona con casaca negra sentada en una arquitectura de concreto',
            'cta' => 'Ver esenciales',
            'href' => 'catalogo.php',
            'note' => 'Polos, casacas y más',
        ],
        [
            'eyebrow' => 'Temporada',
            'title' => "RENUEVA\nTU LOOK.",
            'text' => 'Encuentra tus próximos favoritos y arma el uniforme de esta temporada.',
            'script' => 'Nova collection',
            'script_class' => 'text-white',
            'image' => 'img/hero/look.jpg',
            'alt' => 'Hoodie arena y pantalón cargo en una escalera de concreto',
            'cta' => 'Ver colección',
            'href' => 'catalogo.php',
            'note' => 'Envíos a todo el Perú',
        ],
    ];
}
