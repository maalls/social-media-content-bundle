<?php

// src/Acme/TestBundle/AcmeTestBundle.php
namespace Maalls\SocialMediaContentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Maalls\SocialMediaContentBundle\DependencyInjection\MaallsSocialMediaContentExtension;

class SocialMediaContentBundle extends Bundle
{


    public function getContainerExtension()
    {
        return new MaallsSocialMediaContentExtension();
    }
}