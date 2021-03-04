<?php

namespace AdministracionBundle\Repository;

use AdministracionBundle\Entity\EstadoProducto;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;


class ProductoRepository extends EntityRepository
{
    public function findByProducto(Request $request)
    {

        $em = $this->getEntityManager();

        $where = "where";
        $having = "";
        $orderBy = "";

        $flag = false;

        $whereParameters = array();

        $start = $request->request->get('start');
        $offset = $request->request->get('length');

        $columnas = array(
            0 => "nombre",
            1 => "nombre",
            2 => "nombre",
            3 => "precio",
            4 => "descripcion",
            5 => "cuotas",
            6 => "cantidad",
            7 => "categoria",
            8 => "usuario",
            9 => "estado"
        );


        $valorSearch = $request->request->get('search')['value'];

        if ($valorSearch) {

            $where = $where . " producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.descripcion like :descripcionProducto";
            $whereParameters += ['descripcionProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.precio like :precioProducto";
            $whereParameters += ['precioProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.cuotaspagar like :cuotaspagarProducto";
            $whereParameters += ['cuotaspagarProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.cantidad like :cantidadProducto";
            $whereParameters += ['cantidadProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or categoria.nombre like :categoriaProducto";
            $whereParameters += ['categoriaProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or usuario.nombre like :usuarioProducto";
            $whereParameters += ['usuarioProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or estado.nombre like :estadoProducto";
            $whereParameters += ['estadoProducto' => '%' . $valorSearch . '%'];

            $flag = true;

        }
        $columnaOrder = $columnas[$request->request->get("order")[0]["column"]];
        $sentidoOrder = $request->request->get("order")[0]["dir"];


        if ($columnaOrder == "nombre") {
            $orderBy = "ORDER BY producto.nombre " . $sentidoOrder;
        }
        if ($columnaOrder == "precio") {
            $orderBy = "ORDER BY producto.precio " . $sentidoOrder;
        }
        if ($columnaOrder == "descripcion") {
            $orderBy = "ORDER BY producto.descripcion " . $sentidoOrder;
        }
        if ($columnaOrder == "cuotas") {
            $orderBy = "ORDER BY producto.cuotaspagar " . $sentidoOrder;
        }
        if ($columnaOrder == "cantidad") {
            $orderBy = "ORDER BY producto.cantidad " . $sentidoOrder;
        }
        if ($columnaOrder == "categoria") {
            $orderBy = "ORDER BY categoria.nombre " . $sentidoOrder;
        }
        if ($columnaOrder == "usuario") {
            $orderBy = "ORDER BY usuario.email " . $sentidoOrder;
        }
        if ($columnaOrder == "estado") {
            $orderBy = "ORDER BY estado.nombre " . $sentidoOrder;
        }


        if ($flag == false) {
            $where = "";

        }

        $sql = "select 

                  producto                  
                  
              from 
              
              AdministracionBundle:Producto producto 
              LEFT JOIN  producto.estadoProductoid estado
              LEFT JOIN  producto.ofertaid oferta
              LEFT JOIN  producto.categoriaid categoria
              LEFT JOIN  producto.usuarioid usuario
             
              " . $where . "  " . $orderBy . " 
              
             
              
              
             ";

        $dql = $em->createQuery($sql);

        $dql->setParameters($whereParameters);
        $dql->setMaxResults($offset);
        $dql->setFirstResult($start);


        return $dql;
    }

    public function findByProductoTotal(Request $request)
    {

        $em = $this->getEntityManager();

        $where = "where";
        $having = "";
        $orderBy = "";

        $flag = false;

        $whereParameters = array();

        $start = $request->request->get('start');
        $offset = $request->request->get('length');

        $valorSearch = $request->request->get('search')['value'];

        if ($valorSearch) {

            $where = $where . " producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.descripcion like :descripcionProducto";
            $whereParameters += ['descripcionProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.precio like :precioProducto";
            $whereParameters += ['precioProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.cuotaspagar like :cuotaspagarProducto";
            $whereParameters += ['cuotaspagarProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.cantidad like :cantidadProducto";
            $whereParameters += ['cantidadProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or categoria.nombre like :categoriaProducto";
            $whereParameters += ['categoriaProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or usuario.nombre like :usuarioProducto";
            $whereParameters += ['usuarioProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or estado.nombre like :estadoProducto";
            $whereParameters += ['estadoProducto' => '%' . $valorSearch . '%'];

            $flag = true;

        }

        if ($flag == false) {
            $where = "";

        }

        $sql = "select 

                  producto                  
                  
              from 
              
              AdministracionBundle:Producto producto 
              LEFT JOIN  producto.estadoProductoid estado
              LEFT JOIN  producto.ofertaid oferta
              LEFT JOIN  producto.categoriaid categoria
              LEFT JOIN  producto.usuarioid usuario
             
              " . $where . "   
              
             
              
              
             ";

        $dql = $em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }

    public function findByProductoDetalles(Request $request)
    {

        $em = $this->getEntityManager();

        $where = "where";
        $having = "";
        $orderBy = "";

        $flag = false;

        $whereParameters = array();

        $start = $request->request->get('start');
        $offset = $request->request->get('length');

        $columnas = array(
            0 => "nombre",
            1 => "precio",
            2 => "descripcion",
            3 => "cuotas",
            4 => "cantidad",
            5 => "categoria",
            8 => "usuario",
            9 => "estado"
        );


        $valorSearch = $request->request->get('search')['value'];


        $procedencia = $request->request->get('procedencia');


        if ($procedencia == 'tienda') {
            $idUsuarioTienda = $request->request->get('idUsuarioTienda');

            $where = $where . " usuario.id = :idUsuarioTienda";
            $whereParameters = ['idUsuarioTienda' => $idUsuarioTienda];
        } else if ($procedencia == 'oferta') {
            $idOferta = $request->request->get('idOferta');

            $where = $where . " oferta.id = :idOferta";
            $whereParameters = ['idOferta' => $idOferta];
        } else if ($procedencia == 'coleccion') {
            $idColeccion = $request->request->get('idColeccion');

            $where = $where . " coleccion.id = :idColeccion";
            $whereParameters = ['idColeccion' => $idColeccion];
        } else if ($procedencia == 'usuario') {
            $idUsuario = $request->request->get('idUsuario');

            $where = $where . " usuario.id = :idUsuario";
            $whereParameters = ['idUsuario' => $idUsuario];
        }

        if ($valorSearch) {

            $where = $where . " producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.descripcion like :descripcionProducto";
            $whereParameters += ['descripcionProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.precio like :precioProducto";
            $whereParameters += ['precioProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.cuotaspagar like :cuotaspagarProducto";
            $whereParameters += ['cuotaspagarProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.cantidad like :cantidadProducto";
            $whereParameters += ['cantidadProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or categoria.nombre like :categoriaProducto";
            $whereParameters += ['categoriaProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or usuario.nombre like :usuarioProducto";
            $whereParameters += ['usuarioProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or estado.nombre like :estadoProducto";
            $whereParameters += ['estadoProducto' => '%' . $valorSearch . '%'];

            $flag = true;

        }
        $columnaOrder = $columnas[$request->request->get("order")[0]["column"]];
        $sentidoOrder = $request->request->get("order")[0]["dir"];


        if ($columnaOrder == "nombre") {
            $orderBy = "ORDER BY producto.nombre " . $sentidoOrder;
        }
        if ($columnaOrder == "precio") {
            $orderBy = "ORDER BY producto.precio " . $sentidoOrder;
        }
        if ($columnaOrder == "descripcion") {
            $orderBy = "ORDER BY producto.descripcion " . $sentidoOrder;
        }
        if ($columnaOrder == "cuotas") {
            $orderBy = "ORDER BY producto.cuotaspagar " . $sentidoOrder;
        }
        if ($columnaOrder == "cantidad") {
            $orderBy = "ORDER BY producto.cantidad " . $sentidoOrder;
        }
        if ($columnaOrder == "categoria") {
            $orderBy = "ORDER BY categoria.nombre " . $sentidoOrder;
        }
        if ($columnaOrder == "usuario") {
            $orderBy = "ORDER BY usuario.email " . $sentidoOrder;
        }
        if ($columnaOrder == "estado") {
            $orderBy = "ORDER BY estado.nombre " . $sentidoOrder;
        }


        $sql = "select 

                  producto                  
                  
              from 
              
              AdministracionBundle:Producto producto 
              LEFT JOIN  producto.estadoProductoid estado
              LEFT JOIN  producto.ofertaid oferta
              LEFT JOIN  producto.categoriaid categoria
              LEFT JOIN  producto.usuarioid usuario
              LEFT JOIN  producto.coleccionid coleccion
             
              " . $where . "  " . $orderBy . " 
              
             
              
              
             ";

        $dql = $em->createQuery($sql);

        $dql->setParameters($whereParameters);
        $dql->setMaxResults($offset);
        $dql->setFirstResult($start);


        return $dql;
    }

    public function findByProductoDetallesTotal(Request $request)
    {

        $em = $this->getEntityManager();

        $where = "where";
        $having = "";
        $orderBy = "";

        $flag = false;

        $whereParameters = array();

        $start = $request->request->get('start');
        $offset = $request->request->get('length');

        $valorSearch = $request->request->get('search')['value'];

        $procedencia = $request->request->get('procedencia');


        if ($procedencia == 'tienda') {
            $idUsuarioTienda = $request->request->get('idUsuarioTienda');

            $where = $where . " usuario.id = :idUsuarioTienda";
            $whereParameters = ['idUsuarioTienda' => $idUsuarioTienda];
        } else if ($procedencia == 'oferta') {
            $idOferta = $request->request->get('idOferta');

            $where = $where . " oferta.id = :idOferta";
            $whereParameters = ['idOferta' => $idOferta];
        } else if ($procedencia == 'coleccion') {
            $idColeccion = $request->request->get('idColeccion');

            $where = $where . " coleccion.id = :idColeccion";
            $whereParameters = ['idColeccion' => $idColeccion];
        }

        if ($valorSearch) {

            $where = $where . " producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.descripcion like :descripcionProducto";
            $whereParameters += ['descripcionProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.precio like :precioProducto";
            $whereParameters += ['precioProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.cuotaspagar like :cuotaspagarProducto";
            $whereParameters += ['cuotaspagarProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.cantidad like :cantidadProducto";
            $whereParameters += ['cantidadProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or categoria.nombre like :categoriaProducto";
            $whereParameters += ['categoriaProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or usuario.nombre like :usuarioProducto";
            $whereParameters += ['usuarioProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or estado.nombre like :estadoProducto";
            $whereParameters += ['estadoProducto' => '%' . $valorSearch . '%'];

            $flag = true;

        }


        $sql = "select 

                  producto                  
                  
              from 
              
              AdministracionBundle:Producto producto 
              LEFT JOIN  producto.estadoProductoid estado
              LEFT JOIN  producto.ofertaid oferta
              LEFT JOIN  producto.categoriaid categoria
              LEFT JOIN  producto.usuarioid usuario
              LEFT JOIN  producto.coleccionid coleccion
             
              " . $where . "   
              
             
              
              
             ";

        $dql = $em->createQuery($sql);

        $dql->setParameters($whereParameters);


        return $dql;
    }

    public function findByProductoHomePage($categoria)
    {
        $em = $this->getEntityManager();

        $where = "where";

        $ids = "";

        $ids = $this->idsCategoria($categoria, $ids);


        $whereParameters = array();

        if ($ids == "") {
            $where = $where . " producto.categoriaid IN (:idCategoria)";
            $whereParameters = ['idCategoria' => $categoria->getId()];
        } else {
            $ids = explode(':', $ids);

            $categoriasIds = "";

            foreach ($ids as $id) {
                if ($id != "") {
                    $id = explode("-", $id);

                    $categoriasIds .= $id[0] . ",";
                }
            }

            $categoriasIds = substr($categoriasIds, 0, -1);


            $where = $where . " producto.categoriaid IN (:idCategoria)";
            $whereParameters = ['idCategoria' => $categoriasIds];
        }

        $sql = "select 

                  producto
                  
              from 
              
              AdministracionBundle:Producto producto 
              INNER JOIN  producto.estadoProductoid estado
              LEFT JOIN  producto.ofertaid oferta
              INNER JOIN  producto.categoriaid categoria
              INNER JOIN  producto.usuarioid usuario
              LEFT JOIN  producto.coleccionid coleccion
              INNER JOIN  producto.condicion condicion
              INNER JOIN  usuario.direccion direccion              
              INNER JOIN  direccion.ciudad ciudad
              LEFT JOIN  producto.campannaid campanna
              
             
              " . $where . "   
              
            
              
              GROUP BY producto.id
              
              ORDER BY producto.ranking DESC
              
              
             ";

        $dql = $em->createQuery($sql);

        $dql->setParameters($whereParameters);

        $dql->setMaxResults(6);

        return $dql;
    }

    public function findBySearchParam($param)
    {


        $qb = $this->createQueryBuilder('p');
        $qb->join('p.estadoProductoid', 'e')
            ->where('p.activo = 1')
            ->andWhere('e.slug <> :pausado')
            ->orWhere('e.slug <> :bloqueado')
            ->setParameter('pausado', 'pausado')
            ->setParameter('bloqueado', 'bloqueado');
//        $query="select p from AdministracionBundle:Producto p WHERE p.activo=1 AND p.nombre LIKE ".$param;
        if ($param != "") {
            $param = "'%" . $param . "%'";
            $qb->andWhere('p.nombre like :cadena')
                ->andWhere('p.descripcion like :cadena');
            $qb->setParameter('cadena', $param);
        }
        
        return $qb->getQuery()->getResult();
    }
    
    public function findByFilters($filters = array(), $maxResults = null) {
        
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.estadoProductoid', 'e');
        
        if (isset($filters['activo'])) {
            $qb->andWhere($qb->expr()->eq('p.activo', ':activo'))
                ->setParameter('activo', $filters['activo']);
        }
        
        if (isset($filters['estados_slug'])) {
            $qb->andWhere($qb->expr()->in('e.slug', ':estados_slug'))
                    ->setParameter('estados_slug', $filters['estados_slug']);
        }
        
        if(isset($filters['nombre_o_descripcion'])) {
            $qb->andWhere($qb->expr()->orX($qb->expr()->like('p.nombre',':cadena'),$qb->expr()->like('p.descripcion', ':cadena')))
                ->setParameter('cadena', '%'.$filters['nombre_o_descripcion'].'%');
        }
        
        if (isset($filters['nombre'])) {
            $qb->andWhere($qb->expr()->like('p.nombre',':nombre'))
                ->setParameter('nombre', '%'.$filters['nombre'].'%');
        }
        
        if (isset($filters['descripcion'])) {
            $qb->andWhere($qb->expr()->like('p.descripcion', ':descripcion'))
                ->setParameter('descripcion', '%'.$filters['descripcion'].'%');
        }
        
        if($maxResults) {
            $qb->setMaxResults($maxResults);
        }
        
        return $qb->getQuery()->getResult();
    }

    public function findByProductoPublic(Request $request, $paginado = true)
    {


        $em = $this->getEntityManager();
        $where = "where";
        $having = "";
        $orderBy = "";

        $flag = false;

        $whereParameters = array();

        $valorSearch = $request->request->get('valorSearch');

        $precioMin = (double)$request->request->get('precioMin');

        $precioMax = (double)$request->request->get('precioMax');

        $categoriaid = $request->request->get('categoriaid');

        $condicionid = $request->request->get('condicionid');

        $ciudadid = $request->request->get('ciudadid');

        $tipoenvioid = $request->request->get('tipoenvioid');

        $campannaid = $request->request->get('campannaid');

        $usuarioid = $request->request->get('usuarioid');

        $start = $request->request->get('start');
        $offset = $request->request->get('offset');
        if ($valorSearch) {
            $where = $where . " (producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.descripcion like :descripcionProducto)";
            $whereParameters += ['descripcionProducto' => '%' . $valorSearch . '%'];
            $flag = true;
        }

        if ($precioMax > 0) {
            if ($flag) {
                $where = $where . " and producto.precio <= :precioMax";
                $whereParameters += ['precioMax' => $precioMax];
            } else {
                $where = $where . " producto.precio <= :precioMax";
                $whereParameters = ['precioMax' => $precioMax];
            }
            $flag = true;
        }
        if ($precioMin > 0) {
            if ($flag) {
                $where = $where . " and producto.precio >= :precioMin";
                $whereParameters += ['precioMin' => $precioMin];
            } else {
                $where = $where . " producto.precio >= :precioMin";
                $whereParameters = ['precioMin' => $precioMin];
            }
            $flag = true;
        }


        if ($categoriaid) {
            $whereCat = "";
            $flagCat = false;
            $categoria = null;

            if (is_numeric($categoriaid)) {
                $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($categoriaid);
            } else {
                $categoria = $em->getRepository('AdministracionBundle:Categoria')->findOneBy(array('slug' => $categoriaid));
            }

            $ids = "";

            $ids = $this->idsCategoria($categoria, $ids);

            $ids = explode(':', $ids);

            foreach ($ids as $id) {
                if ($id != "") {
                    $id = explode("-", $id);

                    if ($flagCat) {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . " or  categoria.id = :" . $c;
                        $whereParameters += [$c => $id[0]];
                    } else {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . "  categoria.id = :" . $c . " ";
                        $whereParameters += [$c => $id[0]];

                        $flagCat = true;
                    }
                }

            }

            if ($flagCat) {
                if ($flag) {
                    $where .= " and(" . $whereCat . ")";
                } else {
                    $where .= " (" . $whereCat . ")";

                    $flag = true;
                }
            } else {
                $where = $where . "  categoria.id = :idCategoria ";
                $whereParameters += ["idCategoria" => $categoriaid];

                $flag = true;
            }


        }

        if ($condicionid) {

            if ($flag) {
                $where = $where . " and condicion.slug = :condicionid";
                $whereParameters += ['condicionid' => $condicionid];
            } else {
                $where = $where . " condicion.slug = :condicionid";
                $whereParameters = ['condicionid' => $condicionid];
            }

            $flag = true;
        }

        if ($campannaid) {
            if ($flag) {
                $where = $where . " and campanna.id = :campannaid";
                $whereParameters += ['campannaid' => $campannaid];
            } else {
                $where = $where . " campanna.id = :campannaid";
                $whereParameters += ['campannaid' => $campannaid];
            }

            $flag = true;
        }

        if ($usuarioid) {
            if ($flag) {
                $where = $where . " and usuario.id = :usuarioid";
                $whereParameters += ['usuarioid' => $usuarioid];
            } else {
                $where = $where . " usuario.id = :usuarioid";
                $whereParameters += ['usuarioid' => $usuarioid];
            }

            $flag = true;
        }


        if ($ciudadid) {
            $ciudadid = explode(',', $ciudadid);

            if ($flag) {
                $where = $where . " and ciudad.id IN ( :ciudadid )";
                $whereParameters += ['ciudadid' => $ciudadid];
            } else {
                $where = $where . " ciudad.id IN ( :ciudadid )";
                $whereParameters = ['ciudadid' => $ciudadid];
            }
            $flag = true;
        }

        /*if($tipoenvioid)
        {
            $tipoenvioid= substr($tipoenvioid,0,-1);

            $tipoenvioid= explode(',',$tipoenvioid);

                if ($flag)
                {
                $where=$where." and tipoenvio.id IN ( :tipoenvioid )";
                $whereParameters+=['tipoenvioid'=>$tipoenvioid];
            }
            else
            {
                $where=$where." tipoenvio.id IN ( :tipoenvioid )";
                $whereParameters=['tipoenvioid'=>$tipoenvioid];
            }

            $flag= true;
        }*/

        if ($flag) {
            $where = $where . " and estado.id = :estadoid";
            $whereParameters += ['estadoid' => 3];

            $flag = true;
        } else {
            $where = $where . " estado.id = :estadoid";
            $whereParameters += ['estadoid' => 3];

            $flag = true;
        }

        if ($flag == false) {
            $where = "";
        }
        $sql = "select 
                  producto
              from 
              AdministracionBundle:Producto producto 
              JOIN  producto.estadoProductoid estado
              LEFT JOIN  producto.ofertaid oferta
              JOIN  producto.categoriaid categoria
              JOIN  producto.usuarioid usuario
              LEFT JOIN  producto.coleccionid coleccion
              JOIN  producto.condicion condicion
              LEFT JOIN  usuario.direccion direccion              
              LEFT JOIN  direccion.ciudad ciudad
              " . $where . "   
              GROUP BY producto.id
              ORDER BY producto.ranking DESC
             ";
        $dql = $em->createQuery($sql);
        $dql->setParameters($whereParameters);

        if ($paginado) {
            $dql->setMaxResults($offset);
            $dql->setFirstResult($start);
        }
        return $dql;
    }

    public function findByProductoPublicTotal(Request $request)
    {

        $em = $this->getEntityManager();

        $where = "where";
        $having = "";
        $orderBy = "";

        $flag = false;

        $whereParameters = array();

        $valorSearch = $request->request->get('valorSearch');

        $precioMin = (int)$request->request->get('precioMin');

        $precioMax = (int)$request->request->get('precioMax');

        $categoriaid = $request->request->get('categoriaid');

        if ($categoriaid == null) {
            $categoriaid = $request->query->get('categoriaid');
        }

        $condicionid = $request->request->get('condicionid');

        $ciudadid = $request->request->get('ciudadid');

        //$tipoenvioid=$request->request->get('tipoenvioid');

        $campannaid = $request->request->get('campannaid');

        $usuarioid = $request->request->get('usuarioid');


        if ($valorSearch) {
            $where = $where . " producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $flag = true;
        }

        if ($campannaid) {
            if ($flag) {
                $where = $where . " and campanna.id = :campannaid";
                $whereParameters += ['campannaid' => $campannaid];
            } else {
                $where = $where . " campanna.id = :campannaid";
                $whereParameters += ['campannaid' => $campannaid];
            }

            $flag = true;
        }

        if ($usuarioid) {
            if ($flag) {
                $where = $where . " and usuario.id = :usuarioid";
                $whereParameters += ['usuarioid' => $usuarioid];
            } else {
                $where = $where . " usuario.id = :usuarioid";
                $whereParameters += ['usuarioid' => $usuarioid];
            }

            $flag = true;
        }

        if ($precioMax) {
            if ($flag) {
                $where = $where . " and producto.precio <= :precioMax";
                $whereParameters += ['precioMax' => $precioMax];
            } else {
                $where = $where . " producto.precio <= :precioMax";
                $whereParameters = ['precioMax' => $precioMax];
            }

            $flag = true;
        }
        if ($precioMin) {
            if ($flag) {
                $where = $where . " and producto.precio >= :precioMin";
                $whereParameters += ['precioMin' => $precioMin];
            } else {
                $where = $where . " producto.precio >= :precioMin";
                $whereParameters = ['precioMin' => $precioMin];
            }

            $flag = true;
        }
        if ($categoriaid) {
            $whereCat = "";
            $flagCat = false;
            $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($categoriaid);

            $ids = "";

            $ids = $this->idsCategoria($categoria, $ids);

            $ids = explode(':', $ids);

            foreach ($ids as $id) {
                if ($id != "") {
                    $id = explode("-", $id);

                    if ($flagCat) {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . " or  categoria.id = :" . $c;
                        $whereParameters += [$c => $id[0]];
                    } else {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . "  categoria.id = :" . $c . " ";
                        $whereParameters += [$c => $id[0]];

                        $flagCat = true;

                    }

                    if (count($ids) > 1) {

                    }
                }

            }

            if ($flagCat) {
                if ($flag) {
                    $where .= " and(" . $whereCat . ")";
                } else {
                    $where .= " (" . $whereCat . ")";

                    $flag = true;
                }
            } else {
                $where = $where . "  categoria.id = :idCategoria ";
                $whereParameters += ["idCategoria" => $categoriaid];

                $flag = true;
            }
        }

        if ($condicionid) {
            if ($flag) {
                $where = $where . " and condicion.id = :condicionid";
                $whereParameters += ['condicionid' => $condicionid];
            } else {
                $where = $where . " condicion.id = :condicionid";
                $whereParameters = ['condicionid' => $condicionid];
            }

            $flag = true;
        }

        if ($ciudadid) {
            $ciudadid = substr($ciudadid, 0, -2);

            $ciudadid = explode(',', $ciudadid);

            if ($flag) {
                $where = $where . " and ciudad.id IN ( :ciudadid )";
                $whereParameters += ['ciudadid' => $ciudadid];
            } else {
                $where = $where . " ciudad.id IN ( :ciudadid )";
                $whereParameters = ['ciudadid' => $ciudadid];
            }

            $flag = true;
        }

        /* if($tipoenvioid)
        {
            $tipoenvioid= substr($tipoenvioid,0,-1);

            $tipoenvioid= explode(',',$tipoenvioid);

            if ($flag)
            {
                $where=$where." and tipoenvio.id IN ( :tipoenvioid )";
                $whereParameters+=['tipoenvioid'=>$tipoenvioid];
            }
            else
            {
                $where=$where." tipoenvio.id IN ( :tipoenvioid )";
                $whereParameters=['tipoenvioid'=>$tipoenvioid];
            }

            $flag= true;
        }*/

        if ($flag) {
            $where = $where . " and estado.id = :estadoid";
            $whereParameters += ['estadoid' => 3];

            $flag = true;
        } else {
            $where = $where . " estado.id = :estadoid";
            $whereParameters += ['estadoid' => 3];

            $flag = true;
        }

        if ($flag == false) {
            $where = "";
        }


        $sql = "select producto
                  
              from 
              
              AdministracionBundle:Producto producto 
              INNER JOIN  producto.estadoProductoid estado
              LEFT JOIN  producto.ofertaid oferta
              INNER JOIN  producto.categoriaid categoria
              INNER JOIN  producto.usuarioid usuario
              LEFT JOIN  producto.coleccionid coleccion
              INNER JOIN  producto.condicion condicion
              INNER JOIN  usuario.direccion direccion              
              INNER JOIN  direccion.ciudad ciudad
              LEFT JOIN  producto.campannaid campanna
             
              " . $where . "   
              
             
              
              GROUP BY producto.id
              
              
             ";

        $dql = $em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }

    public function findByProductoPanelUsuario(Request $request)
    {
        $start = $request->request->get('start');
        
        $dql = $this->findByProductoPanelUsuarioTotal($request, "producto.ranking DESC");

        $dql->setMaxResults(6);
        $dql->setFirstResult($start);

        return $dql;
    }

    public function findByProductoPanelUsuarioTotal(Request $request, $orderBy="")
    {

        $em = $this->getEntityManager();

        $where = "where";
        $having = "";

        $flag = false;

        $whereParameters = array();

        $valorSearch = $request->request->get('valorSearch');

        $precioMin = (int)$request->request->get('precioMin');

        $precioMax = (int)$request->request->get('precioMax');

        $categoriaid = $request->request->get('categoriaid');

        if ($categoriaid == null) {
            $categoriaid = $request->query->get('categoriaid');
        }

        $condicionid = $request->request->get('condicionid');

        $ciudadid = $request->request->get('ciudadid');

        //$tipoenvioid=$request->request->get('tipoenvioid');

        $campannaid = $request->request->get('campannaid');

        $usuarioid = $request->request->get('usuarioid');

        if ($valorSearch) {
            $where = $where . " producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $flag = true;
        }

        

        

        if ($precioMax) {
            if ($flag) {
                $where = $where . " and producto.precio <= :precioMax";
                $whereParameters += ['precioMax' => $precioMax];
            } else {
                $where = $where . " producto.precio <= :precioMax";
                $whereParameters = ['precioMax' => $precioMax];
            }

            $flag = true;
        }
        if ($precioMin) {
            if ($flag) {
                $where = $where . " and producto.precio >= :precioMin";
                $whereParameters += ['precioMin' => $precioMin];
            } else {
                $where = $where . " producto.precio >= :precioMin";
                $whereParameters = ['precioMin' => $precioMin];
            }

            $flag = true;
        }
        if ($categoriaid) {
            $whereCat = "";
            $flagCat = false;
            $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($categoriaid);

            $ids = "";

            $ids = $this->idsCategoria($categoria, $ids);

            $ids = explode(':', $ids);

            foreach ($ids as $id) {
                if ($id != "") {
                    $id = explode("-", $id);

                    if ($flagCat) {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . " or  categoria.id = :" . $c;
                        $whereParameters += [$c => $id[0]];
                    } else {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . "  categoria.id = :" . $c . " ";
                        $whereParameters += [$c => $id[0]];

                        $flagCat = true;

                    }
                }

            }

            if ($flagCat) {
                if ($flag) {
                    $where .= " and(" . $whereCat . ")";
                } else {
                    $where .= " (" . $whereCat . ")";

                    $flag = true;
                }
            } else {
                $where = $where . "  categoria.id = :idCategoria ";
                $whereParameters += ["idCategoria" => $categoriaid];

                $flag = true;
            }
        }

        if ($condicionid) {
            if ($flag) {
                $where = $where . " and condicion.id = :condicionid";
                $whereParameters += ['condicionid' => $condicionid];
            } else {
                $where = $where . " condicion.id = :condicionid";
                $whereParameters = ['condicionid' => $condicionid];
            }

            $flag = true;
        }
        if ($campannaid) {
            if ($flag) {
                $where = $where . " and campanna.id = :campannaid";
                $whereParameters += ['campannaid' => $campannaid];
            } else {
                $where = $where . " campanna.id = :campannaid";
                $whereParameters += ['campannaid' => $campannaid];
            }

            $flag = true;
        }
        if ($usuarioid) {
            if ($flag) {
                $where = $where . " and usuario.id = :usuarioid";
                $whereParameters += ['usuarioid' => $usuarioid];
            } else {
                $where = $where . " usuario.id = :usuarioid";
                $whereParameters += ['usuarioid' => $usuarioid];
            }

            $flag = true;
        }

        if ($ciudadid) {
            $ciudadid = substr($ciudadid, 0, -2);

            $ciudadid = explode(',', $ciudadid);

            if ($flag) {
                $where = $where . " and ciudad.id IN ( :ciudadid )";
                $whereParameters += ['ciudadid' => $ciudadid];
            } else {
                $where = $where . " ciudad.id IN ( :ciudadid )";
                $whereParameters = ['ciudadid' => $ciudadid];
            }

            $flag = true;
        }

        /*if($tipoenvioid)
        {
            $tipoenvioid= substr($tipoenvioid,0,-1);

            $tipoenvioid= explode(',',$tipoenvioid);

            if ($flag)
            {
                $where=$where." and tipoenvio.id IN ( :tipoenvioid )";
                $whereParameters+=['tipoenvioid'=>$tipoenvioid];
            }
            else
            {
                $where=$where." tipoenvio.id IN ( :tipoenvioid )";
                $whereParameters=['tipoenvioid'=>$tipoenvioid];
            }

            $flag= true;
        }*/
        if ($flag != false) {
            $where .= " and";
        }
        
        $where .= " producto.borrado = false";


        $sql = "select 

                  producto
                  
              from 
              
              AdministracionBundle:Producto producto 
              LEFT JOIN  producto.estadoProductoid estado
              LEFT JOIN  producto.ofertaid oferta
              LEFT JOIN  producto.categoriaid categoria
              LEFT JOIN  producto.usuarioid usuario
              LEFT JOIN  producto.coleccionid coleccion
              LEFT JOIN  producto.condicion condicion
              LEFT JOIN  usuario.direccion direccion              
              LEFT JOIN  direccion.ciudad ciudad
              LEFT JOIN  producto.campannaid campanna
              
              " . $where . "   
              
              GROUP BY producto.id
             ";
        
        if($orderBy) {
            $sql .= " ORDER BY ".$orderBy;
        }

        $dql = $em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }

    public function findByProductoNuevoUso(Request $request, $condicionid)
    {
        $em = $this->getEntityManager();

        $where = "";
        $having = "";
        $orderBy = "";

        $flag = true;

        $whereParameters = [];

        $whereParameters = ['condicionid' => $condicionid];

        $categoriaid = $request->query->get('categoriaid');

        $valorSearch = $request->request->get('valorSearch');

        $precioMin = (int)$request->request->get('precioMin');

        $precioMax = (int)$request->request->get('precioMax');

        if ($categoriaid == null) {
            $categoriaid = $request->request->get('categoriaid');
        }

        $ciudadid = $request->request->get('ciudadid');

        //$tipoenvioid=$request->request->get('tipoenvioid');

        if ($valorSearch) {
            $where = $where . " producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $flag = true;
        }
        if ($precioMax) {
            if ($flag) {
                $where = $where . " and producto.precio <= :precioMax";
                $whereParameters += ['precioMax' => $precioMax];
            } else {
                $where = $where . " producto.precio <= :precioMax";
                $whereParameters = ['precioMax' => $precioMax];
            }

            $flag = true;
        }
        if ($precioMin) {
            if ($flag) {
                $where = $where . " and producto.precio >= :precioMin";
                $whereParameters += ['precioMin' => $precioMin];
            } else {
                $where = $where . " producto.precio >= :precioMin";
                $whereParameters = ['precioMin' => $precioMin];
            }

            $flag = true;
        }


        $flag = false;

        if ($categoriaid) {
            $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($categoriaid);

            $ids = "";

            $ids = $this->idsCategoria($categoria, $ids);

            $ids = explode(':', $ids);

            foreach ($ids as $id) {
                if ($id != "") {
                    $id = explode("-", $id);

                    if ($flag) {
                        $c = "idCategoria" . $id[0];
                        $where = $where . " or  categoria.id = :" . $c;
                        $whereParameters += [$c => $id[0]];
                    } else {
                        $c = "idCategoria" . $id[0];
                        $where = $where . "  categoria.id = :" . $c . " ";
                        $whereParameters += [$c => $id[0]];

                        $flag = true;

                    }

                    if (count($ids) > 1) {

                    }
                }

            }

        }
        if ($ciudadid) {
            if ($flag) {
                $where = $where . " and ciudad.id = :ciudadid";
                $whereParameters += ['ciudadid' => $ciudadid];
            } else {
                $where = $where . " ciudad.id = :ciudadid";
                $whereParameters = ['ciudadid' => $ciudadid];
            }

            $flag = true;
        }

        /* if($tipoenvioid)
        {
            if ($flag)
            {
                $where=$where." and tipoenvio.id = :tipoenvioid";
                $whereParameters+=['tipoenvioid'=>$tipoenvioid];
            }
            else
            {
                $where=$where." tipoenvio.id = :tipoenvioid";
                $whereParameters=['tipoenvioid'=>$tipoenvioid];
            }

            $flag= true;
        }*/

        $sql = "select 

                  count(producto.id)
                  
              from 
              
              AdministracionBundle:Producto producto 
              INNER JOIN  producto.condicion condicion           
              INNER JOIN  producto.categoriaid categoria
              
              WHERE 
              condicion.id=:condicionid AND 
              (" . $where . ")
              
             ";

        $dql = $em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }

    public function findByProductoCiudad(Request $request)
    {
        $em = $this->getEntityManager();

        $where = "where";
        $having = "";
        $orderBy = "";

        $flag = false;

        $whereParameters = [];


        $categoriaid = $request->query->get('categoriaid');

        $valorSearch = $request->request->get('valorSearch');

        $precioMin = (int)$request->request->get('precioMin');

        $precioMax = (int)$request->request->get('precioMax');

        if ($categoriaid == null) {
            $categoriaid = $request->request->get('categoriaid');
        }

        $condicionid = $request->request->get('condicionid');

        //$tipoenvioid=$request->request->get('tipoenvioid');

        if ($valorSearch) {
            $where = $where . " producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $flag = true;
        }
        if ($precioMax) {
            if ($flag) {
                $where = $where . " and producto.precio <= :precioMax";
                $whereParameters += ['precioMax' => $precioMax];
            } else {
                $where = $where . " producto.precio <= :precioMax";
                $whereParameters = ['precioMax' => $precioMax];
            }

            $flag = true;
        }
        if ($precioMin) {
            if ($flag) {
                $where = $where . " and producto.precio >= :precioMin";
                $whereParameters += ['precioMin' => $precioMin];
            } else {
                $where = $where . " producto.precio >= :precioMin";
                $whereParameters = ['precioMin' => $precioMin];
            }

            $flag = true;
        }


        if ($categoriaid) {
            $whereCat = "";
            $flagCat = false;
            if (is_numeric($categoriaid)) {
                $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($categoriaid);
            } else {
                $categoria = $em->getRepository('AdministracionBundle:Categoria')->findOneBy(array('slug' => $categoriaid));
            }

            $ids = "";

            $ids = $this->idsCategoria($categoria, $ids);

            $ids = explode(':', $ids);

            foreach ($ids as $id) {
                if ($id != "") {
                    $id = explode("-", $id);

                    if ($flagCat) {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . " or  categoria.id = :" . $c;
                        $whereParameters += [$c => $id[0]];
                    } else {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . "  categoria.id = :" . $c . " ";
                        $whereParameters += [$c => $id[0]];

                        $flagCat = true;

                    }

                }

            }

            if ($flagCat) {
                if ($flag) {
                    $where .= " and(" . $whereCat . ")";
                } else {
                    $where .= " (" . $whereCat . ")";

                    $flag = true;
                }
            } else {
                $where = $where . "  categoria.id = :idCategoria ";
                $whereParameters += ["idCategoria" => $categoriaid];

                $flag = true;
            }

        }


        if ($condicionid) {
            if ($flag) {
                $where = $where . " and condicion.slug = :condicionid";
                $whereParameters += ['condicionid' => $condicionid];
            } else {
                $where = $where . " condicion.slug = :condicionid";
                $whereParameters = ['condicionid' => $condicionid];
            }

            $flag = true;
        }

        /* if($tipoenvioid)
        {
            if ($flag)
            {
                $where=$where." and tipoenvio.id = :tipoenvioid";
                $whereParameters+=['tipoenvioid'=>$tipoenvioid];
            }
            else
            {
                $where=$where." tipoenvio.id = :tipoenvioid";
                $whereParameters=['tipoenvioid'=>$tipoenvioid];
            }

            $flag= true;
        }*/
        if ($flag == false) {
            $where = "";
        }

        $sql = "select 
                  ciudad.ciudadNombre,
                  ciudad.id,
                  count(ciudad.id)
              from 
              
              AdministracionBundle:Producto producto 
              INNER JOIN  producto.usuarioid usuario
              INNER JOIN  usuario.direccion direccion
              INNER JOIN  producto.categoriaid categoria
              INNER JOIN  direccion.ciudad ciudad              
              INNER JOIN  producto.estadoProductoid estado
              INNER JOIN  producto.condicion condicion
              " . $where . "
              GROUP BY ciudad.id
             ";

        $dql = $em->createQuery($sql);

        $dql->setParameters($whereParameters);
        return $dql;
    }

    public function findByProductoTipoEnvio(Request $request)
    {
        $em = $this->getEntityManager();

        $where = "where";
        $having = "";
        $orderBy = "";

        $flag = false;

        $whereParameters = [];

        $flag = false;

        $categoriaid = $request->query->get('categoriaid');

        $valorSearch = $request->request->get('valorSearch');

        $precioMin = (int)$request->request->get('precioMin');

        $precioMax = (int)$request->request->get('precioMax');

        $ciudadid = $request->request->get('ciudadid');

        if ($categoriaid == null) {
            $categoriaid = $request->request->get('categoriaid');
        }

        $condicionid = $request->request->get('condicionid');

        if ($valorSearch) {
            $where = $where . " producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $flag = true;
        }
        if ($precioMax) {
            if ($flag) {
                $where = $where . " and producto.precio <= :precioMax";
                $whereParameters += ['precioMax' => $precioMax];
            } else {
                $where = $where . " producto.precio <= :precioMax";
                $whereParameters = ['precioMax' => $precioMax];
            }

            $flag = true;
        }
        if ($precioMin) {
            if ($flag) {
                $where = $where . " and producto.precio >= :precioMin";
                $whereParameters += ['precioMin' => $precioMin];
            } else {
                $where = $where . " producto.precio >= :precioMin";
                $whereParameters = ['precioMin' => $precioMin];
            }

            $flag = true;
        }

        if ($ciudadid) {
            if ($flag) {
                $where = $where . " and ciudad.id = :ciudadid";
                $whereParameters += ['ciudadid' => $ciudadid];
            } else {
                $where = $where . " ciudad.id = :ciudadid";
                $whereParameters = ['ciudadid' => $ciudadid];
            }

            $flag = true;
        }


        if ($categoriaid) {
            $whereCat = "";
            $flagCat = false;
            $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($categoriaid);

            $ids = "";

            $ids = $this->idsCategoria($categoria, $ids);

            $ids = explode(':', $ids);

            foreach ($ids as $id) {
                if ($id != "") {
                    $id = explode("-", $id);

                    if ($flagCat) {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . " or  categoria.id = :" . $c;
                        $whereParameters += [$c => $id[0]];
                    } else {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . "  categoria.id = :" . $c . " ";
                        $whereParameters += [$c => $id[0]];

                        $flagCat = true;

                    }

                    if (count($ids) > 1) {

                    }
                }

            }

            if ($flagCat) {
                if ($flag) {
                    $where .= " and(" . $whereCat . ")";
                } else {
                    $where .= " (" . $whereCat . ")";

                    $flag = true;
                }
            } else {
                $where = $where . "  categoria.id = :idCategoria ";
                $whereParameters += ["idCategoria" => $categoriaid];

                $flag = true;
            }

        }


        if ($condicionid) {
            if ($flag) {
                $where = $where . " and condicion.id = :condicionid";
                $whereParameters += ['condicionid' => $condicionid];
            } else {
                $where = $where . " condicion.id = :condicionid";
                $whereParameters = ['condicionid' => $condicionid];
            }

            $flag = true;
        }

        if ($flag == false) {
            $where = "";
        }

        $sql = "select 
                  tipoenvio.nombre,
                  tipoenvio.id,
                  count(producto.id)
                  
              from 
              
              AdministracionBundle:Producto producto 
              INNER JOIN  producto.tipoenvioid tipoenvio
               INNER JOIN  producto.categoriaid categoria              
              INNER JOIN  producto.usuarioid usuario
              INNER JOIN  usuario.direccion direccion
              INNER JOIN  direccion.ciudad ciudad              
              INNER JOIN  producto.estadoProductoid estado
              INNER JOIN  producto.condicion condicion
              
              
              
              " . $where . "
              
              GROUP BY tipoenvio.id
              
             ";

        $dql = $em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }


    public function idsCategoria($categoria, $ids)
    {
        $ids2 = "";
        if (is_object($categoria)) {
            if (count($categoria->getCategoriaHijas()) != 0) {
                $hijas = $categoria->getCategoriaHijas();

                foreach ($hijas as $hija) {
                    $ids2 = $this->idsCategoria($hija, $ids2);
                }

            } else if (count($categoria->getProductos()) != 0) {
                $ids2 .= ":" . $categoria->getId() . "-" . ($categoria->getCategoriaid() != null ? $categoria->getCategoriaid()->getId() : 1);

            }
        }
        return $ids .= $ids2;
    }


    public function findByProductoOfertaSemana(Request $request)
    {
        $em = $this->getEntityManager();

        $where = "where";
        $having = "";
        $orderBy = "";

        $flag = false;

        $whereParameters = array();

        $valorSearch = $request->request->get('valorSearch');

        $precioMin = (int)$request->request->get('precioMin');

        $precioMax = (int)$request->request->get('precioMax');

        $categoriaid = $request->request->get('categoriaid');

        $porcientoid = $request->request->get('porcientoid');

        $condicionid = $request->request->get('condicionid');

        $ciudadid = $request->request->get('ciudadid');

        //$tipoenvioid=$request->request->get('tipoenvioid');

        $campannaid = $request->request->get('campannaid');

        $usuarioid = $request->request->get('usuarioid');

        $start = $request->request->get('start');

        if ($valorSearch) {
            $where = $where . " producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $flag = true;
        }

        if ($precioMax) {
            if ($flag) {
                $where = $where . " and producto.precio <= :precioMax";
                $whereParameters += ['precioMax' => $precioMax];
            } else {
                $where = $where . " producto.precio <= :precioMax";
                $whereParameters = ['precioMax' => $precioMax];
            }

            $flag = true;
        }
        if ($precioMin) {
            if ($flag) {
                $where = $where . " and producto.precio >= :precioMin";
                $whereParameters += ['precioMin' => $precioMin];
            } else {
                $where = $where . " producto.precio >= :precioMin";
                $whereParameters = ['precioMin' => $precioMin];
            }

            $flag = true;
        }
        if ($categoriaid) {
            $whereCat = "";
            $flagCat = false;
            $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($categoriaid);

            $ids = "";

            $ids = $this->idsCategoria($categoria, $ids);

            $ids = explode(':', $ids);

            foreach ($ids as $id) {
                if ($id != "") {
                    $id = explode("-", $id);

                    if ($flagCat) {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . " or  categoria.id = :" . $c;
                        $whereParameters += [$c => $id[0]];
                    } else {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . "  categoria.id = :" . $c . " ";
                        $whereParameters += [$c => $id[0]];

                        $flagCat = true;

                    }

                    if (count($ids) > 1) {

                    }
                }

            }

            if ($flagCat) {
                if ($flag) {
                    $where .= " and(" . $whereCat . ")";
                } else {
                    $where .= " (" . $whereCat . ")";

                    $flag = true;
                }
            } else {
                $where = $where . "  categoria.id = :idCategoria ";
                $whereParameters += ["idCategoria" => $categoriaid];

                $flag = true;
            }
        }

        if ($condicionid) {
            if ($flag) {
                $where = $where . " and condicion.id = :condicionid";
                $whereParameters += ['condicionid' => $condicionid];
            } else {
                $where = $where . " condicion.id = :condicionid";
                $whereParameters = ['condicionid' => $condicionid];
            }

            $flag = true;
        }
        if ($campannaid) {
            if ($flag) {
                $where = $where . " and campanna.id = :campannaid";
                $whereParameters += ['campannaid' => $campannaid];
            } else {
                $where = $where . " campanna.id = :campannaid";
                $whereParameters += ['campannaid' => $campannaid];
            }

            $flag = true;
        }

        if ($usuarioid) {
            if ($flag) {
                $where = $where . " and usuario.id = :usuarioid";
                $whereParameters += ['usuarioid' => $usuarioid];
            } else {
                $where = $where . " usuario.id = :usuarioid";
                $whereParameters += ['usuarioid' => $usuarioid];
            }

            $flag = true;
        }

        if ($ciudadid) {
            $ciudadid = substr($ciudadid, 0, -1);

            $ciudadid = explode(',', $ciudadid);

            if ($flag) {
                $where = $where . " and ciudad.id IN ( :ciudadid )";
                $whereParameters += ['ciudadid' => $ciudadid];
            } else {
                $where = $where . " ciudad.id IN ( :ciudadid )";
                $whereParameters = ['ciudadid' => $ciudadid];
            }

            $flag = true;
        }

        if ($porcientoid) {
            $porcientoid = substr($porcientoid, 0, -1);

            $porcientoid = explode(',', $porcientoid);

            if ($flag) {
                $where = $where . " and oferta.id IN ( :porcientoid )";
                $whereParameters += ['porcientoid' => $porcientoid];
            } else {
                $where = $where . " oferta.id IN ( :porcientoid )";
                $whereParameters = ['porcientoid' => $porcientoid];
            }

            $flag = true;
        }

        /*if($tipoenvioid)
        {
            $tipoenvioid= substr($tipoenvioid,0,-1);

            $tipoenvioid= explode(',',$tipoenvioid);

            if ($flag)
            {
                $where=$where." and tipoenvio.id IN ( :tipoenvioid )";
                $whereParameters+=['tipoenvioid'=>$tipoenvioid];
            }
            else
            {
                $where=$where." tipoenvio.id IN ( :tipoenvioid )";
                $whereParameters=['tipoenvioid'=>$tipoenvioid];
            }

            $flag= true;
        }*/
        $date = strtotime(date("Y-m-d"));

        $first = strtotime('last Sunday');

        $last = strtotime('next Saturday');

        $first = date("Y-m-d", $first);

        $last = date("Y-m-d", $last);

        $first = explode('-', $first);
        $last = explode('-', $last);
        $first = \DateTime::createFromFormat("Y-m-d H:i:s", date($first[0] . "-" . $first[1] . "-" . $first[2] . " " . "00:00:00"));
        $last = \DateTime::createFromFormat("Y-m-d H:i:s", date($last[0] . "-" . $last[1] . "-" . $last[2] . " " . "23:59:59"));
        if ($flag) {
            $where = $where . " and (oferta.fechainicio BETWEEN :fechainicioOferta and :fechafinOferta";

            $where = $where . " or oferta.fechafin BETWEEN :fechainicioOferta and :fechafinOferta)";

            $whereParameters += ['fechainicioOferta' => $first];
            $whereParameters += ['fechafinOferta' => $last];

            $flag = true;
        } else {
            $where = $where . " (oferta.fechainicio BETWEEN :fechainicioOferta and :fechafinOferta";

            $where = $where . " or oferta.fechafin BETWEEN :fechainicioOferta and :fechafinOferta)";

            $whereParameters += ['fechainicioOferta' => $first];
            $whereParameters += ['fechafinOferta' => $last];

            $flag = true;
        }
        if ($flag) {
            $where = $where . " and estado.id = :estadoid";
            $whereParameters += ['estadoid' => 3];

            $where = $where . " and estadoProducto.id = :estadoProductoid";
            $whereParameters += ['estadoProductoid' => 3];

            $flag = true;
        } else {
            $where = $where . " estado.id = :estadoid";
            $whereParameters += ['estadoid' => 3];

            $where = $where . " and estadoProducto.id = :estadoProductoid";
            $whereParameters += ['estadoProductoid' => 3];

            $flag = true;
        }

        if ($flag == false) {
            $where = "";
        }
        $sql = "select 
                  producto
              from 
              AdministracionBundle:Producto producto 
              INNER JOIN  producto.ofertaid oferta          
              INNER JOIN  oferta.estadoProductoid estado
              INNER JOIN  producto.estadoProductoid estadoProducto                 
              INNER JOIN  producto.categoriaid categoria
              INNER JOIN  producto.usuarioid usuario
              LEFT JOIN  producto.coleccionid coleccion
              INNER JOIN  producto.condicion condicion
              INNER JOIN  usuario.direccion direccion              
              INNER JOIN  direccion.ciudad ciudad
              LEFT JOIN  producto.campannaid campanna
              " . $where . "   
             ";
        $dql = $em->createQuery($sql);

        $dql->setParameters($whereParameters);

        $dql->setMaxResults(6);
        $dql->setFirstResult($start);

        return $dql;
    }

    public function findByProductoOfertaSemanaTotal(Request $request)
    {

        $em = $this->getEntityManager();

        $where = "where";
        $having = "";
        $orderBy = "";

        $flag = false;

        $whereParameters = array();

        $valorSearch = $request->request->get('valorSearch');

        $precioMin = (int)$request->request->get('precioMin');

        $precioMax = (int)$request->request->get('precioMax');

        $categoriaid = $request->request->get('categoriaid');

        $porcientoid = $request->request->get('porcientoid');

        $condicionid = $request->request->get('condicionid');

        $ciudadid = $request->request->get('ciudadid');

        // $tipoenvioid=$request->request->get('tipoenvioid');

        $campannaid = $request->request->get('campannaid');

        $usuarioid = $request->request->get('usuarioid');

        $start = $request->request->get('start');

        if ($valorSearch) {
            $where = $where . " producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $flag = true;
        }
        if ($precioMax) {
            if ($flag) {
                $where = $where . " and producto.precio <= :precioMax";
                $whereParameters += ['precioMax' => $precioMax];
            } else {
                $where = $where . " producto.precio <= :precioMax";
                $whereParameters = ['precioMax' => $precioMax];
            }

            $flag = true;
        }
        if ($precioMin) {
            if ($flag) {
                $where = $where . " and producto.precio >= :precioMin";
                $whereParameters += ['precioMin' => $precioMin];
            } else {
                $where = $where . " producto.precio >= :precioMin";
                $whereParameters = ['precioMin' => $precioMin];
            }

            $flag = true;
        }
        if ($categoriaid) {
            $whereCat = "";
            $flagCat = false;
            $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($categoriaid);

            $ids = "";

            $ids = $this->idsCategoria($categoria, $ids);

            $ids = explode(':', $ids);

            foreach ($ids as $id) {
                if ($id != "") {
                    $id = explode("-", $id);

                    if ($flagCat) {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . " or  categoria.id = :" . $c;
                        $whereParameters += [$c => $id[0]];
                    } else {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . "  categoria.id = :" . $c . " ";
                        $whereParameters += [$c => $id[0]];

                        $flagCat = true;

                    }

                    if (count($ids) > 1) {

                    }
                }

            }

            if ($flagCat) {
                if ($flag) {
                    $where .= " and(" . $whereCat . ")";
                } else {
                    $where .= " (" . $whereCat . ")";

                    $flag = true;
                }
            } else {
                $where = $where . "  categoria.id = :idCategoria ";
                $whereParameters += ["idCategoria" => $categoriaid];

                $flag = true;
            }


        }

        if ($condicionid) {
            if ($flag) {
                $where = $where . " and condicion.id = :condicionid";
                $whereParameters += ['condicionid' => $condicionid];
            } else {
                $where = $where . " condicion.id = :condicionid";
                $whereParameters = ['condicionid' => $condicionid];
            }

            $flag = true;
        }
        if ($campannaid) {
            if ($flag) {
                $where = $where . " and campanna.id = :campannaid";
                $whereParameters += ['campannaid' => $campannaid];
            } else {
                $where = $where . " campanna.id = :campannaid";
                $whereParameters += ['campannaid' => $campannaid];
            }

            $flag = true;
        }

        if ($usuarioid) {
            if ($flag) {
                $where = $where . " and usuario.id = :usuarioid";
                $whereParameters += ['usuarioid' => $usuarioid];
            } else {
                $where = $where . " usuario.id = :usuarioid";
                $whereParameters += ['usuarioid' => $usuarioid];
            }

            $flag = true;
        }

        if ($ciudadid) {
            $ciudadid = substr($ciudadid, 0, -1);

            $ciudadid = explode(',', $ciudadid);

            if ($flag) {
                $where = $where . " and ciudad.id IN ( :ciudadid )";
                $whereParameters += ['ciudadid' => $ciudadid];
            } else {
                $where = $where . " ciudad.id IN ( :ciudadid )";
                $whereParameters = ['ciudadid' => $ciudadid];
            }

            $flag = true;
        }

        if ($porcientoid) {
            $porcientoid = substr($porcientoid, 0, -1);

            $porcientoid = explode(',', $porcientoid);

            if ($flag) {
                $where = $where . " and oferta.id IN ( :porcientoid )";
                $whereParameters += ['porcientoid' => $porcientoid];
            } else {
                $where = $where . " oferta.id IN ( :porcientoid )";
                $whereParameters = ['porcientoid' => $porcientoid];
            }

            $flag = true;
        }

        /*if($tipoenvioid)
        {
            $tipoenvioid= substr($tipoenvioid,0,-1);

            $tipoenvioid= explode(',',$tipoenvioid);

            if ($flag)
            {
                $where=$where." and tipoenvio.id IN ( :tipoenvioid )";
                $whereParameters+=['tipoenvioid'=>$tipoenvioid];
            }
            else
            {
                $where=$where." tipoenvio.id IN ( :tipoenvioid )";
                $whereParameters=['tipoenvioid'=>$tipoenvioid];
            }

            $flag= true;
        }*/


        $date = strtotime(date("Y-m-d"));

        $first = strtotime('last Sunday');

        $last = strtotime('next Saturday');

        $first = date("Y-m-d", $first);

        $last = date("Y-m-d", $last);

        $first = explode('-', $first);
        $last = explode('-', $last);
        $first = \DateTime::createFromFormat("Y-m-d H:i:s", date($first[0] . "-" . $first[1] . "-" . $first[2] . " " . "00:00:00"));
        $last = \DateTime::createFromFormat("Y-m-d H:i:s", date($last[0] . "-" . $last[1] . "-" . $last[2] . " " . "23:59:59"));


        if ($flag) {
            $where = $where . " and (oferta.fechainicio BETWEEN :fechainicioOferta and :fechafinOferta";

            $where = $where . " or oferta.fechafin BETWEEN :fechainicioOferta and :fechafinOferta)";

            $whereParameters += ['fechainicioOferta' => $first];
            $whereParameters += ['fechafinOferta' => $last];

            $flag = true;

        } else {
            $where = $where . " (oferta.fechainicio BETWEEN :fechainicioOferta and :fechafinOferta";

            $where = $where . " or oferta.fechafin BETWEEN :fechainicioOferta and :fechafinOferta)";

            $whereParameters += ['fechainicioOferta' => $first];
            $whereParameters += ['fechafinOferta' => $last];

            $flag = true;
        }


        if ($flag) {
            $where = $where . " and estado.id = :estadoid";
            $whereParameters += ['estadoid' => 3];

            $where = $where . " and estadoProducto.id = :estadoProductoid";
            $whereParameters += ['estadoProductoid' => 3];

            $flag = true;
        } else {
            $where = $where . " estado.id = :estadoid";
            $whereParameters += ['estadoid' => 3];

            $where = $where . " and estadoProducto.id = :estadoProductoid";
            $whereParameters += ['estadoProductoid' => 3];

            $flag = true;
        }

        if ($flag == false) {
            $where = "";
        }


        $sql = "select 
                  producto
              from 
              AdministracionBundle:Producto producto 
              INNER JOIN  producto.ofertaid oferta          
              INNER JOIN  oferta.estadoProductoid estado
              INNER JOIN  producto.estadoProductoid estadoProducto                 
              INNER JOIN  producto.categoriaid categoria
              INNER JOIN  producto.usuarioid usuario
              LEFT JOIN  producto.coleccionid coleccion
              INNER JOIN  producto.condicion condicion
              INNER JOIN  usuario.direccion direccion              
              INNER JOIN  direccion.ciudad ciudad
              LEFT JOIN  producto.campannaid campanna
              " . $where . "   
             ";
        $dql = $em->createQuery($sql);

        $dql->setParameters($whereParameters);

        return $dql;
    }

    public function findProductosEnOferta(Request $request, $paginado = true)
    {


        $em = $this->getEntityManager();
        $whereOferta = " where ";
        $where = "where";
        $having = "";
        $orderBy = "";

        $flag = false;

        $whereParameters = array();

        $valorSearch = $request->request->get('valorSearch');

        $precioMin = (double)$request->request->get('precioMin');

        $precioMax = (double)$request->request->get('precioMax');

        $categoriaid = $request->request->get('categoriaid');

        $condicionid = $request->request->get('condicionid');

        $ciudadid = $request->request->get('ciudadid');

        $tipoenvioid = $request->request->get('tipoenvioid');

        $campannaid = $request->request->get('campannaid');

        $usuarioid = $request->request->get('usuarioid');

        $start = $request->request->get('start');
        $offset = $request->request->get('offset');
        if ($valorSearch) {
            $where = $where . " (producto.nombre like :nombreProducto";
            $whereParameters = ['nombreProducto' => '%' . $valorSearch . '%'];

            $where = $where . " or producto.descripcion like :descripcionProducto)";
            $whereParameters += ['descripcionProducto' => '%' . $valorSearch . '%'];
            $flag = true;
        }

        if ($precioMax > 0) {
            if ($flag) {
                $where = $where . " and (producto.precio - ((oferta.porcientodescuento*producto.precio)/100) <= :precioMax)";
                $whereParameters += ['precioMax' => $precioMax];
            } else {
                $where = $where . " (producto.precio - ((oferta.porcientodescuento*producto.precio)/100) <= :precioMax)";
                $whereParameters = ['precioMax' => $precioMax];
            }
            $flag = true;
        }
        if ($precioMin > 0) {
            if ($flag) {
                $where = $where . " and (producto.precio - ((oferta.porcientodescuento*producto.precio)/100) >= :precioMin)";
                $whereParameters += ['precioMin' => $precioMin];
            } else {
                $where = $where . " (producto.precio - ((oferta.porcientodescuento*producto.precio)/100) >= :precioMin)";
                $whereParameters = ['precioMin' => $precioMin];
            }
            $flag = true;
        }


        if ($categoriaid) {
            $whereCat = "";
            $flagCat = false;
            $categoria = null;

            if (is_numeric($categoriaid)) {
                $categoria = $em->getRepository('AdministracionBundle:Categoria')->find($categoriaid);
            } else {
                $categoria = $em->getRepository('AdministracionBundle:Categoria')->findOneBy(array('slug' => $categoriaid));
            }

            $ids = "";

            $ids = $this->idsCategoria($categoria, $ids);

            $ids = explode(':', $ids);

            foreach ($ids as $id) {
                if ($id != "") {
                    $id = explode("-", $id);

                    if ($flagCat) {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . " or  categoria.id = :" . $c;
                        $whereParameters += [$c => $id[0]];
                    } else {
                        $c = "idCategoria" . $id[0];
                        $whereCat = $whereCat . "  categoria.id = :" . $c . " ";
                        $whereParameters += [$c => $id[0]];

                        $flagCat = true;
                    }
                }

            }

            if ($flagCat) {
                if ($flag) {
                    $where .= " and(" . $whereCat . ")";
                } else {
                    $where .= " (" . $whereCat . ")";

                    $flag = true;
                }
            } else {
                $where = $where . "  categoria.id = :idCategoria ";
                $whereParameters += ["idCategoria" => $categoriaid];

                $flag = true;
            }


        }

        if ($condicionid) {

            if ($flag) {
                $where = $where . " and condicion.slug = :condicionid";
                $whereParameters += ['condicionid' => $condicionid];
            } else {
                $where = $where . " condicion.slug = :condicionid";
                $whereParameters = ['condicionid' => $condicionid];
            }

            $flag = true;
        }

        if ($campannaid) {
            if ($flag) {
                $where = $where . " and campanna.id = :campannaid";
                $whereParameters += ['campannaid' => $campannaid];
            } else {
                $where = $where . " campanna.id = :campannaid";
                $whereParameters += ['campannaid' => $campannaid];
            }

            $flag = true;
        }

        if ($usuarioid) {
            if ($flag) {
                $where = $where . " and usuario.id = :usuarioid";
                $whereParameters += ['usuarioid' => $usuarioid];
            } else {
                $where = $where . " usuario.id = :usuarioid";
                $whereParameters += ['usuarioid' => $usuarioid];
            }

            $flag = true;
        }


        if ($ciudadid) {
            $ciudadid = explode(',', $ciudadid);

            if ($flag) {
                $where = $where . " and ciudad.id IN ( :ciudadid )";
                $whereParameters += ['ciudadid' => $ciudadid];
            } else {
                $where = $where . " ciudad.id IN ( :ciudadid )";
                $whereParameters = ['ciudadid' => $ciudadid];
            }
            $flag = true;
        }

        /*if($tipoenvioid)
        {
            $tipoenvioid= substr($tipoenvioid,0,-1);

            $tipoenvioid= explode(',',$tipoenvioid);

                if ($flag)
                {
                $where=$where." and tipoenvio.id IN ( :tipoenvioid )";
                $whereParameters+=['tipoenvioid'=>$tipoenvioid];
            }
            else
            {
                $where=$where." tipoenvio.id IN ( :tipoenvioid )";
                $whereParameters=['tipoenvioid'=>$tipoenvioid];
            }

            $flag= true;
        }*/

        if ($flag) {
            $where = $where . " and estado.id = :estadoid";
            $whereParameters += ['estadoid' => 3];

            $flag = true;
        } else {
            $where = $where . " estado.id = :estadoid";
            $whereParameters += ['estadoid' => 3];

            $flag = true;
        }

        if ($flag == false) {
            $where = "";
        }
        $fechaActual = date('Y-m-d');

        if($flag){
            $where .= " and oferta.fechainicio < '".$fechaActual."' and oferta.fechafin > '" .$fechaActual."'";
        }else{
            $where .= " oferta.fechainicio < '".$fechaActual."' and oferta.fechafin > '" .$fechaActual."'";
        }

        $sql = "select 
                  producto
              from 
              AdministracionBundle:Producto producto 
              LEFT JOIN  producto.estadoProductoid estado
              JOIN  producto.ofertaid oferta
              LEFT JOIN  producto.categoriaid categoria
              LEFT JOIN  producto.usuarioid usuario
              LEFT JOIN  producto.coleccionid coleccion
              LEFT JOIN  producto.condicion condicion
              LEFT JOIN  usuario.direccion direccion              
              LEFT JOIN  direccion.ciudad ciudad
              " . $where . "   
              GROUP BY producto.id
              ORDER BY producto.ranking DESC
             ";
        $dql = $em->createQuery($sql);
        $dql->setParameters($whereParameters);

        if ($paginado) {
            $dql->setMaxResults($offset);
            $dql->setFirstResult($start);
        }
        return $dql;
    }
    
    public function getProductosExpirados() {
        
        $em = $this->getEntityManager();
        $estadoFinalizado = $em->getRepository(EstadoProducto::class)->findOneBySlug(EstadoProducto::ESTADO_FINALIZADO_SLUG);
        
        $qb = $this->createQueryBuilder('e');
        
        $fechaActual = new \DateTime();
        
        $qb->andWhere($qb->expr()->neq('e.estadoProductoid', ':estado_producto'));
        $qb->setParameter('estado_producto', $estadoFinalizado);
        
        $qb->andWhere($qb->expr()->lte('e.fechaExpiracion', ':fecha_hasta'));
        $qb->setParameter('fecha_hasta', $fechaActual, \Doctrine\DBAL\Types\Type::DATETIME);
        
        $q = $qb->getQuery();
        
        return $q->execute();
    }

    public function findByStatus($status)
    {
        $em = $this->getEntityManager();

        $query = "select p from AdministracionBundle:Producto p left join p.estadoProductoid ep ";
        $query.= " where ep.slug = :status and p.borrado = 0";

        return $em->createQuery($query)->setParameter('status',$status)->getResult();
    }

}
