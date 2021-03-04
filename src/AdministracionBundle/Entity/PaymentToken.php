<?php
namespace AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Token;

/**
 * @ORM\Table
 * @ORM\Entity(repositoryClass="AdministracionBundle\Repository\PaymentTokenRepository")
 */
class PaymentToken extends Token
{
}