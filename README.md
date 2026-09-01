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

cPanel clones GitHub into `/home/atozkptt/repositories/atozee.agency`. That folder is not the live website. `.cpanel.yml` copies the site into `public_html`.

1. Clone the GitHub repo in **Git Version Control** and check out branch `main`. If **Update from Remote** shows `"" is not a valid "branch"`, no branch is checked out — remove the cPanel repository entry and clone again, choosing `main`.
2. Click **Update from Remote**.
3. Click **Deploy HEAD Commit** to copy files into `/home/atozkptt/public_html`.
4. In File Manager, set `public_html/data/` and `public_html/uploads/agencies/` to `755` or `775`.

Live listings stay on the server. Deploy does not overwrite `data/content.json`, `data/settings.json`, or uploaded images. The first visit copies starter content from `data/content.seed.json`.

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
