<?php

use App\Libraries\AvatarStorage;

if (! function_exists('avatar_url')) {
    function avatar_url(?string $filename): string
    {
        return (new AvatarStorage())->url($filename);
    }
}
