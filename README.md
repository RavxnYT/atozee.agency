# AtoZee Agency

Professional sourcing website for [atozee.agency](https://atozee.agency), with a small admin panel for categories, agency names, and images.

Built for Namecheap cPanel + Git. PHP only — no Node process required.

## Public site

- Hero, about, features, partners, stats, and testimonials
- Dynamic partner categories (Coffee Shops, Restaurants, or any category you add)
- Agency cards with name, image, description, and contact actions

## Admin panel

Open `/admin/` (lock icon on the site).

Default login:

- Username: `admin`
- Password: `AtoZee2026!`

Change this password immediately after the first login.

From the admin you can:

- Add, rename, reorder, and delete **categories**
- Add agencies and update **names** and **images** (upload a file or paste a URL)
- Change the admin password

Edits are stored on the server in `data/content.json` and show on the live site right away.

## Deploy on Namecheap cPanel (Git)

1. In cPanel, open **Git Version Control** and pull this repository into `public_html` (or the domain document root).
2. Make sure PHP is enabled (Namecheap shared hosting has this by default).
3. Set write permission on these folders so the admin can save content and images:
   - `data/` → `755` or `775`
   - `uploads/agencies/` → `755` or `775`
4. Visit `https://atozee.agency/` and `https://atozee.agency/admin/`.

Git pull updates the code. It does **not** overwrite live listings, because `data/content.json` and uploaded images are not in Git after first run. The first visit copies starter content from `data/content.seed.json`.

## Local preview

```bash
php -S localhost:8080 router.php
```

Then open http://localhost:8080

## Project layout

```
index.php              Public website
admin/                 Login + dashboard
includes/              Storage, auth, helpers
data/                  JSON content (server-writable)
uploads/agencies/      Uploaded images
assets/                CSS and logo
js/                    Front-end behavior
```
