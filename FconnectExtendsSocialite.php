<?php


namespace Aidev\Fconnect;

use SocialiteProviders\Manager\SocialiteWasCalled;

class FconnectExtendsSocialite
{

    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'fconnect', __NAMESPACE__.'\Provider'
        );
    }
}