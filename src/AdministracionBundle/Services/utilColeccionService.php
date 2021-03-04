<?php


namespace AdministracionBundle\Services;


class utilColeccionService
{

    protected $container;
    protected $em;
    public function __construct($container)
    {
        $this->container = $container;
        $this->em=$this->container->get('doctrine')->getManager();
    }

    public function procesarFoto($foto, $coleccion)
    {
        $newName="";
        if($foto!=null)
        {
            $newName=$coleccion->getId().md5(time()+rand(0,1000)).'.jpg';
            if(file_exists($this->container->getParameter('uploads.images.temp').$foto))
            {
                rename($this->container->getParameter('uploads.images.temp').$foto, $this->container->getParameter('uploads.images.colecciones').$newName);

                //crear el obj imagen

                $coleccion->setImagen($newName);

                $this->em->persist($coleccion);
            }
        }
    }
}