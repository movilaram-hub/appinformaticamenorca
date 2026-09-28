APP INFORMÁTICA MENORCA — VERSIÓN PHP
===========================================

REQUISITOS DEL SERVIDOR
- PHP 8.0 o superior recomendado.
- Hosting Apache/PHP normal (el típico hosting compartido sirve).
- No necesita base de datos.

CÓMO PUBLICARLA
1. Entra por FTP o por el administrador de archivos de tu hosting.
2. Sube TODO el contenido de esta carpeta a la carpeta pública del dominio
   (normalmente public_html, httpdocs, www o similar).
3. Asegúrate de que index.php queda directamente en esa carpeta pública.
4. Abre el dominio en el navegador.

IMPORTANTE
- Si la web se instala en la raíz del dominio, no cambies nada.
- Si se instala dentro de una subcarpeta (ejemplo: dominio.com/app), abre
  config.php y cambia:
      define('BASE_URL', '');
  por:
      define('BASE_URL', '/app');

IDIOMAS
- /es/ Español
- /ca/ Català
- /en/ English
- /fr/ Français
- /de/ Deutsch

FUNCIONES CONSERVADAS
- Diseño responsive para ordenador, tablet y móvil.
- Menú y navegación por secciones.
- Cinco idiomas y selector de idioma.
- Detección inicial del idioma del navegador.
- Estado abierto/cerrado de la tienda usando horario Europe/Madrid.
- WhatsApp, Google Maps, Instagram y enlaces externos.
- Banner VeriFactu.
- Banner y enlace del configurador PC Gaming.
- Transiciones de navegación y microbarra de progreso.
- Páginas de Servicio Técnico, Patinetes, Fibra/Móvil, Empresas, Contacto y Tienda.

PRUEBA LOCAL OPCIONAL
Si tienes PHP instalado, desde esta carpeta puedes ejecutar:
    php -S localhost:8080
Y abrir:
    http://localhost:8080

