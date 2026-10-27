Komet web fonts (paid licence) — files delivered (D19, 2026-10-27; web licence confirmed by the owner).

Self-hosted woff2, converted from the owner's OTF (originals: Unesco/design/fonts/, outside the repo):

  Komet-Light.woff2     -> CSS font-weight 300  (Figma "Komet-Light",   listed as 400)
  Komet-Regular.woff2   -> CSS font-weight 400  (Figma "Komet-Regular", listed as 500)
  Komet-Medium.woff2    -> CSS font-weight 500  (Figma "Komet-Medium",  listed as 600)
  Komet-Bold.woff2      -> CSS font-weight 700  (Figma "Komet-Bold",    listed as 700)
  Komet-Heavy.woff2     -> CSS font-weight 800  (Figma "Komet-Heavy",   listed as 800)

inc/fonts.php prints an @font-face rule for each file that exists (no code change needed).
Do not load Komet from a CDN. Do not commit the original OTF files.
