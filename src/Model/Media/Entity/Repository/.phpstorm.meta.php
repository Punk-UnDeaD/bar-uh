<?php

namespace PHPSTORM_META;

    override(
        \App\Model\Media\Entity\Repository\ImageRepository::get(),
        map(['' => \App\Model\Media\Entity\Image::class])
    );
