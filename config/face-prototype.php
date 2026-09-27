<?php

return [
    'isolated' => false,
    'token' => env('FACE_PROTOTYPE_TOKEN', ''),
    'model' => 'face-api-1.7.15-tiny-landmark68-recognition128',
    // Experimental distances, not probabilities or validated attendance decisions.
    'threshold' => 0.5,
    'minimum_gap' => 0.08,
    'max_references' => 50,
];
