# Client handoff — Reasonable Compliance

## Host on Hostinger

1. Get a free access key from https://web3forms.com (use `reasonablecompliance@gmail.com`)
2. Paste it in `src/data/site.json` → `web3formsAccessKey`
3. Build:
```bash
npm run build
```
4. Hostinger **File Manager** → open `public_html`
5. Upload **everything inside** the `dist` folder
6. Open your domain and test Contact → Send message  
   Emails arrive in `reasonablecompliance@gmail.com`

## Contact form → email

Uses **Web3Forms** (works on Hostinger).  
Submissions go to the Gmail used when creating the access key.

## Admin (`/admin`)

On Hostinger, Decap admin login is limited (no Netlify Identity).  
For content changes: edit JSON in `src/data/`, run `npm run build`, re-upload `dist`.

Local admin (developer PC only):

```bash
npm run dev
npm run decap
```

Then open `http://localhost:4321/admin/index.html`

## Before launch checklist

- [ ] Add Web3Forms access key
- [ ] Replace phone / WhatsApp in `src/data/site.json`
- [ ] Logo is set (`/images/logo.png`)
- [ ] Rebuild and upload `dist` to Hostinger `public_html`
- [ ] Test contact form on live domain

## Commands

```bash
npm install
npm run dev
npm run build
npm run preview
```
