import fs from 'fs';

const files = ['blog', 'services', 'faqs', 'testimonials', 'homepage', 'about', 'contact', 'gallery', 'site', 'social'];
const out = {};
for (const f of files) {
  out[f] = JSON.parse(fs.readFileSync(`public/data/${f}.json`, 'utf8'));
}

function phpString(s) {
  return "'" + String(s).replace(/\\/g, '\\\\').replace(/'/g, "\\'") + "'";
}

function jsonToPhp(v, indent = 0) {
  const pad = '  '.repeat(indent);
  const pad2 = '  '.repeat(indent + 1);
  if (v === null) return 'null';
  if (typeof v === 'boolean') return v ? 'true' : 'false';
  if (typeof v === 'number') return String(v);
  if (typeof v === 'string') return phpString(v);
  if (Array.isArray(v)) {
    if (!v.length) return '[]';
    return '[\n' + v.map((x) => pad2 + jsonToPhp(x, indent + 1)).join(',\n') + '\n' + pad + ']';
  }
  const keys = Object.keys(v);
  if (!keys.length) return '[]';
  return (
    '[\n' +
    keys.map((k) => pad2 + phpString(k) + ' => ' + jsonToPhp(v[k], indent + 1)).join(',\n') +
    '\n' +
    pad +
    ']'
  );
}

const php = '<?php\n/** Packaged site content — used by Repair / Reload in admin */\nreturn ' + jsonToPhp(out) + ';\n';
fs.writeFileSync('public/rc-panel/seed-data.php', php);
console.log('ok', {
  bytes: fs.statSync('public/rc-panel/seed-data.php').size,
  blog: out.blog.items.length,
  services: out.services.items.length,
  faqs: out.faqs.items.length,
  testimonials: out.testimonials.items.length,
});
