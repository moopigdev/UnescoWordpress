Komet web fonts (paid licence) — waiting for the owner's files (D6 / P-205).

Put the web-licensed files here with exactly these names (woff2 preferred, woff optional):

  Komet-Light.woff2     -> CSS font-weight 300  (Figma "Komet-Light",   listed as 400)
  Komet-Regular.woff2   -> CSS font-weight 400  (Figma "Komet-Regular", listed as 500)
  Komet-Medium.woff2    -> CSS font-weight 500  (Figma "Komet-Medium",  listed as 600)
  Komet-Bold.woff2      -> CSS font-weight 700  (Figma "Komet-Bold",    listed as 700)
  Komet-Heavy.woff2     -> CSS font-weight 800  (Figma "Komet-Heavy",   listed as 800)

inc/fonts.php prints an @font-face rule only for files that exist, so nothing 404s while a style is
missing. No code change is needed when the files arrive. Do not load Komet from a CDN.
