# NØVA 1.0

NØVA es el primer modelo de catálogo web para una tienda de ropa. Esta versión está hecha en PHP vanilla: no usa framework, ni base de datos, ni un bundler para ejecutar la tienda. El frente es la tienda pública y el panel es el back. La idea es ir sumando piezas sobre esta base.

Los estilos salen de Tailwind CSS. El JavaScript cubre el carrusel, el menú, la búsqueda en vivo, el carrito y los favoritos. Los íconos son SVG (Lucide y el de WhatsApp), no emojis.

## Cómo verlo

Hace falta PHP 8.1 o superior.

```bash
php -S localhost:8000 router.php
```

Abre `http://localhost:8000`.

`router.php` solo evita que el servidor de desarrollo sirva `includes/` y `data/` como archivos sueltos. En Apache, `.htaccess` hace lo mismo.

## Panel

- URL: `http://localhost:8000/admin/login.php`
- Usuario: `admin`
- Contraseña: `nova123`

Cambia la contraseña y el WhatsApp en `includes/config.php` antes de publicar. El número actual (`+51 900 000 000`) es un placeholder.

## Qué incluye esta versión

La tienda tiene inicio, catálogo, ficha de producto, carrito, nosotros y contacto. El inicio replica la estructura del diseño: carrusel, barra de envíos y WhatsApp, categorías, favoritos de la temporada y el banner de colección.

El catálogo busca, filtra por categoría y ordena. El carrito vive en la sesión de PHP. El pedido no se cobra en la web: se arma un mensaje y se abre WhatsApp. El formulario de contacto guarda el mensaje para leerlo en el panel.

El panel tiene resumen, alta, edición y baja de prendas, subida de imagen, y la bandeja de mensajes.

El pago en línea queda para una siguiente versión.

## Cómo está hecho

Cada página pública es un archivo PHP en la raíz. Arranca cargando `includes/bootstrap.php`, que abre la sesión, lee la configuración y deja listas las funciones de productos, carrito, mensajes e íconos. Después incluye `includes/header.php` y `includes/footer.php`, que pintan la misma cabecera y el pie.

| Ruta | Qué hace |
| --- | --- |
| `index.php` | Inicio |
| `catalogo.php` | Listado, búsqueda, filtro y orden |
| `producto.php` | Ficha |
| `carrito.php` | Carrito. También recibe el alta si el navegador no ejecuta JavaScript |
| `nosotros.php` | La marca |
| `contacto.php` | Formulario |
| `api/cart.php` | Alta y cambios del carrito, en JSON |
| `api/search.php` | Sugerencias de búsqueda |
| `admin/` | Login, resumen, productos y mensajes |

`includes/products.php` lee y escribe `data/products.json`. Las categorías están en `data/categories.json`. Los mensajes del contacto se guardan en `data/messages.php` para que, si alguien pide ese archivo por HTTP, PHP no lo imprima.

El carrito no usa base de datos. Cada línea (producto, color y talla) se guarda en `$_SESSION`. El botón de agregar envía el formulario a `api/cart.php` con JavaScript. Si JavaScript no corre, el mismo formulario llega a `carrito.php` y la página se recarga.

El logo NØVA, en la cabecera o en el pie, abre el inicio con un clic. Cuatro clics seguidos abren el panel. Al entrar a la tienda, la primera visita de la sesión muestra una pantalla de carga con la marca.

El panel exige sesión de administrador. La contraseña no está en texto plano: en `includes/config.php` hay un hash y el login lo comprueba con `password_verify`. Los formularios del panel, del carrito y del contacto llevan un token de sesión para no aceptar un envío de otra página.

`assets/js/app.js` se carga al final del layout. Ahí están el menú móvil, el carrusel, el corazón de favoritos (en `localStorage` del navegador), el contador del carrito y el desplegable de búsqueda. Los favoritos no se sincronizan con el servidor.

## Estilos

Tailwind se compila una vez y el resultado queda en `assets/css/app.css`. La tienda no necesita Node para funcionar. Para regenerar el CSS después de cambiar clases:

```bash
npm install
npm run css
```

La fuente es Inter para la interfaz y Caveat para la frase manuscrita del carrusel. Los archivos están en `assets/fonts/`.

## Fotos

Las imágenes de `assets/img/` son referencias de Unsplash y Pexels para armar esta primera versión. Desde el panel se sube otra foto (JPG, PNG o WebP, hasta 2 MB) y queda en `assets/img/uploads/`.

## Prueba rápida

```bash
php tests/smoke.php
```

Comprueba que el catálogo carga, que la búsqueda encuentra una prenda y que el carrito suma, actualiza y quita una línea.
