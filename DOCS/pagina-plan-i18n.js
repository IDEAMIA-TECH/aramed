(function () {
  'use strict';

  var STORAGE_KEY = 'aramed-plan-lang';

  var T = {
    es: {
      'meta.title': 'Programa de Crecimiento Comercial y Mercadotecnia Digital | ARAMED 2026–2027',
      'meta.description': 'Propuesta de operación digital continua para convertir la plataforma IDEAMIA en el principal canal comercial de ARAMED.',
      'nav.plan': 'Plan 2026–2027',
      'nav.aria': 'Navegación principal',
      'nav.situacion': 'Situación',
      'nav.objetivos': 'Objetivos',
      'nav.alcance': 'Alcance',
      'nav.fase2': 'Fase 2',
      'nav.entregables': 'Entregables',
      'nav.planes': 'Planes',
      'nav.menuOpen': 'Abrir menú',
      'lang.aria': 'Idioma',
      'hero.badge': 'Propuesta estratégica · IDEAMIA Tech',
      'hero.title': 'Programa de Crecimiento Comercial y Mercadotecnia Digital',
      'hero.lead': 'Convertir la plataforma digital de ARAMED en el <strong>principal canal de generación de oportunidades comerciales</strong>, fortaleciendo la marca, ampliando la cobertura nacional y generando demanda constante para las líneas de producto representadas.',
      'hero.ctaScope': 'Ver alcance',
      'hero.ctaPlans': 'Planes comerciales',
      'sit.label': 'Situación actual',
      'sit.title': 'La base tecnológica ya está lista',
      'sit.intro': 'ARAMED cuenta hoy con una plataforma robusta desarrollada por IDEAMIA Tech. El siguiente paso es operarla con estrategia continua.',
      'sit.cap1t': 'Plataforma web administrable', 'sit.cap1d': 'CMS corporativo completo',
      'sit.cap2t': 'Blog profesional', 'sit.cap2d': 'Publicación y programación',
      'sit.cap3t': 'SEO Manager', 'sit.cap3d': 'Metadatos, sitemap, schema',
      'sit.cap4t': 'Newsletter & campañas', 'sit.cap4d': 'Email marketing integrado',
      'sit.cap5t': 'Gestión de leads', 'sit.cap5d': 'Formularios y cotizaciones',
      'sit.cap6t': 'Analytics', 'sit.cap6d': 'Conversion tracking GA4',
      'sit.cap7t': 'Catálogo digital', 'sit.cap7d': 'Productos y fichas técnicas',
      'sit.cap8t': 'Casos de éxito', 'sit.cap8d': 'Proyectos y evidencia',
      'sit.cap9t': 'Automatización', 'sit.cap9d': 'Contenidos programados',
      'sit.insight': '<strong>Clave:</strong> una plataforma sin estrategia de contenidos y captación produce muy poco retorno. Se requiere operación continua para generar resultados.',
      'sit.imgAlt': 'Simulación médica de alta fidelidad — tecnología ARAMED',
      'obj.label': 'Objetivos estratégicos',
      'obj.title': 'Cuatro metas para escalar el negocio',
      'obj.c1t': 'Visibilidad en educación en salud',
      'obj.c1i1': 'Medicina', 'obj.c1i2': 'Enfermería', 'obj.c1i3': 'Odontología',
      'obj.c1i4': 'Simulación clínica', 'obj.c1i5': 'Emergencias médicas', 'obj.c1i6': 'Especialidades biomédicas',
      'obj.c2t': 'Leads calificados',
      'obj.c2d': 'Incrementar la generación de contactos con intención de compra.',
      'obj.c2m1': '+30% leads año 1', 'obj.c2m2': '+50% año 2',
      'obj.c3t': 'Demanda diversificada',
      'obj.c3i1': 'Universidades medianas', 'obj.c3i2': 'Instituciones privadas',
      'obj.c3i3': 'Centros de simulación', 'obj.c3i4': 'Hospitales escuela',
      'obj.c4t': 'Liderazgo nacional',
      'obj.c4d': 'Posicionar a ARAMED como referente en simulación médica y educación en salud en México.',
      'scope.label': 'Alcance IDEAMIA Tech',
      'scope.title': 'Operación integral de mercadotecnia digital',
      'scope.intro': 'Siete pilares de ejecución mensual sobre la plataforma ya desarrollada.',
      'scope.s1img': 'Panel de administración web y gestión de contenidos CMS',
      'scope.s1t': 'Administración integral del sitio web',
      'scope.s1d': 'Operación continua del home, catálogo, blog y casos de éxito.',
      'scope.s1h1': 'Home', 'scope.s1h2': 'Catálogo · Blog · Casos de éxito',
      'scope.s1t1': 'Banners', 'scope.s1t2': 'CTAs', 'scope.s1t3': 'Promociones',
      'scope.s1t4': 'Aliados', 'scope.s1t5': 'Servicios', 'scope.s1t6': 'Productos destacados',
      'scope.s1t7': 'Nuevos productos', 'scope.s1t8': 'SEO en fichas', 'scope.s1t9': 'Planeación editorial',
      'scope.s1t10': 'Evidencia fotográfica', 'scope.s1t11': 'Videos',
      'scope.s2img': 'Redacción de artículos especializados en educación médica y simulación clínica',
      'scope.s2t': 'Estrategia de contenidos',
      'scope.s2d': 'Producción mensual de artículos SEO especializados.',
      'scope.s2i1': 'Tendencias en simulación médica', 'scope.s2i2': 'Acreditaciones COMAEM',
      'scope.s2i3': 'Laboratorios de enfermería', 'scope.s2i4': 'Centros de simulación clínica',
      'scope.s2i5': 'Anatomage y tecnología educativa', 'scope.s2i6': 'Simulación para odontología',
      'scope.s2m': 'Meta: 4 a 8 artículos / mes',
      'scope.s3img': 'Gestión de redes sociales: Facebook, Instagram, LinkedIn y YouTube',
      'scope.s3t': 'Administración de redes sociales',
      'scope.s3h1': 'Canales', 'scope.s3h2': 'Contenido & frecuencia',
      'scope.s3i1': 'Casos de éxito', 'scope.s3i2': 'Productos', 'scope.s3i3': 'Eventos',
      'scope.s3i4': 'Capacitaciones', 'scope.s3i5': 'Testimoniales', 'scope.s3i6': 'Lanzamientos',
      'scope.s3m': '12 a 20 publicaciones / mes',
      'scope.s4img': 'Captación de leads: formularios web, registros y automatización comercial',
      'scope.s4t': 'Generación de leads',
      'scope.s4d': 'Automatización dentro del sistema desarrollado.',
      'scope.s4i1': 'Formularios web', 'scope.s4i2': 'Newsletter', 'scope.s4i3': 'Landing pages',
      'scope.s4i4': 'Descarga de catálogos', 'scope.s4i5': 'Registro a webinars', 'scope.s4i6': 'Registro a eventos',
      'scope.s5img': 'Campañas de email marketing y automatización de correos',
      'scope.s5t': 'Email marketing',
      'scope.s5d': 'Módulo de campañas y plantillas desarrollado por IDEAMIA.',
      'scope.s5h1': 'Campañas', 'scope.s5h2': 'Automatizaciones',
      'scope.s5i1': 'Lanzamientos', 'scope.s5i2': 'Eventos', 'scope.s5i3': 'Congresos',
      'scope.s5i4': 'Nuevos productos', 'scope.s5i5': 'Casos de éxito',
      'scope.s5i6': 'Bienvenida', 'scope.s5i7': 'Seguimiento', 'scope.s5i8': 'Nutrición de prospectos',
      'scope.s5i9': 'Recuperación de oportunidades',
      'scope.s6img': 'SEO continuo: posicionamiento en buscadores y visibilidad digital',
      'scope.s6t': 'SEO continuo',
      'scope.s6i1': 'Metadatos', 'scope.s6i2': 'Schema', 'scope.s6i3': 'Sitemap',
      'scope.s6i4': 'Robots', 'scope.s6i5': 'Redirecciones', 'scope.s6h1': 'Términos objetivo',
      'scope.s6i6': 'Simulación médica', 'scope.s6i7': 'Simuladores médicos', 'scope.s6i8': 'Anatomage México',
      'scope.s6i9': 'Laboratorio de enfermería', 'scope.s6i10': 'Simulación clínica',
      'scope.s6i11': 'Equipamiento médico universitario',
      'scope.s7img': 'Dashboard de analítica, KPIs comerciales y reportes ejecutivos',
      'scope.s7t': 'Analítica y dirección comercial',
      'scope.s7d': 'Reporte ejecutivo mensual para marketing, ventas y dirección.',
      'scope.s7h1': 'Marketing', 'scope.s7h2': 'Comercial', 'scope.s7h3': 'Dirección',
      'scope.s7i1': 'Visitantes', 'scope.s7i2': 'Conversiones', 'scope.s7i3': 'Leads', 'scope.s7i4': 'Fuentes de tráfico',
      'scope.s7i5': 'Leads generados', 'scope.s7i6': 'Leads calificados', 'scope.s7i7': 'Oportunidades', 'scope.s7i8': 'Conversión',
      'scope.s7i9': 'ROI', 'scope.s7i10': 'CAC estimado', 'scope.s7i11': 'Tendencias', 'scope.s7i12': 'Recomendaciones',
      'fase2.banner': 'Fase 2 — Crecimiento comercial',
      'fase2.bannerSub': 'Basado en el estudio estratégico entregado por Isaac Arrayales.',
      'fase2.statLabel': 'Registros segmentados — base nacional',
      'fase2.statSub': 'Universidades · Medicina · Enfermería · Odontología · Hospitales escuela',
      'fase2.imgAlt': 'Cobertura nacional en simulación clínica',
      'fase2.regTitle': 'Regionalización digital',
      'fase2.r1': 'Zona Centro', 'fase2.r2': 'Zona Sur', 'fase2.r3': 'Zona Bajío',
      'fase2.r4': 'Zona Noreste', 'fase2.r5': 'Zona Noroeste',
      'deliver.label': 'Entregables', 'deliver.title': 'Compromiso mensual',
      'deliver.i1': 'Reporte ejecutivo', 'deliver.i2': 'Reporte SEO', 'deliver.i3': 'Reporte redes sociales',
      'deliver.i4': 'Reporte leads', 'deliver.i5': 'Reunión mensual estratégica', 'deliver.i6': 'Calendario editorial',
      'deliver.i7': 'Publicaciones programadas', 'deliver.i8': 'Campañas de email marketing',
      'plans.label': 'Esquema comercial', 'plans.title': 'Planes recomendados',
      'plans.intro': 'Inversión mensual en MXN. Escalable según objetivos de crecimiento.',
      'plans.p1n': 'Plan Essential',
      'plans.p1d': 'Administración web + SEO continuo para mantener presencia y posicionamiento base.',
      'plans.p2n': 'Plan Growth',
      'plans.p2d': 'Web + SEO + redes sociales + blog + newsletter para acelerar captación orgánica.',
      'plans.p3n': 'Plan Revenue Growth',
      'plans.p3d': 'Web + SEO + redes + blog + campañas + automatización + dirección comercial digital.',
      'plans.perMonth': 'MXN / mes',
      'plans.featured': 'Recomendado',
      'footer.l1': '<strong>ARAMED y Laboratorios</strong> · Programa 2026–2027',
      'footer.l2': 'Operación digital por <strong>IDEAMIA Tech</strong> · Propuesta de crecimiento comercial',
      'footer.l3': 'Documento generado a partir del plan estratégico · Confidencial'
    },
    en: {
      'meta.title': 'Commercial Growth & Digital Marketing Program | ARAMED 2026–2027',
      'meta.description': 'Proposal for ongoing digital operations to turn the IDEAMIA platform into ARAMED\'s primary commercial channel.',
      'nav.plan': 'Plan 2026–2027',
      'nav.aria': 'Main navigation',
      'nav.situacion': 'Current state',
      'nav.objetivos': 'Goals',
      'nav.alcance': 'Scope',
      'nav.fase2': 'Phase 2',
      'nav.entregables': 'Deliverables',
      'nav.planes': 'Plans',
      'nav.menuOpen': 'Open menu',
      'lang.aria': 'Language',
      'hero.badge': 'Strategic proposal · IDEAMIA Tech',
      'hero.title': 'Commercial Growth & Digital Marketing Program',
      'hero.lead': 'Turn ARAMED\'s digital platform into the <strong>primary channel for generating commercial opportunities</strong>, strengthening the brand, expanding national coverage, and driving steady demand for the product lines represented.',
      'hero.ctaScope': 'View scope',
      'hero.ctaPlans': 'Commercial plans',
      'sit.label': 'Current situation',
      'sit.title': 'The technology foundation is ready',
      'sit.intro': 'ARAMED now has a robust platform built by IDEAMIA Tech. The next step is to operate it with a continuous strategy.',
      'sit.cap1t': 'Manageable web platform', 'sit.cap1d': 'Full corporate CMS',
      'sit.cap2t': 'Professional blog', 'sit.cap2d': 'Publishing and scheduling',
      'sit.cap3t': 'SEO Manager', 'sit.cap3d': 'Metadata, sitemap, schema',
      'sit.cap4t': 'Newsletter & campaigns', 'sit.cap4d': 'Integrated email marketing',
      'sit.cap5t': 'Lead management', 'sit.cap5d': 'Forms and quotes',
      'sit.cap6t': 'Analytics', 'sit.cap6d': 'GA4 conversion tracking',
      'sit.cap7t': 'Digital catalog', 'sit.cap7d': 'Products and spec sheets',
      'sit.cap8t': 'Success stories', 'sit.cap8d': 'Projects and proof',
      'sit.cap9t': 'Automation', 'sit.cap9d': 'Scheduled content',
      'sit.insight': '<strong>Key point:</strong> a platform without content and lead-generation strategy delivers very little return. Continuous operation is required to generate results.',
      'sit.imgAlt': 'High-fidelity medical simulation — ARAMED technology',
      'obj.label': 'Strategic goals',
      'obj.title': 'Four goals to scale the business',
      'obj.c1t': 'Visibility in health education',
      'obj.c1i1': 'Medicine', 'obj.c1i2': 'Nursing', 'obj.c1i3': 'Dentistry',
      'obj.c1i4': 'Clinical simulation', 'obj.c1i5': 'Medical emergencies', 'obj.c1i6': 'Biomedical specialties',
      'obj.c2t': 'Qualified leads',
      'obj.c2d': 'Increase generation of contacts with purchase intent.',
      'obj.c2m1': '+30% leads year 1', 'obj.c2m2': '+50% year 2',
      'obj.c3t': 'Diversified demand',
      'obj.c3i1': 'Mid-size universities', 'obj.c3i2': 'Private institutions',
      'obj.c3i3': 'Simulation centers', 'obj.c3i4': 'Teaching hospitals',
      'obj.c4t': 'National leadership',
      'obj.c4d': 'Position ARAMED as a reference in medical simulation and health education in Mexico.',
      'scope.label': 'IDEAMIA Tech scope',
      'scope.title': 'Full digital marketing operations',
      'scope.intro': 'Seven pillars of monthly execution on the platform already built.',
      'scope.s1img': 'Web admin panel and CMS content management',
      'scope.s1t': 'Full website administration',
      'scope.s1d': 'Ongoing operation of home, catalog, blog, and success stories.',
      'scope.s1h1': 'Home', 'scope.s1h2': 'Catalog · Blog · Success stories',
      'scope.s1t1': 'Banners', 'scope.s1t2': 'CTAs', 'scope.s1t3': 'Promotions',
      'scope.s1t4': 'Partners', 'scope.s1t5': 'Services', 'scope.s1t6': 'Featured products',
      'scope.s1t7': 'New products', 'scope.s1t8': 'SEO on product pages', 'scope.s1t9': 'Editorial planning',
      'scope.s1t10': 'Photo evidence', 'scope.s1t11': 'Videos',
      'scope.s2img': 'Specialized articles on medical education and clinical simulation',
      'scope.s2t': 'Content strategy',
      'scope.s2d': 'Monthly production of specialized SEO articles.',
      'scope.s2i1': 'Medical simulation trends', 'scope.s2i2': 'COMAEM accreditations',
      'scope.s2i3': 'Nursing labs', 'scope.s2i4': 'Clinical simulation centers',
      'scope.s2i5': 'Anatomage and educational technology', 'scope.s2i6': 'Dentistry simulation',
      'scope.s2m': 'Target: 4 to 8 articles / month',
      'scope.s3img': 'Social media management: Facebook, Instagram, LinkedIn and YouTube',
      'scope.s3t': 'Social media management',
      'scope.s3h1': 'Channels', 'scope.s3h2': 'Content & frequency',
      'scope.s3i1': 'Success stories', 'scope.s3i2': 'Products', 'scope.s3i3': 'Events',
      'scope.s3i4': 'Training', 'scope.s3i5': 'Testimonials', 'scope.s3i6': 'Launches',
      'scope.s3m': '12 to 20 posts / month',
      'scope.s4img': 'Lead capture: web forms, registrations and sales automation',
      'scope.s4t': 'Lead generation',
      'scope.s4d': 'Automation within the developed system.',
      'scope.s4i1': 'Web forms', 'scope.s4i2': 'Newsletter', 'scope.s4i3': 'Landing pages',
      'scope.s4i4': 'Catalog downloads', 'scope.s4i5': 'Webinar registration', 'scope.s4i6': 'Event registration',
      'scope.s5img': 'Email marketing campaigns and email automation',
      'scope.s5t': 'Email marketing',
      'scope.s5d': 'Campaign and template module built by IDEAMIA.',
      'scope.s5h1': 'Campaigns', 'scope.s5h2': 'Automations',
      'scope.s5i1': 'Launches', 'scope.s5i2': 'Events', 'scope.s5i3': 'Conferences',
      'scope.s5i4': 'New products', 'scope.s5i5': 'Success stories',
      'scope.s5i6': 'Welcome', 'scope.s5i7': 'Follow-up', 'scope.s5i8': 'Prospect nurturing',
      'scope.s5i9': 'Opportunity recovery',
      'scope.s6img': 'Ongoing SEO: search rankings and digital visibility',
      'scope.s6t': 'Ongoing SEO',
      'scope.s6i1': 'Metadata', 'scope.s6i2': 'Schema', 'scope.s6i3': 'Sitemap',
      'scope.s6i4': 'Robots', 'scope.s6i5': 'Redirects', 'scope.s6h1': 'Target keywords',
      'scope.s6i6': 'Medical simulation', 'scope.s6i7': 'Medical simulators', 'scope.s6i8': 'Anatomage Mexico',
      'scope.s6i9': 'Nursing lab', 'scope.s6i10': 'Clinical simulation',
      'scope.s6i11': 'University medical equipment',
      'scope.s7img': 'Analytics dashboard, commercial KPIs and executive reports',
      'scope.s7t': 'Analytics and commercial direction',
      'scope.s7d': 'Monthly executive report for marketing, sales and leadership.',
      'scope.s7h1': 'Marketing', 'scope.s7h2': 'Sales', 'scope.s7h3': 'Leadership',
      'scope.s7i1': 'Visitors', 'scope.s7i2': 'Conversions', 'scope.s7i3': 'Leads', 'scope.s7i4': 'Traffic sources',
      'scope.s7i5': 'Leads generated', 'scope.s7i6': 'Qualified leads', 'scope.s7i7': 'Opportunities', 'scope.s7i8': 'Conversion',
      'scope.s7i9': 'ROI', 'scope.s7i10': 'Estimated CAC', 'scope.s7i11': 'Trends', 'scope.s7i12': 'Recommendations',
      'fase2.banner': 'Phase 2 — Commercial growth',
      'fase2.bannerSub': 'Based on the strategic study delivered by Isaac Arrayales.',
      'fase2.statLabel': 'Segmented records — national database',
      'fase2.statSub': 'Universities · Medicine · Nursing · Dentistry · Teaching hospitals',
      'fase2.imgAlt': 'National coverage in clinical simulation',
      'fase2.regTitle': 'Digital regionalization',
      'fase2.r1': 'Central region', 'fase2.r2': 'Southern region', 'fase2.r3': 'Bajío region',
      'fase2.r4': 'Northeast region', 'fase2.r5': 'Northwest region',
      'deliver.label': 'Deliverables', 'deliver.title': 'Monthly commitment',
      'deliver.i1': 'Executive report', 'deliver.i2': 'SEO report', 'deliver.i3': 'Social media report',
      'deliver.i4': 'Leads report', 'deliver.i5': 'Monthly strategy meeting', 'deliver.i6': 'Editorial calendar',
      'deliver.i7': 'Scheduled posts', 'deliver.i8': 'Email marketing campaigns',
      'plans.label': 'Commercial model', 'plans.title': 'Recommended plans',
      'plans.intro': 'Monthly investment in MXN. Scalable based on growth goals.',
      'plans.p1n': 'Essential Plan',
      'plans.p1d': 'Web administration + ongoing SEO to maintain presence and baseline positioning.',
      'plans.p2n': 'Growth Plan',
      'plans.p2d': 'Web + SEO + social + blog + newsletter to accelerate organic lead capture.',
      'plans.p3n': 'Revenue Growth Plan',
      'plans.p3d': 'Web + SEO + social + blog + campaigns + automation + digital commercial direction.',
      'plans.perMonth': 'MXN / month',
      'plans.featured': 'Recommended',
      'footer.l1': '<strong>ARAMED y Laboratorios</strong> · Program 2026–2027',
      'footer.l2': 'Digital operations by <strong>IDEAMIA Tech</strong> · Commercial growth proposal',
      'footer.l3': 'Document generated from the strategic plan · Confidential'
    }
  };

  function t(lang, key) {
    var dict = T[lang] || T.es;
    return dict[key] != null ? dict[key] : (T.es[key] || '');
  }

  function applyLanguage(lang) {
    if (lang !== 'en') lang = 'es';
    var dict = T[lang];

    document.querySelectorAll('[data-i18n]').forEach(function (el) {
      var key = el.getAttribute('data-i18n');
      if (dict[key] != null) el.textContent = dict[key];
    });

    document.querySelectorAll('[data-i18n-html]').forEach(function (el) {
      var key = el.getAttribute('data-i18n-html');
      if (dict[key] != null) el.innerHTML = dict[key];
    });

    document.querySelectorAll('[data-i18n-alt]').forEach(function (el) {
      var key = el.getAttribute('data-i18n-alt');
      if (dict[key] != null) el.setAttribute('alt', dict[key]);
    });

    document.querySelectorAll('[data-i18n-attr]').forEach(function (el) {
      el.getAttribute('data-i18n-attr').split(';').forEach(function (pair) {
        var parts = pair.trim().split(':');
        if (parts.length === 2 && dict[parts[1].trim()] != null) {
          el.setAttribute(parts[0].trim(), dict[parts[1].trim()]);
        }
      });
    });

    document.title = dict['meta.title'] || document.title;
    var meta = document.getElementById('metaDescription');
    if (meta && dict['meta.description']) meta.setAttribute('content', dict['meta.description']);

    var htmlRoot = document.getElementById('htmlRoot');
    if (htmlRoot) htmlRoot.setAttribute('lang', lang === 'en' ? 'en' : 'es-MX');

    var featured = document.getElementById('featuredPlan');
    if (featured && dict['plans.featured']) {
      featured.setAttribute('data-featured-label', dict['plans.featured']);
    }

    document.querySelectorAll('.lang-btn').forEach(function (btn) {
      var active = btn.getAttribute('data-lang') === lang;
      btn.classList.toggle('active', active);
      btn.setAttribute('aria-pressed', active ? 'true' : 'false');
    });

    try { localStorage.setItem(STORAGE_KEY, lang); } catch (e) { /* ignore */ }
  }

  document.querySelectorAll('.lang-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      applyLanguage(btn.getAttribute('data-lang'));
    });
  });

  var saved = 'es';
  try { saved = localStorage.getItem(STORAGE_KEY) || 'es'; } catch (e) { /* ignore */ }
  if (saved === 'en') applyLanguage('en');
})();
