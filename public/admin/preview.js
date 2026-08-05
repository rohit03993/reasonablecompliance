/* global CMS, createClass, h */

function listToArray(list) {
  if (!list) return [];
  if (typeof list.toJS === 'function') return list.toJS();
  if (Array.isArray(list)) return list;
  return [];
}

function get(entry, path) {
  return entry.getIn(['data'].concat(path));
}

function str(value) {
  if (value == null) return '';
  return String(value);
}

var HomepagePreview = createClass({
  render: function () {
    var entry = this.props.entry;
    var headline = get(entry, ['hero', 'headline']);
    var subheadline = get(entry, ['hero', 'subheadline']);
    var intro = get(entry, ['hero', 'intro']);
    var trustTitle = get(entry, ['trustTitle']);
    var trustBullets = listToArray(get(entry, ['trustBullets']));
    var whyTitle = get(entry, ['whyChooseTitle']);
    var whyBullets = listToArray(get(entry, ['whyChooseBullets']));
    var industriesTitle = get(entry, ['industriesTitle']);
    var industriesIntro = get(entry, ['industriesIntro']);
    var industries = listToArray(get(entry, ['industries']));
    var processTitle = get(entry, ['processTitle']);
    var processSteps = listToArray(get(entry, ['processSteps']));
    var ctaHeadline = get(entry, ['finalCta', 'headline']);
    var ctaText = get(entry, ['finalCta', 'text']);

    return h('div', { className: 'preview' }, [
      h('section', { className: 'preview-hero', key: 'hero' }, [
        h('p', { className: 'preview-brand', key: 'brand' }, 'Reasonable Compliance'),
        h('h1', { key: 'h' }, str(headline)),
        h('p', { className: 'sub', key: 's' }, str(subheadline)),
        h('p', { className: 'intro', key: 'i' }, str(intro)),
        h('p', { className: 'preview-tagline', key: 't' }, 'Trusted Compliance. Clear Advice.'),
        h('div', { className: 'preview-ctas', key: 'c' }, [
          h('span', { className: 'btn btn-primary', key: 'b1' }, 'Get a Free Consultation'),
          h('span', { className: 'btn btn-secondary', key: 'b2' }, 'Contact Us'),
        ]),
      ]),
      h('section', { className: 'preview-section alt', key: 'trust' }, [
        h('p', { className: 'eyebrow', key: 'e1' }, 'Trust'),
        h('h2', { key: 'h2' }, str(trustTitle)),
        h(
          'ul',
          { className: 'check-list', key: 'ul' },
          trustBullets.map(function (item, idx) {
            return h('li', { key: 'tb' + idx }, str(item));
          })
        ),
      ]),
      h('section', { className: 'preview-section', key: 'why' }, [
        h('p', { className: 'eyebrow', key: 'e2' }, 'Partnership'),
        h('h2', { key: 'h3' }, str(whyTitle)),
        h(
          'ul',
          { className: 'check-list', key: 'ul2' },
          whyBullets.map(function (item, idx) {
            return h('li', { key: 'wb' + idx }, str(item));
          })
        ),
      ]),
      h('section', { className: 'preview-section alt', key: 'ind' }, [
        h('p', { className: 'eyebrow', key: 'e3' }, 'Industries'),
        h('h2', { key: 'h4' }, str(industriesTitle)),
        h('p', { key: 'p' }, str(industriesIntro)),
        h(
          'div',
          { className: 'chips', key: 'chips' },
          industries.map(function (item, idx) {
            return h('span', { className: 'chip', key: 'ch' + idx }, str(item));
          })
        ),
      ]),
      h('section', { className: 'preview-section navy', key: 'proc' }, [
        h('p', { className: 'eyebrow', key: 'e4' }, 'How we work'),
        h('h2', { key: 'h5' }, str(processTitle)),
        h(
          'div',
          { className: 'steps', key: 'steps' },
          processSteps.map(function (step, idx) {
            var title = step.title || (step.get && step.get('title')) || '';
            var description = step.description || (step.get && step.get('description')) || '';
            return h('div', { className: 'step', key: 'st' + idx }, [
              h('div', { className: 'step-num', key: 'n' }, String(idx + 1).padStart(2, '0')),
              h('h3', { key: 't' }, str(title)),
              h('p', { key: 'd' }, str(description)),
            ]);
          })
        ),
      ]),
      h('section', { className: 'preview-section', key: 'cta' }, [
        h('h2', { key: 'h6' }, str(ctaHeadline)),
        h('p', { key: 'pt' }, str(ctaText)),
        h('span', { className: 'btn btn-primary', key: 'btn' }, 'Book Your Consultation Today'),
      ]),
    ]);
  },
});

var AboutPreview = createClass({
  render: function () {
    var entry = this.props.entry;
    var title = get(entry, ['title']);
    var paragraphs = listToArray(get(entry, ['paragraphs']));
    var whyTitle = get(entry, ['whyChooseTitle']);
    var whyBullets = listToArray(get(entry, ['whyChooseBullets']));

    return h('div', { className: 'preview' }, [
      h('section', { className: 'preview-hero', key: 'hero' }, [
        h('p', { className: 'eyebrow', key: 'e' }, 'About'),
        h('h1', { key: 'h' }, str(title)),
      ]),
      h(
        'section',
        { className: 'preview-section', key: 'body' },
        paragraphs.map(function (p, idx) {
          return h('p', { key: 'p' + idx, style: { color: '#1e293b', marginBottom: '14px' } }, str(p));
        })
      ),
      h('section', { className: 'preview-section alt', key: 'why' }, [
        h('h2', { key: 'h2' }, str(whyTitle)),
        h(
          'ul',
          { className: 'check-list', key: 'ul' },
          whyBullets.map(function (item, idx) {
            return h('li', { key: 'w' + idx }, str(item));
          })
        ),
      ]),
    ]);
  },
});

var ContactPreview = createClass({
  render: function () {
    var entry = this.props.entry;
    return h('div', { className: 'preview' }, [
      h('section', { className: 'preview-hero', key: 'hero' }, [
        h('p', { className: 'eyebrow', key: 'e' }, 'Contact'),
        h('h1', { key: 'h' }, str(get(entry, ['headline']))),
        h('p', { className: 'sub', key: 's' }, str(get(entry, ['supportingText']))),
        h('span', { className: 'btn btn-primary', key: 'b' }, 'Send message'),
      ]),
    ]);
  },
});

var SitePreview = createClass({
  render: function () {
    var entry = this.props.entry;
    var siteName = str(get(entry, ['siteName']));
    var tagline = str(get(entry, ['tagline']));
    var logoSrc = str(get(entry, ['logo']));
    var email = str(get(entry, ['email']));
    var phone = str(get(entry, ['phone']));
    var whatsapp = str(get(entry, ['whatsapp']));
    var ctaPrimary = str(get(entry, ['ctaPrimary']));

    return h('div', { className: 'preview' }, [
      h('div', { className: 'settings-card', key: 'card' }, [
        logoSrc
          ? h('img', { src: logoSrc, alt: siteName, key: 'logo' })
          : h('div', { className: 'preview-brand', key: 'brand', style: { color: '#0a2540' } }, siteName),
        h('p', { className: 'muted', key: 'tag' }, tagline),
        h('div', { className: 'settings-row', key: 'e' }, [
          h('span', { key: 'l' }, 'Email'),
          h('span', { key: 'v' }, email),
        ]),
        h('div', { className: 'settings-row', key: 'p' }, [
          h('span', { key: 'l' }, 'Phone'),
          h('span', { key: 'v' }, phone),
        ]),
        h('div', { className: 'settings-row', key: 'w' }, [
          h('span', { key: 'l' }, 'WhatsApp'),
          h('span', { key: 'v' }, whatsapp),
        ]),
        h('div', { style: { marginTop: '18px' }, key: 'cta' }, [
          h('span', { className: 'btn btn-primary', key: 'b' }, ctaPrimary || 'Primary CTA'),
        ]),
      ]),
    ]);
  },
});

var ServicesPreview = createClass({
  render: function () {
    var entry = this.props.entry;
    var items = listToArray(get(entry, ['items']));

    return h('div', { className: 'preview' }, [
      h('section', { className: 'preview-section', key: 'sec' }, [
        h('p', { className: 'eyebrow', key: 'e' }, 'Services'),
        h('h2', { key: 'h' }, 'All services'),
      ].concat(
        items.map(function (item, idx) {
          var title = item.title || (item.get && item.get('title')) || '';
          var summary = item.summary || (item.get && item.get('summary')) || '';
          var bullets = listToArray(item.bullets || (item.get && item.get('bullets')));
          return h('div', { className: 'service-card', key: 'svc' + idx }, [
            h('h3', { key: 't' }, str(title)),
            h('p', { className: 'muted', key: 's' }, str(summary)),
            h(
              'ul',
              { className: 'check-list', key: 'ul' },
              bullets.map(function (b, i) {
                return h('li', { key: 'b' + i }, str(b));
              })
            ),
          ]);
        })
      )),
    ]);
  },
});

var FaqsPreview = createClass({
  render: function () {
    var entry = this.props.entry;
    var items = listToArray(get(entry, ['items']));

    return h('div', { className: 'preview' }, [
      h('section', { className: 'preview-section', key: 'sec' }, [
        h('p', { className: 'eyebrow', key: 'e' }, 'FAQ'),
        h('h2', { key: 'h' }, 'Frequently asked questions'),
      ].concat(
        items.map(function (item, idx) {
          var q = item.question || (item.get && item.get('question')) || '';
          var a = item.answer || (item.get && item.get('answer')) || '';
          return h('div', { className: 'faq-item', key: 'f' + idx }, [
            h('strong', { key: 'q' }, str(q)),
            h('p', { key: 'a' }, str(a)),
          ]);
        })
      )),
    ]);
  },
});

CMS.registerPreviewStyle('/admin/preview.css');
CMS.registerPreviewTemplate('homepage', HomepagePreview);
CMS.registerPreviewTemplate('about', AboutPreview);
CMS.registerPreviewTemplate('contact', ContactPreview);
CMS.registerPreviewTemplate('site', SitePreview);
CMS.registerPreviewTemplate('services', ServicesPreview);
CMS.registerPreviewTemplate('faqs', FaqsPreview);
CMS.init();
