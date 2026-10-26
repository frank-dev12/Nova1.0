# NØVA 1.0

Catálogo web de ropa en PHP vanilla, con la tienda al frente y un panel para ir armando el catálogo. Los estilos usan Tailwind CSS y los íconos son SVG, no emojis.

## Cómo verlo

```bash
php -S localhost:8000 router.php
```

Abre `http://localhost:8000`.

## Panel

- URL: `http://localhost:8000/admin/login.php`
- Usuario: `admin`
- Contraseña: `nova123`

Cambia la contraseña y el WhatsApp en `includes/config.php` antes de publicar. El número actual es un placeholder.

## Qué incluye esta versión

- Inicio con carrusel, categorías, favoritos y llamado a la colección
- Catálogo con búsqueda, filtro y orden
- Ficha de producto, carrito en sesión y pedido por WhatsApp
- Nosotros y contacto. Los mensajes se guardan para el panel
- Panel: resumen, alta y edición de prendas, lectura de mensajes

El pago en línea queda para una siguiente versión. El cierre del pedido es por WhatsApp.

## Estilos

```bash
npm install
npm run css
```

Eso regenera `assets/css/app.css`. La tienda no necesita Node para funcionar.

## Fotos

Las imágenes actuales son referencias de Unsplash y Pexels para armar la primera versión. Sustitúyelas desde el panel cuando tengas la campaña.
