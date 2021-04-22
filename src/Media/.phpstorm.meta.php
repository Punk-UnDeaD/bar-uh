<?php

namespace PHPSTORM_META;

override(
    \App\Media\Repository\ImageRepository::get(),
    map(['' => \App\Media\Entity\Image::class])
);
