<?php
/**
 * AuthenticationManager
 *
 * @package     erdiko/authenticate
 * @copyright   Copyright (c) 2017, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */

namespace erdiko\authenticate;

use erdiko\authorize\traits\SessionAccessTrait;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthenticationManager implements AuthenticationManagerInterface
{
    use SessionAccessTrait;

    private $authenticationManager;

    public function __construct(UserProviderInterface $userProvider, $passwordEncoder=null)
    {
        $passwordEncoder = empty($passwordEncoder)
            ? new BCryptPasswordEncoder(10)
            : $passwordEncoder;
        // Create an encoder factory that will "encode" passwords
        $encoderFactory = new \Symfony\Component\Security\Core\Encoder\EncoderFactory(array(
            // In case you don't provide a Password encoder, it will use BCrypt as default
            'Symfony\Component\Security\Core\User\User' => $passwordEncoder,
        ));

        // The user checker is a simple class that allows to check against different elements (user disabled, account expired etc)
        $userChecker = new UserChecker();
        // The (authentication) providers are a way to make sure to match credentials against users based on their "providerkey".
        $userProvider = array(
            new DaoAuthenticationProvider($userProvider, $userChecker, 'main', $encoderFactory, true),
        );


        $this->authenticationManager = new AuthenticationProviderManager($userProvider, true);
    }

    public function authenticate(TokenInterface $unauthenticatedToken)
    {

        try {
            $authenticatedToken = $this->authenticationManager->authenticate($unauthenticatedToken);
            self::startSession();
            $tokenStorage = new TokenStorage();
            $tokenStorage->setToken($authenticatedToken);
            $_SESSION['tokenstorage'] = $tokenStorage;
        } catch (\Exception $failed) {
            // authentication failed
            throw new \Exception($failed->getMessage());
        }
        return $authenticatedToken;
    }
}