/**
 * Funciones auxiliares de javascripts 
 */
function confirmarBorrar(PRODUCTO_NO){
    if (confirm("¿Quieres eliminar el Producto:  "+ PRODUCTO_NO+ "?")){
        document.location.href="?orden=Borrar&id="+ PRODUCTO_NO;
    }
}