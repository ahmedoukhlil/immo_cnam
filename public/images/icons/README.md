# CNAM PWA Icons

## Fichiers générés
- `icon-{size}x{size}.png` — icônes PNG pour toutes les tailles standards
- `favicon.ico` — favicon multi-résolution (16/32/48px)
- `manifest.json` — Web App Manifest prêt à l'emploi

## Intégration HTML (dans <head>)

```html
<!-- Favicon -->
<link rel="icon" href="/icons/favicon.ico" sizes="any">
<link rel="icon" href="/icons/icon-32x32.png" type="image/png">

<!-- Apple Touch Icon (iOS) -->
<link rel="apple-touch-icon" href="/icons/icon-180x180.png">

<!-- Web App Manifest -->
<link rel="manifest" href="/manifest.json">

<!-- Theme color -->
<meta name="theme-color" content="#d63384">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="CNAM">
```

## Arborescence recommandée
```
public/
├── icons/
│   ├── icon-192x192.png
│   ├── icon-512x512.png
│   └── ...
├── favicon.ico
└── manifest.json
```
