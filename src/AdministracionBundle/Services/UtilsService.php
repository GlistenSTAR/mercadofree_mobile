<?php


namespace AdministracionBundle\Services;




use Symfony\Component\DependencyInjection\ContainerInterface;

class UtilsService {

    protected $container;
    protected $em;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em=$this->container->get('doctrine')->getManager();
    }



    public function getUsuarioLogueado(){
        $usuario = $this->container->get('security.context')->getToken()->getUser();
//        echo $usuario;die;

        if(is_object($usuario)){
            return $this->em->getRepository('AdministracionBundle:Usuario')->find($usuario->getId());
        }
        else{
            return null;
        }

    }

    public function generateSlug($texto){
        $slug = strtolower($texto);
        $slug = str_replace(" ", "-", $slug);
        $slug = str_replace("á", "a", $slug);
        $slug = str_replace("é", "e", $slug);
        $slug = str_replace("í", "i", $slug);
        $slug = str_replace("ó", "o", $slug);
        $slug = str_replace("ú", "u", $slug);
        $slug = str_replace("ñ", "nn", $slug);

        return $slug;
    }

    public function generateUniqueSlug($texto, $tableName, $fieldName){
        $slug = preg_replace("/-$/","",preg_replace('/[^a-z0-9]+/i', "-", strtolower($texto)));
        $slug = str_replace(" ", "-", $slug);
        $slug = str_replace("á", "a", $slug);
        $slug = str_replace("é", "e", $slug);
        $slug = str_replace("í", "i", $slug);
        $slug = str_replace("ó", "o", $slug);
        $slug = str_replace("ú", "u", $slug);
        $slug = str_replace("ñ", "nn", $slug);


        $txt = "'". $slug ."%'";
//        $sql = "SELECT COUNT(*) AS NumHits FROM $tableName t";
        $qb = $this->container->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('count(t.id) as NumHits')
        ->from($tableName, 't')
            ->where('t.'.$fieldName. ' LIKE :p')
            ->setParameter('p', $txt);

        $result =  $qb->getQuery()->getSingleResult();
        $numHits = $result['NumHits'];
        return ($numHits > 0) ? ($slug . '-' . $numHits) : $slug;

    }

    public function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }


}