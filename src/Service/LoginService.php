<?php

namespace App\Service;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginService
{
    private AuthenticationUtils $authenticationUtils;
    public function __construct(AuthenticationUtils $authenticationUtils){
        $this->authenticationUtils = $authenticationUtils;
    }
    public function getLoginError(){
        return $this->authenticationUtils->getLastAuthenticationError();
    }

    public function getLastUsername(){
        return $this->authenticationUtils->getLastUsername();
    }

}