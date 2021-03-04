<?php
namespace AdministracionBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use AdministracionBundle\Entity\Usuario;

class UsuarioRepository extends EntityRepository
{

    public function findByUsuario(Request $request)
    {

        $em=$this->getEntityManager();

        $where="where";
        $having="";
        $orderBy="";

        $flag=false;

        $whereParameters=array();

        $start=$request->request->get('start');
        $offset=$request->request->get('length');

        $columnas=array(
            0=>"fechaRegistro",
            1=>"fechaRegistro",
            2=>"email",
            3=>"rolid",
            4=>"password"
        );


        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {

            $where=$where." or usuario.email like :emailUsuario";
            $whereParameters+=['emailUsuario'=>'%'.$valorSearch.'%'];

            $where=$where." or rd.nombre like :rolUsuario";
            $whereParameters+=['rolUsuario'=>'%'.$valorSearch.'%'];


            $flag=true;

        }
        $columnaOrder=$columnas[$request->request->get("order")[0]["column"]];
        $sentidoOrder=$request->request->get("order")[0]["dir"];


        if ($columnaOrder == "email") {
            $orderBy = "ORDER BY usuario.email ".$sentidoOrder;
        }
        if ($columnaOrder == "password") {
            $orderBy = "ORDER BY usuario.password ".$sentidoOrder;
        }
        if ($columnaOrder == "fechaRegistro") {
            $orderBy = "ORDER BY usuario.fechaRegistro ".$sentidoOrder;
        }
        if ($columnaOrder == "rolid") {
            $orderBy = "ORDER BY rd.nombre ".$sentidoOrder;
        }

        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  usuario                  
                  
              from 
              
              AdministracionBundle:Usuario usuario 
              LEFT JOIN  usuario.rolid rd               
             
              ".$where."  ".$orderBy." 
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);
        $dql->setMaxResults($offset);
        $dql->setFirstResult($start);


        return $dql;
    }
    public function findByUsuarioTotal(Request $request)
    {

        $em=$this->getEntityManager();

        $where="where";
        $having="";
        $orderBy="";

        $flag=false;

        $whereParameters=array();

        $start=$request->request->get('start');
        $offset=$request->request->get('length');

        $columnas=array(
            0=>"nombre",
            1=>"apellidos",
            2=>"fechaRegistro",
            3=>"email",
            4=>"rolid",
            5=>"password"
        );


        $valorSearch=$request->request->get('search')['value'];

        if($valorSearch)
        {

            $where=$where." or usuario.email like :emailUsuario";
            $whereParameters+=['emailUsuario'=>'%'.$valorSearch.'%'];

            $where=$where." or rd.nombre like :rolUsuario";
            $whereParameters+=['rolUsuario'=>'%'.$valorSearch.'%'];


            $flag=true;

        }

        if ($flag==false)
        {
            $where="";

        }

        $sql="select 

                  usuario                  
                  
              from 
              
              AdministracionBundle:Usuario usuario 
              LEFT JOIN  usuario.rolid rd               
             
              ".$where."   
              
             
              
              
             "
        ;

        $dql=$em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }

    /**
     * 
     * @param Usuario $usuario
     */
    public function persistAndFlush(Usuario $usuario) {
        $em = $this->getEntityManager();
        $em->persist($usuario);
        $em->flush();
    }

    public function findByRol($rol)
    {
        $em = $this->getEntityManager();

        $query = "select u from AdministracionBundle:Usuario u left join u.rolid r ";
        $query.= " where r.slug = :rol";

        return $em->createQuery($query)->setParameter('rol',$rol)->getResult();
    }

}
