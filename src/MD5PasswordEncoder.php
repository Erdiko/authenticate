<?php
/**
 * MD5PasswordEncoder
 *
 * @package     erdiko/authenticate
 * @copyright   Copyright (c) 2017, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */

namespace erdiko\authenticate;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @author Elnur Abdurrakhimov <elnur@elnur.pro>
 * @author Terje Bråten <terje@braten.be>
 */
class MD5PasswordEncoder extends BasePasswordEncoder
{
    const MAX_PASSWORD_LENGTH = 72;

    /**
     * @var string
     */
    private $cost;

    /**
     * Constructor.
     *
     * @param int $cost The algorithmic cost that should be used
     *
     * @throws \RuntimeException         When no BCrypt encoder is available
     * @throws \InvalidArgumentException if cost is out of range
     */
    public function __construct($cost=10)
    {
        $cost = (int) $cost;
        if ($cost < 4 || $cost > 31) {
            throw new \InvalidArgumentException('Cost must be in the range of 4-31.');
        }

        $this->cost = $cost;
    }

	/**
	 * Merges a password and a salt.
	 *
	 * @param string $password the password to be used
	 * @param string $salt     the salt to be used
	 *
	 * @return string a merged password and salt
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function mergePasswordAndSalt($password, $salt)
	{
		if (empty($salt)) {
			return $password;
		}

		return $password.$salt;
	}

    /**
     * Encodes the raw password.
     *
     *
     * @param string $raw  The password to encode
     * @param string $salt The salt
     *
     * @return string The encoded password
     *
     * @throws BadCredentialsException when the given password is too long
     *
     */
    public function encodePassword($raw, $salt="")
    {
        if ($this->isPasswordTooLong($raw)) {
            throw new BadCredentialsException('Invalid password.');
        }

        return md5($this->mergePasswordAndSalt($raw,$salt));
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        return !$this->isPasswordTooLong($raw) && ($encoded===$this->encodePassword($raw,$salt));
    }
}
