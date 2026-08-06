const fs = require('fs');
const path = 'public/data/blog.json';
let raw = fs.readFileSync(path, 'utf8');
// Remove accidental literal \n after closing brace
raw = raw.replace(/\}\s*\\n\s*$/, '}\n').replace(/\}\n\\n\s*$/, '}\n');
if (raw.trimEnd().endsWith('}\\n')) {
  raw = raw.trimEnd().slice(0, -2);
}
raw = raw.replace(/}\n$/, '}').trimEnd();
if (!raw.endsWith('}')) {
  const idx = raw.lastIndexOf('}');
  raw = raw.slice(0, idx + 1);
}

const data = JSON.parse(raw);

function toHtml(body) {
  if (!body) return '';
  let text = String(body);
  // unwrap bad single <p>...</p> that still has plain newlines inside
  if (/^<p>[\s\S]*<\/p>$/i.test(text.trim()) && text.includes('\n\n')) {
    text = text.trim().replace(/^<p>/i, '').replace(/<\/p>$/i, '');
  }
  if (/<[a-z][\s\S]*>/i.test(text) && /<\/[a-z]+>/i.test(text) && !text.includes('\n\n')) {
    return text;
  }
  if (/<[a-z][\s\S]*>/i.test(text) && /<\/[a-z]+>/i.test(text) && !/^<p>[\s\S]*\n\n/i.test(text)) {
    return text;
  }

  return text
    .split(/\n\s*\n/)
    .map((block) => block.trim())
    .filter(Boolean)
    .map((block) => {
      const lines = block.split('\n');
      const isList = lines.every((l) => !l.trim() || l.trim().startsWith('- '));
      if (isList) {
        return (
          '<ul>' +
          lines
            .filter((l) => l.trim().startsWith('- '))
            .map((l) => '<li>' + l.replace(/^\s*-\s*/, '') + '</li>')
            .join('') +
          '</ul>'
        );
      }
      if (lines.length === 1 && lines[0].length < 80 && !/[.!?]$/.test(lines[0]) && !/^\d+\./.test(lines[0])) {
        return '<h2>' + lines[0] + '</h2>';
      }
      return '<p>' + lines.join('<br>') + '</p>';
    })
    .join('\n');
}

data.items = data.items.map((item) => ({
  ...item,
  image: item.image || '/images/logo.png',
  body: toHtml(item.body),
}));

fs.writeFileSync(path, JSON.stringify(data, null, 2) + '\n');
JSON.parse(fs.readFileSync(path, 'utf8'));
console.log('OK posts=', data.items.length);
