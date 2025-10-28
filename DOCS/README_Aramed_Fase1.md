# 🌐 Proyecto: Sitio Web Integral – Aramed y Laboratorios
**Desarrollado por:** IDEAMIA – Tech  
**Cliente:** Aramed y Laboratorios S.A. de C.V.  
**Fase actual:** Fase 1 – Frontend Cliente + Diseño  
**Fecha de inicio:** 08/10/2025  
**Responsable técnico:** Ing. Jorge Alberto Plascencia Correa  

---

## 📘 Objetivo del Proyecto
Desarrollar un **sitio web corporativo moderno, funcional y optimizado** para Aramed y Laboratorios, con diseño responsivo, gestión modular y enfoque escalable.  
La **Fase 1** comprende el desarrollo del **Frontend completo del sitio público**, con base en el diseño UX/UI aprobado y la estructura funcional definida.

---

## ⚙️ Alcance de la Fase 1

### 1. Análisis y Diseño UI/UX
- Reunión inicial para definir **look & feel**, branding y paleta de colores.  
- Creación de **wireframes y navegación completa** (Home, Catálogo, Blog, Contacto, Proyectos).  
- Diseño responsivo (desktop, tablet, móvil).  
- **Prototipo interactivo** (Figma o Adobe XD).  
- Revisión y ajustes con el cliente.  

**Duración estimada:** 38 horas  

---

### 2. Estructura Base del Frontend
- Configuración del entorno **LAMP** y estructura MVC.  
- Layout general (header, footer, menús dinámicos).  
- Integración de **Bootstrap o Tailwind** según el diseño final.  
- Rutas amigables (`mod_rewrite`) y SEO técnico básico.  

**Duración estimada:** 32 horas  

---

### 3. Desarrollo de Páginas Públicas

#### 🏠 Home
- Banner rotatorio (slides dinámicos con títulos, subtítulos y CTA).  
- Bloques de servicios y productos destacados.  
- Sección **Marcas / Alianzas** (logos dinámicos en carrusel).  
- Sección “Misión, Visión y Valores”.  
- Testimonios y aliados.  

#### 🧬 Catálogo de Productos
- Listado con **filtros dinámicos** (marca, tipo, precio, disponibilidad).  
- Buscador AJAX.  
- Fichas de producto con galería, video, PDF y botón **“Agregar a cotización”**.  

#### 💬 Solicitud de Cotización
- Formulario dinámico con resumen de productos seleccionados.  
- Validaciones y consentimiento.  
- Generación automática de **folio** y notificación por correo.  

#### 🧪 Proyectos
- Listado filtrable por **año, categoría y marca**.  
- Vista detalle con galería de imágenes y videos.  

#### 📰 Blog
- Listado paginado, buscador por tema o categoría.  
- Página de detalle con estructura **SEO y Open Graph**.  
- Botones para compartir en redes sociales.  

#### 📞 Contacto
- Formulario con motivo de contacto.  
- **Mapa interactivo** embebido.  
- Envío automático al correo de atención al cliente.  

#### ✉️ Newsletter
- Formulario con validaciones y doble opt-in (opcional).  
- Notificación a **marketing@aramedylaboratorio.com**.  

#### ⚖️ Páginas Legales
- Aviso de Privacidad, Términos de Uso, Política de Cookies.  

---

### 4. Componentes Frontend Avanzados
- Filtros dinámicos con **AJAX**.  
- Lazy Loading de imágenes.  
- Conversión automática a formatos **WebP / AVIF**.  
- Sliders con **Swiper** o **Slick**.  

---

### 5. Testing y QA
- Pruebas de usabilidad y navegación en desktop / móvil.  
- Validación de formularios (contacto, cotización, newsletter).  
- Corrección de errores visuales y responsive.  
- Revisión con cliente antes de despliegue final.  

---

## 📅 Cronograma – Fase 1 (162 horas)

| Semana | Actividad Principal | Responsable | Horas |
|:--|:--|:--|:--|
| **1** | Análisis y Diseño UI/UX | IDEAMIA / Aramed | 38 h |
| **2** | Estructura Base del Frontend | IDEAMIA Tech | 32 h |
| **3** | Páginas Públicas – Parte 1 (Home, Proyectos) | IDEAMIA Tech | 36 h |
| **4** | Páginas Públicas – Parte 2 + QA Final | IDEAMIA Tech / Aramed | 56 h |

---

## 🎨 Referencias Visuales

### Sitio Base
- [iHealth Labs](https://ihealthlabs.com/)
- [Gaumard Scientific](https://www.gaumard.com/)
- [Simulab](https://simulab.com/)
- [SimX](https://simxar.com/)
- [Medtronic México](https://www.medtronic.com/mx-es/index.html)
- [Anatomage](https://anatomage.com/)
- [Eko Health](https://www.ekohealth.com/)
- [Pro Theme Health](https://pro-theme.com/html/health/about-1.html)

---

## 🧩 Estructura del Sitio

### **Header**
- Topbar con mensajes automáticos (avisos / eventos).  
- Navbar con links: `Inicio`, `Catálogos`, `Proyectos`, `Aliados`, `Blogs`, `Contáctanos`.  

### **Footer**
- Logo Aramed y Laboratorios.  
- Menú rápido + horarios.  
- Información de contacto.  
- Redes sociales: LinkedIn, Facebook, Instagram, X.  

---

## 🔒 Tecnologías y Estándares
- **Lenguajes:** PHP, JavaScript, CSS, HTML5, AJAX  
- **Frameworks:** Bootstrap / Tailwind  
- **Base de datos:** MySQL  
- **Arquitectura:** MVC  
- **Optimización:** SEO, Open Graph, WebP, Lazy Load  
- **Correo saliente:** PHPMailer o API SendGrid  

---

## ✅ Entregables Fase 1
1. Prototipo visual aprobado (Figma o Adobe XD).  
2. Frontend completo funcional (páginas públicas).  
3. Formularios de contacto y cotización conectados a correo.  
4. Archivos optimizados y listos para integración backend (Fase 2).  
5. Reporte de pruebas y correcciones de QA.  

---

## 🧾 Declaraciones
IDEAMIA – Tech se compromete a desarrollar las funcionalidades descritas en la Fase 1 conforme al presente documento.  
Aramed y Laboratorios reconoce que las funciones adicionales se desarrollarán en fases posteriores.  

**Firmas de aceptación**  
**IDEAMIA – Tech:** Ing. Jorge Alberto Plascencia Correa – Dirección General  
**Aramed y Laboratorios:** ___________________________

---

## 📂 Estructura Inicial de Carpetas
/aramedylaboratorio
│
├── /public_html
│   ├── index.php
│   ├── /assets
│   │   ├── /css
│   │   ├── /js
│   │   ├── /img
│   └── /views
│       ├── home.php
│       ├── catalogo.php
│       ├── proyectos.php
│       ├── blog.php
│       ├── contacto.php
│       └── legales.php
│
├── /includes
│   ├── header.php
│   ├── footer.php
│   └── config.php
│
└── /admin (para fases futuras)

---

## 📧 Contactos
- **Cliente:** marketing@aramedylaboratorio.com  
- **Soporte Técnico:** soporte@ideamia.com.mx  
- **Dirección IDEAMIA:** Club de Golf Atlas 535 Int 20, Tlaquepaque, Jalisco, 45623  

---

© 2025 Aramed y Laboratorios | Desarrollado por IDEAMIA – Tech




Aquí tienes un análisis completo del documento “Acuerdo de Aceptación – Fase 1” junto con una lista detallada de tareas, separando lo que debe hacerse, lo que está en curso y lo que puede considerarse completo si ya fue validado por ambas partes.

⸻

🧩 RESUMEN GENERAL DEL PROYECTO

Proyecto: Sistema Web Integral para Aramed y Laboratorios
Desarrollador: IDEAMIA – TECH
Fase actual: Fase 1 – Frontend Cliente + Diseño (162 horas totales)
Tecnologías: PHP, JavaScript, CSS, AJAX, MySQL
Objetivo: Desarrollar el frontend completo de la página pública, con diseño UI/UX, estructura base y funcionalidades interactivas.

⸻

📋 LISTA DE TAREAS POR BLOQUE

1. Análisis y Diseño UI/UX (Semana 1 – 38h)

Responsable: IDEAMIA / Aramed
Estado recomendado: ✅ Completar y validar antes de seguir al desarrollo

Tareas:
	•	Reunión inicial para definir branding, paleta de colores, tipografías y estilos.
	•	Diseño de wireframes para páginas: Home, Catálogo, Blog, Contacto, Proyectos.
	•	Definición de navegación y jerarquía visual.
	•	Diseño responsivo (desktop, tablet, móvil).
	•	Creación de prototipo visual interactivo (Figma/Adobe XD).
	•	Envío al cliente para revisión y ajustes.
	•	Aprobación formal del diseño (firma o correo de conformidad).

Se considera completo si: El cliente aprobó el prototipo final y existe evidencia de aceptación (correo o firma).

⸻

2. Estructura Base del Frontend (Semana 2 – 32h)

Responsable: IDEAMIA TECH
Estado recomendado: ⚙️ En curso o recién completado

Tareas:
	•	Configuración del proyecto LAMP en el servidor.
	•	Estructura de carpetas bajo arquitectura MVC.
	•	Creación del layout principal: header, footer y menú dinámico.
	•	Integración del framework CSS (Bootstrap o Tailwind).
	•	Configuración de URLs amigables (mod_rewrite).
	•	Verificación del rendimiento inicial y tiempos de carga.

Se considera completo si: La estructura base está montada y el layout general se ve consistente en todos los dispositivos.

⸻

3. Desarrollo de Páginas Públicas – Parte 1 (Semana 3 – 36h)

Responsable: IDEAMIA TECH
Estado recomendado: ⚙️ En desarrollo o listo para QA

Tareas:
	•	Página Home con:
	•	Banner rotatorio
	•	Productos destacados
	•	Marcas dinámicas
	•	Bloques de servicios
	•	Misión/Visión
	•	Testimonios o alianzas
	•	Página Proyectos con:
	•	Listado filtrable (año, categoría, marca)
	•	Vista detalle con galería, videos, PDFs

Se considera completo si: Las secciones cargan dinámicamente y el contenido es gestionable desde el backend o mock data temporal.

⸻

4. Desarrollo de Páginas Públicas – Parte 2 & QA (Semana 4 – 56h)

Responsable: IDEAMIA TECH / Aramed
Estado recomendado: 🚧 Revisión QA / pruebas

Tareas:
	•	Catálogo de productos con filtros, buscador, multimedia y botón “Agregar a cotización”.
	•	Solicitud de cotización con formulario validado, generación de folio y correo automático.
	•	Formulario de contacto dinámico con motivo de contacto y mapa interactivo.
	•	Blog con paginación, buscador, SEO y Open Graph.
	•	Newsletter con validaciones y doble opt-in (opcional).
	•	Páginas legales/SEO: Aviso de Privacidad, Términos, Cookies.
	•	Optimización visual: lazy loading, sliders (Swiper/Slick), conversión WebP/AVIF.
	•	Pruebas de QA: usabilidad, validaciones, responsive.
	•	Corrección de errores visuales.
	•	Revisión final con el cliente.

Se considera completo si: Todos los formularios funcionan, las páginas cargan correctamente, y el cliente aprueba la visualización general.

⸻

5. Testing y Revisión Final

Responsable: IDEAMIA / Aramed
Tareas finales:
	•	Validación de formularios y envío de correos automáticos.
	•	Pruebas de navegación y usabilidad en distintos dispositivos.
	•	Verificación de rendimiento (lazy load, compresión).
	•	Revisión general del responsive.
	•	Entrega formal de la Fase 1 (con firma o correo de aceptación).

⸻

✅ RESUMEN DE ESTADO (Checklist Global)

Bloque	Responsable	Estimado	Estado sugerido
Análisis y Diseño UI/UX	IDEAMIA / Aramed	38h	✅ Completado (si aprobado por cliente)
Estructura Base Frontend	IDEAMIA TECH	32h	⚙️ En curso
Desarrollo Parte 1	IDEAMIA TECH	36h	⚙️ En curso / QA
Desarrollo Parte 2 & QA	IDEAMIA TECH / Aramed	56h	🚧 En revisión
Testing Final	IDEAMIA / Aramed	Incluido	⏳ Pendiente


⸻

📦 Próximos pasos recomendados
	1.	Confirmar con el cliente (Aramed) si el prototipo visual fue aprobado formalmente.
	2.	Actualizar un documento de avance con evidencias (capturas o links de las secciones completadas).
	3.	Ejecutar QA completo en móviles y tablets.
	4.	Preparar minuta de entrega Fase 1 para firma digital o física.
	5.	Iniciar planeación de Fase 2 (Backend Admin + API de gestión) una vez validado el cierre de esta fase.

TÉRMINOS Y CONDICIONES DE USO
Última actualización: [FECHA]

Estos Términos y Condiciones regulan el acceso y uso del sitio web [URL del sitio] (el “Sitio”), propiedad de [NOMBRE DE LA EMPRESA], con domicilio en [DIRECCIÓN COMPLETA] y correo electrónico [CORREO ELECTRÓNICO].

Al acceder o usar el Sitio, usted acepta quedar vinculado por estos Términos. Si no está de acuerdo, debe abstenerse de utilizarlo.

1. Servicios y productos

Descritos en el Sitio, nuestros servicios y/o productos están sujetos a disponibilidad, condiciones específicas y precios vigentes en el momento de la compra o contratación.

2. Registro y cuenta de usuario

Para acceder a ciertos servicios, podrá registrarse con un usuario y contraseña. Usted es responsable de mantener la confidencialidad de su cuenta, así como de toda actividad que se realice bajo ella. Debe notificarnos inmediatamente si detecta acceso no autorizado.

3. Pagos y facturación

El pago de los servicios/productos se efectuará mediante los métodos disponibles en el Sitio. Usted garantiza que dispone de los derechos necesarios de los medios de pago utilizados. Nos reservamos el derecho de suspender, cancelar o rechazar pedidos ante posibles irregularidades.

4. Envíos, devoluciones y cancelaciones

[Si aplica, detallar política de envío, plazos, costos, cambios y devoluciones]
De otra forma, indíquese que cada caso estará sujeto a las condiciones específicas que se publiquen o acuerden.

5. Propiedad intelectual

Todo el contenido del Sitio (textos, imágenes, logotipos, marcas, programas, etc.) es propiedad de la Empresa o de terceros que han autorizado su uso. Queda prohibida su reproducción, distribución, transformación o comercialización sin autorización expresa.

6. Uso permitido

El Usuario se compromete a utilizar el Sitio de conformidad con la ley, la moral, el orden público y estos Términos. Queda prohibido:
	•	Realizar actividades fraudulentas, ilegales o maliciosas
	•	Difamar, acosar o fomentar violencia
	•	Introducir virus, malware, o alterar el funcionamiento del Sitio
	•	Suplantar identidad o usar datos de terceros sin consentimiento

7. Limitación de responsabilidad

En la medida permitida por la ley, la Empresa no será responsable por daños indirectos, incidentales, lucro cesante, pérdida de datos o interrupción del servicio, derivados del uso o imposibilidad de uso del Sitio, salvo en casos de dolo o culpa grave.

8. Modificaciones del Sitio y de los Términos

La Empresa se reserva el derecho de modificar, suspender o interrumpir el Sitio, total o parcialmente, así como de actualizar estos Términos en cualquier momento. Las modificaciones se publicarán en esta página y entrarán en vigor desde su publicación.

9. Legislación aplicable y jurisdicción

Estos Términos se regirán e interpretarán conforme a las leyes de los Estados Unidos Mexicanos. Para la resolución de controversias, las partes se someten a la jurisdicción de los tribunales competentes de [CIUDAD, ESTADO].

10. Contacto

Para dudas o aclaraciones sobre estos Términos y sobre el Sitio, puede contactarnos en:
Correo electrónico: [CORREO ELECTRÓNICO]
Domicilio: [DIRECCIÓN COMPLETA]




POLÍTICA DE PRIVACIDAD
Última actualización: [FECHA]

En [NOMBRE DE LA EMPRESA] (en adelante “nosotros”, “nuestro/a”, “la Empresa”), con domicilio en [DIRECCIÓN COMPLETA], somos responsables de recoger, utilizar, almacenar y proteger los datos personales que usted nos proporciona a través de nuestro sitio web [URL del sitio] (el “Sitio”). Esta Política de Privacidad explica qué datos recogemos, con qué finalidad, cómo los protegemos y los derechos que usted tiene.

1. Datos que recogemos

Podemos recoger los siguientes datos personales:
	•	Nombre completo
	•	Correo electrónico
	•	Teléfono
	•	Dirección
	•	Datos de facturación (si aplica)
	•	Información sobre su dispositivo, navegación y cookies (por ejemplo: Dirección IP, tipo de navegador, páginas visitadas, tiempo en el sitio)
	•	Cualquier otro dato que usted nos proporcione voluntariamente (por ejemplo al registrarse, suscribirse, hacer un pedido, enviar un formulario de contacto)

2. Finalidad del tratamiento

Usaremos sus datos personales para las siguientes finalidades:
	•	Proveerle los productos o servicios solicitados
	•	Gestionar y responder a sus consultas o solicitudes
	•	Enviarle comunicaciones comerciales, promociones o novedades (cuando usted haya dado su consentimiento)
	•	Mejorar nuestro Sitio web, optimizar su experiencia de usuario y administrar cookies y rastreadores
	•	Cumplir con obligaciones legales, contables o fiscales

3. Bases legales

De conformidad con la Ley Federal de Protección de Datos Personales en Posesión de los Particulares de México (LFPDPPP), el tratamiento de datos personales se basa en su consentimiento o en otros supuestos previstos por la ley.  ￼

4. Compartir, transferir o revelar datos

No venderemos, alquilaremos ni compartiremos sus datos personales con terceros, salvo en los siguientes casos:
	•	Cuando sea necesario para entregar un producto o servicio, con prestadores de servicios a nuestro cargo (por ejemplo, envío, pasarela de pagos)
	•	Cuando expresamente usted lo autorice
	•	Cuando exista obligación legal o requerimiento de autoridad competente

5. Uso de cookies y rastreadores

Utilizamos cookies y otros mecanismos de rastreo para mejorar la experiencia del usuario, analizar el comportamiento en el Sitio, personalizar contenido y publicidad. Usted puede desactivar las cookies desde su navegador; sin embargo, esto puede afectar el correcto funcionamiento del Sitio.

6. Medidas de seguridad

Hemos implementado medidas técnicas, físicas y organizativas para proteger los datos personales contra acceso no autorizado, divulgación, alteración o destrucción. No obstante, ningún método de transmisión por Internet o de almacenamiento electrónico es 100 % seguro.

7. Conservación de los datos

Conservaremos sus datos personales durante el tiempo necesario para cumplir con las finalidades antes mencionadas, mientras su cuenta esté activa, o hasta que solicite su eliminación, salvo que prevalezca una obligación legal para su conservación.

8. Derechos ARCO y revocación

Usted tiene los derechos de Acceso, Rectificación, Cancelación y Oposición (ARCO) al tratamiento de sus datos, así como a revocar el consentimiento otorgado. Para ejercitarlos, envíe su solicitud al correo electrónico [CORREO ELECTRÓNICO] o al domicilio antes señalado. Deberá incluir: nombre completo, domicilio u otro medio para comunicar la respuesta, documento que acredite su identidad, descripción de los datos respecto de los que busca ejercer el derecho y la modalidad de respuesta deseada.

9. Modificaciones del aviso de privacidad

Nos reservamos el derecho de modificar esta Política de Privacidad en cualquier momento. La nueva versión estará disponible en esta página con la fecha de actualización correspondiente.

10. Contacto

Si tiene preguntas o comentarios sobre esta Política de Privacidad o el tratamiento de sus datos, puede contactarnos en:
Correo electrónico: [CORREO ELECTRÓNICO]
Domicilio: [DIRECCIÓN COMPLETA]




Política de Cookies

Última actualización: [FECHA]

En Aramed y Laboratorio (en adelante “nosotros”, “nuestro” o “la Empresa”), utilizamos cookies y tecnologías similares para mejorar su experiencia de navegación en el sitio web https://aramedylaboratorio.com (en adelante, el “Sitio”).
Esta Política explica qué son las cookies, qué tipos utilizamos, con qué finalidad, y cómo puede gestionarlas o desactivarlas.

⸻

1. ¿Qué son las cookies?

Las cookies son pequeños archivos de texto que se almacenan en su dispositivo (ordenador, tablet, teléfono móvil, etc.) cuando visita un sitio web.
Permiten que el sitio recuerde sus acciones y preferencias (como idioma, inicio de sesión o contenido del carrito) durante un periodo de tiempo, para ofrecerle una experiencia más personalizada y eficiente.

⸻

2. Tipos de cookies que utilizamos

a) Cookies necesarias (esenciales)

Estas cookies son indispensables para el funcionamiento del Sitio y le permiten navegar y usar sus funciones básicas (por ejemplo, acceder a áreas seguras o formularios).
No pueden desactivarse en nuestros sistemas.

Ejemplos:
	•	Cookies de sesión
	•	Cookies de seguridad
	•	Cookies de autenticación

⸻

b) Cookies de rendimiento y analítica

Nos ayudan a entender cómo los usuarios interactúan con el Sitio, recopilan información anónima (como páginas visitadas, tiempo de navegación o errores).
Esto nos permite mejorar la estructura, contenido y rendimiento de nuestro sitio.

Ejemplo:
	•	Google Analytics
	•	Proveedor: Google LLC
	•	Información recopilada: IP anonimizada, tiempo en la página, dispositivo, navegador
	•	Política de privacidad de Google: https://policies.google.com/privacy

⸻

c) Cookies de personalización (preferencias)

Permiten recordar sus elecciones, como el idioma o la región, para ofrecerle una experiencia adaptada a sus preferencias.

⸻

d) Cookies de publicidad o marketing

Se utilizan para mostrarle anuncios relevantes y medir la efectividad de las campañas publicitarias.
También pueden ser utilizadas por terceros autorizados (por ejemplo, Google Ads, Meta Pixel) para mostrar publicidad personalizada en función de sus intereses.

⸻

3. Cookies de terceros

En algunos casos, colaboramos con empresas externas que también pueden colocar cookies en su dispositivo para recopilar información sobre su navegación.
Estas cookies están sujetas a las políticas de privacidad de dichos terceros.

Ejemplos posibles:
	•	Google Ads / DoubleClick
	•	Facebook Pixel (Meta Platforms, Inc.)
	•	YouTube (Google LLC)

⸻

4. Consentimiento

Cuando accede al Sitio por primera vez, le mostramos un aviso o banner de cookies donde puede aceptar todas las cookies o configurar sus preferencias.
Al hacer clic en “Aceptar todas las cookies”, usted consiente el uso de las mismas conforme a esta Política.
Puede retirar su consentimiento o cambiar su configuración en cualquier momento mediante el enlace “Configuración de cookies” disponible en el pie de página del Sitio.

⸻

5. Cómo desactivar o eliminar cookies

Puede configurar su navegador para bloquear o eliminar cookies en cualquier momento.
Tenga en cuenta que si las desactiva, algunas secciones del Sitio podrían no funcionar correctamente.

Enlaces de ayuda según su navegador:
	•	Google Chrome
	•	Mozilla Firefox
	•	Safari
	•	Microsoft Edge

⸻

6. Actualizaciones de esta Política

Podemos actualizar esta Política de Cookies en cualquier momento para reflejar cambios en nuestras prácticas o en la legislación vigente.
Le recomendamos revisar periódicamente esta página para mantenerse informado.

⸻

7. Contacto

Si tiene dudas o comentarios sobre esta Política de Cookies, puede contactarnos en:
📧 [CORREO ELECTRÓNICO]
📍 [DIRECCIÓN COMPLETA]



1.	GAUMARD: 	Gaumard Scientific desarrolla simuladores médicos de alta fidelidad que transforman la enseñanza clínica. Su innovación tecnológica complementa nuestra misión de ofrecer experiencias de aprendizaje realistas y seguras en salud.
2.	MEDICAL X	Medical-X desarrolla simuladores médicos de alta fidelidad para entrenamiento clínico. Su tecnología avanzada potencia a Aramed en formación realista y segura.
3.	ANATOMAGE	Anatomage crea plataformas 3D interactivas que revolucionan la enseñanza anatómica mediante visualizaciones precisas del cuerpo humano. Su innovación eleva nuestros estándares en simulación médica educativa.
4.	ECHO HEALTHCARE	Echo Healthcare desarrolla soluciones inmersivas y realistas para simulación médica (maniquíes, máscaras, entornos interactivos). Su innovación eleva nuestra oferta formativa con un enfoque de alto impacto.
5.	3B SCIENTIFIC 	3B Scientific fabrica modelos anatómicos y simuladores médicos para educación en salud. Su calidad global refuerza nuestra oferta educativa y credibilidad como aliado estratégico.
6.	VSI 	VSI diseña y fabrica simuladores de alta precisión para educación veterinaria, colaboran con profesionales veterinarios para crear estas soluciones, Su misión es permitir que los estudiantes veterinarios desarrollen habilidades diagnósticas
7.	(SafeGuard) SIMBODIES	Safeguard Medical provee tecnología, equipamiento y entrenamiento en medicina de emergencia. Su enfoque en salvamento y realismo fortalece nuestro respaldo en formación crítica.
8.	iSIMULATE	iSimulate desarrolla soluciones de simulación médica móviles e inteligentes que elevan la formación clínica. Su tecnología complementa nuestra misión de brindar capacitación realista, eficiente y accesible en salud.
9.	KYOTO KAGAKU	Kyoto Kagaku fabrica modelos anatómicos, simuladores y “phantoms” para imagen médica. Su precisión e innovación fortalecen nuestra excelencia educativa y liderazgo en simulación.
10.	SIMULAB 	Simulab desarrolla simuladores anatómicos de alta fidelidad que transforman la formación clínica. Su innovación tecnológica apoya nuestra misión de ofrecer aprendizaje seguro, inmersivo y centrado en resultados.
11.	SIMX	SimX desarrolla simulaciones médicas en realidad virtual inmersiva que entrenan juicio clínico realista. Su innovación potencia nuestra oferta formativa de alto impacto.
12.	NASCO	Nasco Healthcare provee simuladores clínicos, maniquíes y herramientas de entrenamiento para emergencias y cuidados avanzados. Su oferta robustece nuestra formación con tecnología confiable.
13.	TRUCORP	TruCorp fabrica maniquíes y simuladores médicos con retroalimentación en tiempo real para entrenamiento clínico. Su realismo y precisión elevan nuestra formación práctica y eficacia educativa.
14.	ERLER ZIMMER	Erler-Zimmer diseña modelos anatómicos y simuladores médicos con altísima calidad histórica. Su innovación y rigor elevan nuestra formación práctica con precisión educativa.
15.	STRATEGIC OPERATIONS	Strategic Operations desarrolla simuladores quirúrgicos de alta fidelidad que replican con exactitud la anatomía humana y las condiciones del quirófano. Gracias a esta alianza, potenciamos nuestra capacidad para brindar capacitación avanzada en entornos controlados
16.	VATA 	VATA Inc. desarrolla herramientas de simulación médica realistas (acceso vascular, heridas, modelos de ultrasonido). Su precisión eleva nuestras prácticas clínicas y fortalece nuestra formación.
17.	ADAM ROUILLY		AdamRouilly diseña desde 1918 modelos anatómicos, simuladores clínicos y herramientas formativas. Su legado, innovación y versatilidad enriquecen nuestro portafolio educativo.
18.	RUDIGER 				Rüdiger Anatomie produce modelos anatómicos y pósters educativos “Made in Germany” con manufactura artesanal. Su precisión y autenticidad enriquecen nuestra enseñanza de ciencias de la salud.
19.	3D Med	3-Dmed diseña simuladores quirúrgicos y entrenadores médicos de alta precisión. Su enfoque en realismo y desempeño mejora nuestras soluciones para la práctica clínica y educativa.
20.	SARATOGA	Saratoga Dental diseña y fabrica equipos dentales, laboratorios técnicos y simuladores formativos. Su enfoque “a medida” refuerza nuestra oferta educativa con soluciones profesionales y personalizadas.
21.	KEKLIKOĞLU	Keklikoğlu desarrolla modelos anatómicos de alta fidelidad que elevan la enseñanza clínica y veterinaria. Su compromiso con calidad e innovación fortalece nuestra misión de aprendizaje seguro y realista.

