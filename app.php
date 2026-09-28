<?php
require_once __DIR__ . '/config.php';

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function base_url(string $path = ''): string {
    $base = rtrim(BASE_URL, '/');
    if ($path === '') return $base ?: '/';
    return $base . '/' . ltrim($path, '/');
}

function image_url(string $path): string {
    return base_url('images/' . ltrim($path, '/'));
}

function translations(): array {
    static $data = null;
    if ($data === null) {
        $json = file_get_contents(__DIR__ . '/data/translations.json');
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
    return $data;
}

function dict(string $locale): array {
    $all = translations();
    return $all[$locale] ?? $all['es'];
}

function supported_locale(string $locale): bool {
    return in_array($locale, ['es','ca','en','fr','de'], true);
}

function route_url(string $locale, string $page = 'home'): string {
    $routes = [
        'home' => '',
        'servicio-tecnico' => 'servicio-tecnico',
        'patinetes' => 'patinetes',
        'fibra-movil' => 'fibra-movil',
        'empresas' => 'empresas',
        'contacto' => 'contacto',
        'tienda' => 'tienda',
    ];
    $slug = $routes[$page] ?? '';
    return base_url($locale . '/' . ($slug ? $slug . '/' : ''));
}

function current_page_title(array $d, string $page): string {
    return match($page) {
        'servicio-tecnico' => $d['service']['title'],
        'patinetes' => $d['scooters']['title'],
        'fibra-movil' => $d['telecom']['title'],
        'empresas' => $d['business']['title'],
        'contacto' => $d['contact']['title'],
        'tienda' => $d['catalog']['title'],
        default => $d['home']['title'],
    };
}

function whatsapp_url(string $message): string {
    return 'https://wa.me/' . SITE_WHATSAPP . '?text=' . rawurlencode($message);
}

function render_whatsapp_button(string $label, string $message, string $extra=''): void {
    echo '<a class="btn btn-dark ' . h($extra) . '" href="' . h(whatsapp_url($message)) . '" target="_blank" rel="noopener noreferrer">' . h($label) . '</a>';
}

function language_name(string $locale): string {
    return ['es'=>'Español','ca'=>'Català','en'=>'English','fr'=>'Français','de'=>'Deutsch'][$locale] ?? 'Español';
}

function render_topbar(string $locale, array $d): void { ?>
<div class="topbar">
  <div class="container topbar-inner">
    <div class="topbar-left">
      <a href="tel:<?= h(SITE_PHONE_RAW) ?>"><?= h(SITE_PHONE) ?></a>
      <a href="https://wa.me/<?= h(SITE_WHATSAPP) ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
      <span class="desktop-wide"><?= h(SITE_ADDRESS_SHORT) ?></span>
      <span>L-V · 09:00–14:00 · 16:00–19:00</span>
    </div>
    <div class="topbar-right">
      <span id="store-status" class="store-status" data-locale="<?= h($locale) ?>"
        data-open="<?= h($d['status']['open']) ?>" data-closed="<?= h($d['status']['closed']) ?>"
        data-until="<?= h($d['status']['until']) ?>" data-openat="<?= h($d['status']['openAt']) ?>"
        data-tomorrow="<?= h($d['status']['tomorrow']) ?>" data-monday="<?= h($d['status']['monday']) ?>"></span>
      <details class="language-switcher">
        <summary aria-label="Idioma"><span class="globe">◎</span><span class="lang-full"><?= h(language_name($locale)) ?></span><span class="lang-short"><?= strtoupper(h($locale)) ?></span><span>⌄</span></summary>
        <div class="language-menu">
          <?php foreach(['es','ca','en','fr','de'] as $lang): ?>
            <a href="#" data-lang-link="<?= h($lang) ?>" class="<?= $lang===$locale?'active':'' ?>"><?= h(language_name($lang)) ?><?= $lang===$locale?' ✓':'' ?></a>
          <?php endforeach; ?>
        </div>
        <div id="language-hint" class="language-hint" hidden>🌐 <?= h(language_hint($locale)) ?><button type="button" aria-label="Cerrar">×</button></div>
      </details>
    </div>
  </div>
</div>
<?php }

function language_hint(string $locale): string {
    return [
      'es'=>'Puedes cambiar el idioma aquí.', 'ca'=>'Pots canviar l’idioma aquí.',
      'en'=>'You can change language here.', 'fr'=>'Vous pouvez changer la langue ici.',
      'de'=>'Hier können Sie die Sprache ändern.'
    ][$locale] ?? 'Puedes cambiar el idioma aquí.';
}

function render_header(string $locale, array $d, string $page): void {
    $nav = [
      ['home',$d['nav']['home']],['servicio-tecnico',$d['nav']['service']],['patinetes',$d['nav']['scooters']],
      ['fibra-movil',$d['nav']['telecom']],['empresas',$d['nav']['business']],['contacto',$d['nav']['contact']]
    ]; ?>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="<?= h(route_url($locale)) ?>">
      <img src="<?= h(image_url('brand/app-logo.svg')) ?>" alt="APP Informática Menorca">
      <span class="brand-words"><strong>INFORMÁTICA</strong><small>MENORCA</small></span>
    </a>
    <nav class="desktop-nav" aria-label="Principal">
      <?php foreach($nav as [$id,$label]): ?><a class="<?= $page===$id?'active':'' ?>" href="<?= h(route_url($locale,$id)) ?>"><?= h($label) ?></a><?php endforeach; ?>
      <a href="<?= h(GAMING_CONFIGURATOR_URL) ?>" target="_blank" rel="noopener noreferrer"><?= h($d['nav']['pcBuilder']) ?></a>
      <a class="btn btn-primary nav-store" href="<?= h(ONLINE_STORE_URL) ?>" target="_blank" rel="noopener noreferrer"><?= h($d['nav']['online']) ?></a>
    </nav>
    <details class="mobile-menu">
      <summary><?= h($d['nav']['menu']) ?></summary>
      <nav>
        <?php foreach($nav as [$id,$label]): ?><a href="<?= h(route_url($locale,$id)) ?>"><?= h($label) ?></a><?php endforeach; ?>
        <a href="<?= h(GAMING_CONFIGURATOR_URL) ?>" target="_blank" rel="noopener noreferrer"><?= h($d['nav']['pcBuilder']) ?></a>
        <a class="btn btn-primary" href="<?= h(ONLINE_STORE_URL) ?>" target="_blank" rel="noopener noreferrer"><?= h($d['nav']['online']) ?></a>
      </nav>
    </details>
  </div>
</header>
<?php }

function render_footer(array $d): void { ?>
<footer class="site-footer">
  <div class="container footer-grid">
    <div><strong>APP INFORMÁTICA</strong><p><?= h($d['footer']) ?></p></div>
    <div><p><?= h(SITE_ADDRESS) ?></p><p><?= h(SITE_PHONE) ?></p><p><?= h(SITE_EMAIL) ?></p><a class="instagram" href="<?= h(INSTAGRAM_URL) ?>" target="_blank" rel="noopener noreferrer">◎ @tecnymaticamenorca</a></div>
    <div class="copyright">© APP Informática Menorca</div>
  </div>
</footer>
<?php }

function render_verifactu(string $locale, bool $compact=false): void {
  $copy = [
    'es'=>['VERIFACTU','Prepara tu negocio para VeriFactu','sin complicarte','Software de facturación o TPV para autónomos, comercios y empresas. Consulta qué solución encaja mejor con tu negocio.','Quiero saber qué solución necesito','Ver soluciones','Soluciones de facturación y TPV adaptadas a VeriFactu'],
    'ca'=>['VERIFACTU','Prepara el teu negoci per a VeriFactu','sense complicacions','Software de facturació o TPV per a autònoms, comerços i empreses. Consulta quina solució encaixa millor amb el teu negoci.','Vull saber quina solució necessito','Veure solucions','Solucions de facturació i TPV adaptades a VeriFactu'],
    'en'=>['VERIFACTU','Get your business ready for VeriFactu','without the hassle','Billing or POS software for self-employed professionals, shops and businesses. Find the solution that best fits your needs.','Help me choose a solution','View solutions','Billing and POS solutions adapted to VeriFactu'],
    'fr'=>['VERIFACTU','Préparez votre entreprise à VeriFactu','en toute simplicité','Logiciel de facturation ou TPV pour indépendants, commerces et entreprises. Découvrez la solution la plus adaptée à votre activité.','Trouver la bonne solution','Voir les solutions','Solutions de facturation et TPV adaptées à VeriFactu'],
    'de'=>['VERIFACTU','Machen Sie Ihr Unternehmen bereit für VeriFactu','ganz unkompliziert','Abrechnungs- oder Kassensoftware für Selbstständige, Geschäfte und Unternehmen. Finden Sie die passende Lösung für Ihren Betrieb.','Passende Lösung finden','Lösungen ansehen','Abrechnungs- und Kassensysteme für VeriFactu']
  ][$locale] ?? null;
  [$eyebrow,$titleA,$titleB,$desc,$primary,$secondary,$note]=$copy; ?>
<section class="section promo-section">
 <div class="container"><div class="promo verifactu <?= $compact?'compact':'' ?>">
  <div class="promo-copy">
    <div class="promo-kicker"><span><?= h($eyebrow) ?></span><small><?= h($note) ?></small></div>
    <h2><?= h($titleA) ?> <em><?= h($titleB) ?></em></h2>
    <p><?= h($desc) ?></p>
    <div class="actions"><a class="btn btn-yellow" href="<?= h(VERIFACTU_URL) ?>" target="_blank" rel="noopener noreferrer"><?= h($primary) ?> <b>→</b></a><a class="btn btn-outline-teal" href="<?= h(VERIFACTU_URL) ?>" target="_blank" rel="noopener noreferrer"><?= h($secondary) ?></a></div>
    <div class="verifactu-logo"><img src="<?= h(image_url('verifactu/verifactu-logo.jpg')) ?>" alt="VeriFactu"></div>
  </div>
  <a class="promo-image" href="<?= h(VERIFACTU_URL) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= h($primary) ?>"><img src="<?= h(image_url('verifactu/verifactu-worker.jpg')) ?>" alt="TPV para comercio y hostelería"></a>
 </div></div>
</section>
<?php }

function render_gaming_promo(string $locale): void {
  $copy=[
   'es'=>['PC GAMING','Configura tu próximo PC gaming','Accede al configurador oficial de APP y monta un equipo a medida con componentes gaming, distintas configuraciones y compra directa.','Abrir configurador','Ver opciones'],
   'ca'=>['PC GAMING','Configura el teu proper PC gaming','Accedeix al configurador oficial d\'APP i munta un equip a mida amb components gaming, diferents configuracions i compra directa.','Obrir configurador','Veure opcions'],
   'en'=>['GAMING PC','Build your next gaming PC','Access APP\'s official configurator and create a custom build with gaming components, multiple setup options and direct purchase.','Open configurator','View options'],
   'fr'=>['PC GAMING','Configurez votre prochain PC gaming','Accédez au configurateur officiel d\'APP et créez une configuration sur mesure avec des composants gaming, plusieurs options et achat direct.','Ouvrir le configurateur','Voir les options'],
   'de'=>['GAMING-PC','Stellen Sie Ihren nächsten Gaming-PC zusammen','Öffnen Sie den offiziellen APP-Konfigurator und erstellen Sie ein individuelles System mit Gaming-Komponenten, verschiedenen Optionen und direktem Kauf.','Konfigurator öffnen','Optionen ansehen']
  ][$locale]; ?>
<section class="section promo-section"><div class="container"><div class="promo gaming">
 <div class="promo-copy"><span class="gaming-pill"><?= h($copy[0]) ?></span><h2><?= h($copy[1]) ?></h2><p><?= h($copy[2]) ?></p><div class="actions"><a class="btn btn-gaming" href="<?= h(GAMING_CONFIGURATOR_URL) ?>" target="_blank" rel="noopener noreferrer"><?= h($copy[3]) ?> <b>→</b></a><a class="btn btn-gaming-secondary" href="<?= h(GAMING_CONFIGURATOR_URL) ?>" target="_blank" rel="noopener noreferrer"><?= h($copy[4]) ?></a></div></div>
 <a class="promo-image" href="<?= h(GAMING_CONFIGURATOR_URL) ?>" target="_blank" rel="noopener noreferrer"><img src="<?= h(image_url('promos/configurador-pc-gaming.jpg')) ?>" alt="Configurador PC Gaming"></a>
</div></div></section>
<?php }

function render_home(string $locale, array $d): void {
  $home=$d['home'];
  $categoryImages=['home/categoria-portatiles.jfif','home/categoria-impresoras.jfif','home/categoria-gaming-consolas.jfif','home/categoria-televisores.jfif'];
  $services=[
   ['servicio-tecnico','#ef2b33','#fff','home/section-art/servicio_tecnico.png'],
   ['patinetes','#ffc52f','#111','home/section-art/patinetes.png'],
   ['fibra-movil','#12b9df','#111','home/section-art/fibra_movil.png'],
   ['empresas','#5747d6','#fff','home/section-art/fincas.png']
  ]; ?>
<section class="section hero-section"><div class="container"><div class="split-card home-hero">
 <div class="hero-copy"><p class="eyebrow"><?= h($home['eyebrow']) ?></p><h1><?= h($home['title']) ?></h1><p class="lead"><?= h($home['description']) ?></p><div class="actions"><a class="btn btn-primary" href="<?= h(route_url($locale,'tienda')) ?>"><?= h($home['products']) ?> <b>→</b></a><a class="btn btn-outline" href="<?= h(whatsapp_url($home['whatsapp'])) ?>" target="_blank" rel="noopener noreferrer"><?= h($home['whatsapp']) ?></a></div><div class="trust-grid"><?php foreach($home['trust'] as $item): ?><p><b>✓</b><?= h($item) ?></p><?php endforeach; ?></div></div>
 <div class="hero-image fade-left"><img src="<?= h(image_url('home/tienda-app.jpeg')) ?>" alt="APP Informática Menorca"></div>
</div></div></section>

<section class="section"><div class="container"><div class="section-heading split-heading"><div><p class="eyebrow"><?= h($home['categoriesEyebrow']) ?></p><h2><?= h($home['categoriesTitle']) ?></h2></div><a href="<?= h(route_url($locale,'tienda')) ?>"><?= h($home['allCategories']) ?> →</a></div>
<div class="category-grid"><?php foreach($home['categories'] as $i=>$item): ?><a class="category-card" href="<?= h(route_url($locale,'tienda')) ?>"><img src="<?= h(image_url($categoryImages[$i])) ?>" alt=""><span class="category-shade"></span><div><h3><?= h($item[0]) ?></h3><p><?= h($item[1]) ?></p><b class="round-arrow">→</b></div></a><?php endforeach; ?></div></div></section>

<?php render_gaming_promo($locale); ?>

<section class="section"><div class="container"><div class="section-heading"><p class="eyebrow"><?= h($home['servicesEyebrow']) ?></p><h2><?= h($home['servicesTitle']) ?></h2></div><div class="services-grid">
<?php foreach($home['cards'] as $i=>$card): $meta=$services[$i]; ?><article class="service-card" style="--card-bg:<?= h($meta[1]) ?>;--card-fg:<?= h($meta[2]) ?>"><div class="service-copy"><div><p><?= h($card[0]) ?></p><h3><?= h($card[1]) ?></h3><span><?= h($card[2]) ?></span></div><a class="btn btn-light" href="<?= h(route_url($locale,$meta[0])) ?>"><?= h($card[3]) ?></a></div><div class="service-art"><img src="<?= h(image_url($meta[3])) ?>" alt=""></div></article><?php endforeach; ?>
</div>
<div class="brand-grid"><?php foreach([[$home['apple'],'home/section-art/apple.png','#f1f2f3'],[$home['xiaomi'],'home/section-art/xiaomi.png','#fff1e5']] as $brand): ?><article class="brand-card" style="background:<?= h($brand[2]) ?>"><div><p><?= h($brand[0][0]) ?></p><h3><?= h($brand[0][1]) ?></h3><span><?= h($brand[0][2]) ?></span><a class="btn btn-light" href="<?= h(route_url($locale,'tienda')) ?>"><?= h($d['common']['more']) ?></a></div><img src="<?= h(image_url($brand[1])) ?>" alt=""></article><?php endforeach; ?></div></div></section>

<?php render_verifactu($locale); ?>

<section class="section"><div class="container"><div class="slow-promo"><div><p><?= h($home['promo'][0]) ?></p><h2><?= h($home['promo'][1]) ?></h2><span><?= h($home['promo'][2]) ?></span><a class="btn btn-light" href="<?= h(route_url($locale,'servicio-tecnico')) ?>"><?= h($home['promo'][3]) ?></a></div><img src="<?= h(image_url('service/ordenador-lento.png')) ?>" alt=""></div></div></section>

<section class="section"><div class="container"><div class="badges"><?php foreach($home['badges'] as $item): ?><div><strong><?= h($item[0]) ?></strong><p><?= h($item[1]) ?></p></div><?php endforeach; ?></div></div></section>
<?php render_store_block($locale,$d); }

function render_store_block(string $locale,array $d): void { ?>
<section class="section store-section"><div class="container"><div class="store-card"><div class="store-copy"><p class="eyebrow muted">APP Informática Menorca</p><h2><?= h($d['common']['store']) ?></h2><p class="address"><?= h(SITE_ADDRESS) ?></p><p><?= h(SITE_PHONE) ?></p><p><?= h(SITE_EMAIL) ?></p><div class="schedule"><p><?= h($d['common']['schedule']) ?></p><strong><?= h($d['common']['weekdays']) ?></strong><span>09:00–14:00 · 16:00–19:00</span><small><?= h($d['common']['closedWeekend']) ?></small></div><div class="actions"><?php render_whatsapp_button($d['common']['whatsapp'],'APP Informática Menorca'); ?><a class="btn btn-outline" href="<?= h(MAPS_DIRECTIONS_URL) ?>" target="_blank" rel="noopener noreferrer"><?= h($d['common']['directions']) ?></a></div></div><div class="map-card"><div><strong><?= h($d['common']['map']) ?></strong><p><?= h(SITE_ADDRESS) ?></p></div><iframe title="<?= h($d['common']['map']) ?>" src="<?= h(MAPS_EMBED_URL) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div></div></div></section>
<?php }

function render_service_page(string $locale,array $d): void { $x=$d['service']; ?>
<section class="section page-narrow"><div class="container narrow"><div class="split-card service-hero"><div class="page-copy"><p class="eyebrow muted"><?= h($x['eyebrow']) ?></p><h1><?= h($x['title']) ?></h1><p class="lead"><?= h($x['description']) ?></p></div><div class="hero-image fade-left"><img src="<?= h(image_url('service/servicio-tecnico-taller.jfif')) ?>" alt="<?= h($x['title']) ?>"></div></div><ul class="feature-list two-cols"><?php foreach($x['items'] as $item): ?><li>✓ <?= h($item) ?></li><?php endforeach; ?></ul><div class="page-actions"><?php render_whatsapp_button($d['common']['whatsapp'],$x['whatsapp']); ?></div></div></section>
<?php }

function render_scooters_page(string $locale,array $d): void { $x=$d['scooters']; $families=[['ZWOLF','zwolf.png'],['ZCOUGAR','zcougar.png'],['ZRINO','zrino.png'],['ZLION','zlion.png'],['ZFOX','zfox.png'],['ZTOY','ztoy.png']]; ?>
<section class="section"><div class="container"><div class="scooter-hero"><div><p class="eyebrow"><?= h($x['eyebrow']) ?></p><h1><?= h($x['title']) ?></h1><p class="lead"><?= h($x['description']) ?></p><div class="actions"><a class="btn btn-primary" href="#gamas"><?= h($x['view']) ?></a><?php render_whatsapp_button($x['whatsapp'],'APP Informática Menorca - '.$x['title']); ?></div></div><div class="zwheel-logo"><img src="<?= h(image_url('brand/zwheel-logo.png')) ?>" alt="ZWheel"></div></div></div></section>
<section class="section"><div class="container"><div class="pillars"><?php foreach($x['pillars'] as $p): ?><div><h2><?= h($p[0]) ?></h2><p><?= h($p[1]) ?></p></div><?php endforeach; ?></div></div></section>
<section class="section" id="gamas"><div class="container"><div class="section-heading"><p class="eyebrow"><?= h($x['rangesEyebrow']) ?></p><h2><?= h($x['rangesTitle']) ?></h2><p><?= h($x['rangesDesc']) ?></p></div><div class="scooter-grid"><?php foreach($families as $i=>$f): $info=$x['familyTitles'][$i]; ?><article class="scooter-card"><div class="square-image"><img src="<?= h(image_url('patinetes/'.$f[1])) ?>" alt="<?= h($f[0]) ?>"></div><div class="scooter-copy"><p><?= h($f[0]) ?></p><h3><?= h($info[0]) ?></h3><span><?= h($info[1]) ?></span><div class="tags"><?php foreach($info[2] as $tag): ?><b><?= h($tag) ?></b><?php endforeach; ?></div><?php render_whatsapp_button($x['consult'],'APP Informática Menorca - '.$f[0]); ?></div></article><?php endforeach; ?></div></div></section>
<section class="section"><div class="container"><div class="workshop"><p class="eyebrow"><?= h($x['workshop']) ?></p><h2><?= h($x['workshopTitle']) ?></h2><ul class="feature-list three-cols"><?php foreach($x['workshopItems'] as $item): ?><li>✓ <?= h($item) ?></li><?php endforeach; ?></ul><a class="btn btn-primary" href="<?= h(route_url($locale,'contacto')) ?>"><?= h($x['contact']) ?></a></div></div></section>
<?php }

function render_telecom_page(string $locale,array $d): void { $x=$d['telecom']; $ops=[['O2','brand/o2-logo-white.svg',true],['Digi','brand/digi-logo.svg',false],['MásMóvil','brand/masmovil-logo.svg',false]]; $tv=[['Movistar Plus+','fibra-movil/movistar-plus.png','dark'],['Netflix','fibra-movil/netflix.png','light'],['Disney+','fibra-movil/disney-plus.png','teal']]; ?>
<section class="section"><div class="container"><div class="telecom-hero"><div><img class="o2" src="<?= h(image_url('brand/o2-logo-white.svg')) ?>" alt="O2"><p class="eyebrow white"><?= h($x['eyebrow']) ?></p><h1><?= h($x['title']) ?></h1><p class="lead"><?= h($x['description']) ?></p><?php render_whatsapp_button($x['rates'],'APP Informática Menorca - '.$x['title']); ?></div><div class="hero-image"><img src="<?= h(image_url('fibra-movil/tv-hero.png')) ?>" alt="TV streaming"></div></div></div></section>
<section class="section"><div class="container"><div class="section-heading"><p class="eyebrow"><?= h($x['sectionEyebrow']) ?></p><h2><?= h($x['sectionTitle']) ?></h2></div><div class="operators"><?php foreach($ops as $op): ?><div><span class="<?= $op[2]?'dark':'' ?>"><img src="<?= h(image_url($op[1])) ?>" alt="<?= h($op[0]) ?>"></span></div><?php endforeach; ?></div><ul class="feature-list three-cols"><?php foreach($x['services'] as $item): ?><li>✓ <?= h($item) ?></li><?php endforeach; ?></ul></div></section>
<section class="section"><div class="container"><div class="section-heading"><p class="eyebrow"><?= h($x['tvEyebrow']) ?></p><h2><?= h($x['tvTitle']) ?></h2><p><?= h($x['tvDesc']) ?></p></div><div class="tv-list"><?php foreach($tv as $i=>$item): ?><article class="tv-card <?= h($item[2]) ?> <?= $i%2?'reverse':'' ?>"><div class="tv-image"><img src="<?= h(image_url($item[1])) ?>" alt="<?= h($item[0]) ?>"></div><div class="tv-copy"><h3><?= h($item[0]) ?></h3><ul><?php foreach($x['features'][$i] as $feature): ?><li>✓ <span><?= h($feature) ?></span></li><?php endforeach; ?></ul><?php render_whatsapp_button($x['consult'].' '.$item[0],'APP Informática Menorca - '.$item[0]); ?></div></article><?php endforeach; ?></div></div></section>
<?php }

function render_business_page(string $locale,array $d): void { $x=$d['business']; render_verifactu($locale,true); ?>
<section class="section hero-after-promo"><div class="container"><div class="split-card business-hero"><div class="hero-copy"><p class="eyebrow"><?= h($x['eyebrow']) ?></p><h1><?= h($x['title']) ?></h1><p class="lead"><?= h($x['description']) ?></p><?php render_whatsapp_button($x['action'],'APP Informática Menorca - '.$x['title']); ?><div class="highlight-grid"><?php foreach($x['highlights'] as $item): ?><div><?= h($item) ?></div><?php endforeach; ?></div><div class="coverage"><span>✓ <?= h($x['coverage']) ?></span><span>✓ <?= h($x['onsite']) ?></span></div></div><div class="hero-image fade-left"><img src="<?= h(image_url('empresas/hero-empresas.png')) ?>" alt="<?= h($x['title']) ?>"></div></div></div></section>
<section class="section"><div class="container"><div class="section-heading"><p class="eyebrow"><?= h($x['servicesEyebrow']) ?></p><h2><?= h($x['servicesTitle']) ?></h2></div><ul class="feature-list three-cols"><?php foreach($x['items'] as $item): ?><li>✓ <?= h($item) ?></li><?php endforeach; ?></ul></div></section>
<?php }

function render_contact_page(string $locale,array $d): void { $x=$d['contact']; ?>
<section class="section"><div class="container"><div class="split-card contact-hero"><div class="hero-copy"><p class="eyebrow"><?= h($x['eyebrow']) ?></p><h1><?= h($x['title']) ?></h1><p class="lead"><?= h($x['description']) ?></p><div class="contact-lines"><strong><?= h(SITE_ADDRESS) ?></strong><span><?= h(SITE_PHONE) ?></span><span><?= h(SITE_EMAIL) ?></span></div><div class="schedule"><p><?= h($d['common']['schedule']) ?></p><strong><?= h($d['common']['weekdays']) ?></strong><span>09:00–14:00 · 16:00–19:00</span></div><div class="actions"><?php render_whatsapp_button($d['common']['whatsapp'],'APP Informática Menorca - '.$x['eyebrow']); ?><a class="btn btn-outline" href="<?= h(MAPS_DIRECTIONS_URL) ?>" target="_blank" rel="noopener noreferrer"><?= h($d['common']['directions']) ?></a></div></div><div class="hero-image fade-left tall"><img src="<?= h(image_url('contacto/contacto-app.png')) ?>" alt="<?= h($x['imageAlt']) ?>"></div></div></div></section>
<?php }

function render_catalog_page(string $locale,array $d): void { $x=$d['catalog']; ?>
<section class="section page-narrow"><div class="container narrow"><p class="eyebrow muted"><?= h($x['eyebrow']) ?></p><h1 class="simple-title"><?= h($x['title']) ?></h1><p class="lead simple-lead"><?= h($x['description']) ?></p><a class="btn btn-dark" href="<?= h(ONLINE_STORE_URL) ?>" target="_blank" rel="noopener noreferrer"><?= h($x['button']) ?></a></div></section>
<?php }

function render_app(string $locale, string $page='home'): void {
    if (!supported_locale($locale)) { http_response_code(404); $locale='es'; }
    $d=dict($locale);
    $title=current_page_title($d,$page) . ' | ' . SITE_NAME;
    ?><!doctype html><html lang="<?= h($locale) ?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#13948d"><title><?= h($title) ?></title><link rel="icon" href="<?= h(image_url('brand/app-logo.png')) ?>"><link rel="stylesheet" href="<?= h(base_url('assets/css/app.css')) ?>?v=1"></head><body data-page="<?= h($page) ?>" data-locale="<?= h($locale) ?>"><div id="nav-progress"></div><div class="sticky-shell"><?php render_topbar($locale,$d); render_header($locale,$d,$page); ?></div><main class="page-enter"><?php
    match($page) {
      'servicio-tecnico' => render_service_page($locale,$d),
      'patinetes' => render_scooters_page($locale,$d),
      'fibra-movil' => render_telecom_page($locale,$d),
      'empresas' => render_business_page($locale,$d),
      'contacto' => render_contact_page($locale,$d),
      'tienda' => render_catalog_page($locale,$d),
      default => render_home($locale,$d),
    };
    ?></main><?php render_footer($d); ?><script src="<?= h(base_url('assets/js/app.js')) ?>?v=1" defer></script></body></html><?php
}
