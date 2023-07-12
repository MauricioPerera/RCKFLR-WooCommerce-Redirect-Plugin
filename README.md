# RCKFLR WooCommerce Redirect Plugin

Este plugin de WordPress agrega campos personalizados a los ajustes de datos del producto de WooCommerce. Estos campos incluyen una URL de redirección, una casilla de verificación para decidir si abrir la URL en una nueva pestaña, y casillas de verificación para incluir el ID de la orden y un nonce en la URL.

![Diagrama del plugin](https://rckflr.party/wp-content/uploads/2023/07/Diseno-sin-titulo-scaled.jpg)

## Funcionalidades

1. Agrega un campo de URL de redirección a los ajustes de datos del producto de WooCommerce.
2. Agrega una casilla de verificación para decidir si abrir la URL de redirección en una nueva pestaña.
3. Agrega casillas de verificación para incluir el ID de la orden y un nonce en la URL de redirección.
4. Cuando se compra un producto, el plugin redirige al cliente a la URL de redirección después de la compra. Si se ha seleccionado la opción de abrir en una nueva pestaña, la URL se abre en una nueva pestaña en lugar de redirigir.
5. Si se han seleccionado las opciones de incluir el ID de la orden y un nonce en la URL, estos se añaden a la URL como parámetros GET.
6. El nonce también se guarda en los metadatos de la orden, lo que permite recuperarlo más tarde para su validación.

## Validación del nonce

Para validar el nonce, puedes usar la función `wp_verify_nonce` en tu código. Esta función toma dos parámetros: el nonce que quieres verificar, y la acción que pasaste cuando creaste el nonce. Aquí tienes un ejemplo de cómo podrías hacerlo:

```php
$nonce = $_GET['nonce'];
$order_id = $_GET['order_id'];

// Get the saved nonce from the order meta
$saved_nonce = get_post_meta( $order_id, '_rckflr_woo_redirect_nonce', true );

if ( wp_verify_nonce( $nonce, 'rckflr_woo_redirect_nonce' ) && $nonce === $saved_nonce ) {
    // The nonce is valid
} else {
    // The nonce is not valid
}
```
En este código, se obtiene el nonce y el ID de la orden de la URL como parámetros GET. Luego, se obtiene el nonce guardado de los metadatos de la orden. Finalmente, se verifica el nonce utilizando la función wp_verify_nonce y se comprueba que el nonce de la URL coincide con el nonce guardado.

Por favor, ten en cuenta que este es solo un ejemplo y que deberías implementar tus propias medidas de seguridad adicionales para proteger tu sitio y tus usuarios.

## Información del autor

* Autor: Mauricio Perera
* Enlace del autor: [LinkedIn](https://www.linkedin.com/in/mauricioperera/)
* Enlace de donación: [Buy me a coffee](https://www.buymeacoffee.com/rckflr)
