<!-- Meta tag -->
<meta name="keywords" content="<?= $kpf_config["seo"]["keywords"] ?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="language" content="<?= $kpf_config["seo"]["lang"] ?>">
<meta name="author" content="<?= $kpf_config["seo"]["author"] ?>">
<meta http-equiv="content-language" content="<?= $kpf_config["seo"]["lang_short"] ?>">

<!-- METADATA -->
<meta name="title" content="<?= $kpf_config["seo"]["title"] ?>" />
<meta name="description" content="<?= $kpf_config["seo"]["description"] ?>" />

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website" />
<meta property="og:url" content="<?= $kpf_urlHTTP . $kpf_urlHOST ?>" />
<meta property="og:title" content="<?= $kpf_config["seo"]["title"] ?>" />
<meta property="og:description" content="<?= $kpf_config["seo"]["description"] ?>" />
<meta property="og:image" content="<?= $kpf_config["seo"]["image"] ?>" />

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image" />
<meta property="twitter:url" content="<?= $kpf_urlHTTP . $kpf_urlHOST ?>" />
<meta property="twitter:title" content="<?= $kpf_config["seo"]["title"] ?>" />
<meta property="twitter:description" content="<?= $kpf_config["seo"]["description"] ?>" />
<meta property="twitter:image" content="<?= $kpf_config["seo"]["image"] ?>" />

<!-- Theme-color -->
<meta name="theme-color" content="<?= $kpf_config["seo"]["color"] ?>" />

<!-- favicon -->
<link rel="shortcut icon" href="<?= $kpf_config["seo"]["favicon"] ?>" type="image/x-icon">
<link rel="apple-touch-icon" href="<?= $kpf_config["seo"]["favicon"] ?>">

<!-- PWA -->
<!-- <link rel="manifest" href="manifest.json">
<script>
    //if browser support service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('sw.js');
    };
</script> -->