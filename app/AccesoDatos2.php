<?php
    include_once "producto.php";
    include_once "config.php";

    /*
    * Acceso a datos con BD Productos : 
    * Usando la librería mysqli
    * Uso el Patrón Singleton :Un único objeto para la clase
    * Constructor privado, y métodos estáticos 
    */
    class AccesoDatos {
        private static $modelo = null;
        private $dbh = null;
        
        public static function getModelo(){
            if (self::$modelo == null){
                self::$modelo = new AccesoDatos();
            }
            return self::$modelo;
        }
        



        

        /**** FUNCIONES ****/

        /* CONSTRUCTOR privado Patron singleton */
        private function __construct(){
            $this->dbh = new mysqli(DB_SERVER,DB_USER,DB_PASSWD,DATABASE);
            
            if ( $this->dbh->connect_error){
                die(" Error en la conexión ".$this->dbh->connect_errno);
            } 
        }

        // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
        public static function closeModelo(){
            if (self::$modelo != null){
                $obj = self::$modelo;
                
                $obj->dbh->close();   // Cierro la base de datos
                self::$modelo = null; // Borro el objeto.
            }
        }


        /* FUNCTION SELECT PRODUCTOS-> Devuelve el array de objetos */
        public function getProductos():array {
            $tpro = [];
            // Crea la sentencia preparada
            $stmt_productos = $this->dbh->prepare("select * from PRODUCTOS");
            // Si falla termian el programa
            if ( $stmt_productos == false) die (__FILE__.':'.__LINE__.$this->dbh->error);
            // Ejecuto la sentencia
            $stmt_productos->execute();
            // Obtengo los resultados
            $result = $stmt_productos->get_result();
            // Si hay resultado correctos
            if ( $result ){
                // Obtengo cada fila de la respuesta como un objeto de tipo Producto
                while ( $pro = $result->fetch_object('Producto')){
                $tpro[]= $pro;
                }
            }
            
            return $tpro;
        }
        
        /* FUNCTION SELECT PRODUCTO-> Devuelve un Producto o false */
        public function getProducto(String $npro) {
            $pro = false;
            
            $stmt_productos= $this->dbh->prepare("select * from PRODUCTOS where PRODUCTO_NO =?");
            if($stmt_productos == false){
                die ($this->dbh->error);
            } 

            // Enlazo $PRODUCTO_NO con el primer ? 
            $stmt_productos->bind_param("s", $npro);
            $stmt_productos->execute();

            $result = $stmt_productos->get_result();

            if( $result ){
                $pro = $result->fetch_object('Producto');
            }
            
            return $pro;

        }
        
        /* FUNCTION UPDATE PRODUCTO */
        public function modProducto($pro):bool{
            $stmt_modpro = $this->dbh->prepare("update PRODUCTOS set DESCRIPCION=?, PRECIO_ACTUAL=?, STOCK_DISPONIBLE=? where PRODUCTO_NO=?");
            if( $stmt_modpro == false){
                die ($this->dbh->error);
            }

            $stmt_modpro->bind_param("ssss", $pro->DESCRIPCION, $pro->PRECIO_ACTUAL, $pro->STOCK_DISPONIBLE, $pro->PRODUCTO_NO);
            $stmt_modpro->execute();

            $resu = ($this->dbh->affected_rows  == 1);
            return $resu;
        }

        /* FUNCTION INSERTAR PRODUCTO */
        public function addProducto($pro):bool{
            $stmt_creapro  = $this->dbh->prepare("insert into PRODUCTOS (PRODUCTO_NO,DESCRIPCION,PRECIO_ACTUAL,STOCK_DISPONIBLE) Values(?,?,?,?)");
            if ( $stmt_creapro == false){
                die ($this->dbh->error);
            }

            $stmt_creapro->bind_param("ssss",$pro->PRODUCTO_NO, $pro->DESCRIPCION, $pro->PRECIO_ACTUAL, $pro->STOCK_DISPONIBLE);
            $stmt_creapro->execute();

            $resu = ($this->dbh->affected_rows == 1);
            return $resu;
        }

        /* FUNCTION DELETE PRODUCTO */
        public function borrarProducto(String $npro):bool {
            $stmt_borpro = $this->dbh->prepare("delete from PRODUCTOS where PRODUCTO_NO =?");
            if ( $stmt_borpro == false){
                die ($this->dbh->error);
            } 
        
            $stmt_borpro->bind_param("s", $npro);
            $stmt_borpro->execute();
            $resu = ($this->dbh->affected_rows  == 1);
            return $resu;
        }   
        
        /* FUNCTION para evitar que se pueda clonar el objeto.(SINGLETON) */
        public function __clone(){ 
            trigger_error('La clonación no permitida', E_USER_ERROR); 
        }
    }
?>