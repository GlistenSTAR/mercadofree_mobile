<?php

namespace AdministracionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSlugCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('administracion:generate:slug')
            ->setDescription('ej: app/console administracion:generate:slug AdministracionBundle:Categoria slug')
            ->addArgument('entity', InputArgument::REQUIRED, 'Nombre de la entidad:Ej: AdministracionBundle:Categoria ')
            ->addArgument('field', InputArgument::REQUIRED, 'Nombre del campo: Ej: slug');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entity = $input->getArgument('entity');
        $field = $input->getArgument('field');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $filas = $em->getRepository($entity)->findAll();
        $servicio = $this->getContainer()->get('util');
        foreach ($filas as $fila){
            $data = $fila->getNombre();
            $slug = $servicio->generateUniqueSlug($data, $entity, $field);
            $fila->setSlug($slug);
            $em->flush();
        }

    }
}
