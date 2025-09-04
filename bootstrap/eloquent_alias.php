<?php

// Eloquent alias for IDE helper compatibility
if (!class_exists('Eloquent')) {
    class_alias('Illuminate\Database\Eloquent\Model', 'Eloquent');
}
