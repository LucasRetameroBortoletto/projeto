<?php
// Lapiseira em SVG inline, usada no header e no botão de login.
// Fica no HTML (e não como <img>) para o CSS/JS conseguirem animar cada peça:
//   .lapiseira__botao   botão traseiro (afunda no clique)
//   .lapiseira__corpo   corpo e clipe
//   .lapiseira__grip    empunhadura serrilhada
//   .lapiseira__ponta   cone e tubo guia
//   .lapiseira__grafite o grafite (avança no clique)
// Antes de incluir, defina $classe_lapiseira com a classe extra do lugar onde ela aparece.
?>
<svg class="lapiseira <?= $classe_lapiseira ?? '' ?>" viewBox="0 0 300 24" fill="none" stroke="currentColor"
     stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    <g class="lapiseira__botao">
        <rect x="4" y="8.5" width="14" height="7" rx="1.5"/>
    </g>
    <g class="lapiseira__corpo">
        <rect x="18" y="7" width="158" height="10" rx="0.5"/>
        <path d="M23 7 V17"/>
        <path d="M30 7 V4 H116 C120 4 122 5.2 122 7"/>
    </g>
    <g class="lapiseira__grip">
        <path d="M176 7.5 H236 V16.5 H176"/>
        <path d="M181 7.5 V16.5 M186 7.5 V16.5 M191 7.5 V16.5 M196 7.5 V16.5 M201 7.5 V16.5 M206 7.5 V16.5
                 M211 7.5 V16.5 M216 7.5 V16.5 M221 7.5 V16.5 M226 7.5 V16.5 M231 7.5 V16.5" stroke-width="0.6"/>
    </g>
    <g class="lapiseira__ponta">
        <path d="M236 7.5 L258 10.5 V13.5 L236 16.5"/>
        <path d="M258 11.2 H276 V12.8 H258"/>
    </g>
    <path class="lapiseira__grafite" d="M276 12 H282" stroke-width="1.4"/>
</svg>
