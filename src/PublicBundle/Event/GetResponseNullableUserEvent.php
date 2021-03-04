<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PublicBundle\Event;

use AdministracionBundle\Entity\Usuario;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Response user event that allows null user.
 *
 * @author Konstantinos Christofilos <kostas.christofilos@gmail.com>
 */
class GetResponseNullableUserEvent extends GetResponseUserEvent
{
    /**
     * GetResponseNullableUserEvent constructor.
     *
     * @param Usuario|null $user
     * @param Request            $request
     */
//    public function __construct($user, $request = null)
//    {
//        parent::__construct($user, $request);
//    }
    public function __construct(Usuario $user = null, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }
}
