# Reasonable Compliance — Hostinger content admin

## Live content login (on server)

URL: `https://reasonablecompliance.com/manage/`

- Username: `admin`
- Password: `Admin@RC2026`

Change password later in `public/manage/config.php` (or on server `public_html/manage/config.php`).

## How it works

1. Log in at `/manage/`
2. Edit Brand, Homepage, About, Contact, Services, FAQs
3. Click Save
4. Refresh the public website — content updates from `/data/*.json`

## Deploy note

When you `git pull` + copy `dist` on Hostinger, **preserve server content edits**:

```bash
cd ~/domains/reasonablecompliance.com
cp -r public_html/data /tmp/rc-data-backup
cd repo
git pull
cp -r dist/. ../public_html/
cp -r /tmp/rc-data-backup/. ../public_html/data/
```

Otherwise a fresh deploy can overwrite content saved in `/manage`.
