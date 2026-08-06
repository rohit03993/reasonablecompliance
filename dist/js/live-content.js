(function () {
  function getByPath(root, path) {
    return path.split('.').reduce(function (acc, key) {
      if (acc == null) return undefined;
      return acc[key];
    }, root);
  }

  function setText(selectorPath, value) {
    if (value == null) return;
    document.querySelectorAll('[data-cms="' + selectorPath + '"]').forEach(function (el) {
      el.textContent = String(value);
    });
  }

  function setList(selectorPath, items, itemClass) {
    if (!Array.isArray(items)) return;
    document.querySelectorAll('[data-cms-list="' + selectorPath + '"]').forEach(function (el) {
      el.innerHTML = items
        .map(function (item) {
          return '<li class="' + (itemClass || '') + '"><span class="mt-1.5 inline-block h-2 w-2 shrink-0 rounded-full bg-emerald" aria-hidden="true"></span><span>' + escapeHtml(String(item)) + '</span></li>';
        })
        .join('');
    });
  }

  function setChips(selectorPath, items) {
    if (!Array.isArray(items)) return;
    document.querySelectorAll('[data-cms-list="' + selectorPath + '"]').forEach(function (el) {
      el.innerHTML = items
        .map(function (item) {
          return '<li class="rounded-full border border-navy/15 bg-white px-4 py-2 text-sm font-medium text-navy">' + escapeHtml(String(item)) + '</li>';
        })
        .join('');
    });
  }

  function escapeHtml(str) {
    return str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  async function boot() {
    try {
      const [site, homepage, about, contact, faqs, blog, testimonials] = await Promise.all([
        fetch('/data/site.json', { cache: 'no-store' }).then(function (r) { return r.json(); }),
        fetch('/data/homepage.json', { cache: 'no-store' }).then(function (r) { return r.json(); }),
        fetch('/data/about.json', { cache: 'no-store' }).then(function (r) { return r.json(); }),
        fetch('/data/contact.json', { cache: 'no-store' }).then(function (r) { return r.json(); }),
        fetch('/data/faqs.json', { cache: 'no-store' }).then(function (r) { return r.json(); }),
        fetch('/data/blog.json', { cache: 'no-store' }).then(function (r) { return r.json(); }).catch(function () { return null; }),
        fetch('/data/testimonials.json', { cache: 'no-store' }).then(function (r) { return r.json(); }).catch(function () { return null; }),
      ]);

      window.__RC_CONTENT__ = { site: site, homepage: homepage, about: about, contact: contact, faqs: faqs, blog: blog, testimonials: testimonials };

      setText('site.siteName', site.siteName);
      setText('site.tagline', site.tagline);
      setText('site.email', site.email);
      setText('site.ctaPrimary', site.ctaPrimary);
      setText('site.ctaSecondary', site.ctaSecondary);
      setText('site.footerBlurb', site.footerBlurb);

      document.querySelectorAll('[data-cms-href="site.email"]').forEach(function (el) {
        if (site.email) el.setAttribute('href', 'mailto:' + site.email);
      });

      if (homepage && homepage.hero) {
        setText('homepage.hero.headline', homepage.hero.headline);
        setText('homepage.hero.subheadline', homepage.hero.subheadline);
        setText('homepage.hero.intro', homepage.hero.intro);
      }
      setText('homepage.trustTitle', homepage.trustTitle);
      setText('homepage.whyChooseTitle', homepage.whyChooseTitle);
      setText('homepage.industriesTitle', homepage.industriesTitle);
      setText('homepage.industriesIntro', homepage.industriesIntro);
      setText('homepage.processTitle', homepage.processTitle);
      setText('homepage.finalCta.headline', getByPath(homepage, 'finalCta.headline'));
      setText('homepage.finalCta.text', getByPath(homepage, 'finalCta.text'));

      setList('homepage.trustBullets', homepage.trustBullets, 'flex gap-3 text-sm leading-relaxed sm:text-base');
      setList('homepage.whyChooseBullets', homepage.whyChooseBullets, 'flex gap-3 text-sm leading-relaxed sm:text-base');
      setChips('homepage.industries', homepage.industries);

      if (Array.isArray(homepage.processSteps)) {
        document.querySelectorAll('[data-cms-process]').forEach(function (wrap) {
          wrap.innerHTML = homepage.processSteps
            .map(function (step, index) {
              var num = String(index + 1).padStart(2, '0');
              return (
                '<li class="reveal border-t border-white/15 pt-6 is-visible">' +
                '<p class="font-display text-3xl font-bold text-emerald">' + num + '</p>' +
                '<h3 class="mt-3 text-lg font-semibold text-white">' + escapeHtml(step.title || '') + '</h3>' +
                '<p class="mt-2 text-sm leading-relaxed text-white/70">' + escapeHtml(step.description || '') + '</p>' +
                '</li>'
              );
            })
            .join('');
        });
      }

      if (about) {
        setText('about.title', about.title);
        setList('about.whyChooseBullets', about.whyChooseBullets, 'flex gap-3 text-sm leading-relaxed sm:text-base');
        setText('about.whyChooseTitle', about.whyChooseTitle);
        if (Array.isArray(about.paragraphs)) {
          document.querySelectorAll('[data-cms-about-paragraphs]').forEach(function (wrap) {
            wrap.innerHTML = about.paragraphs
              .map(function (p) {
                return '<p class="reveal is-visible">' + escapeHtml(p) + '</p>';
              })
              .join('');
          });
        }
      }

      if (contact) {
        setText('contact.headline', contact.headline);
        setText('contact.supportingText', contact.supportingText);
      }

      if (faqs && Array.isArray(faqs.items)) {
        document.querySelectorAll('[data-cms-faqs]').forEach(function (wrap) {
          wrap.innerHTML = faqs.items
            .map(function (faq, index) {
              return (
                '<details class="faq-item group reveal py-1 is-visible"' + (index === 0 ? ' open' : '') + '>' +
                '<summary class="flex min-h-14 cursor-pointer list-none items-center justify-between gap-4 py-4 text-left font-semibold text-navy marker:content-none [&::-webkit-details-marker]:hidden">' +
                '<span>' + escapeHtml(faq.question || '') + '</span>' +
                '<span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-navy/15 text-emerald transition group-open:rotate-45" aria-hidden="true">+</span>' +
                '</summary>' +
                '<p class="pb-5 pr-10 text-sm leading-relaxed text-muted sm:text-base">' + escapeHtml(faq.answer || '') + '</p>' +
                '</details>'
              );
            })
            .join('');
        });
      }

      if (blog) {
        setText('blog.title', blog.title);
        setText('blog.intro', blog.intro);
        if (Array.isArray(blog.items)) {
          var posts = blog.items.slice().sort(function (a, b) {
            return String(b.date || '').localeCompare(String(a.date || ''));
          }).slice(0, 3);
          document.querySelectorAll('[data-cms-blog-teasers]').forEach(function (wrap) {
            wrap.innerHTML = posts
              .map(function (post) {
                var href = '/blog/read?slug=' + encodeURIComponent(post.slug || '');
                var dateLabel = '';
                try {
                  dateLabel = new Date((post.date || '') + 'T00:00:00').toLocaleDateString('en-IN', {
                    day: 'numeric', month: 'short', year: 'numeric'
                  });
                } catch (e) {
                  dateLabel = post.date || '';
                }
                return (
                  '<li class="reveal is-visible group flex flex-col rounded-2xl border border-navy/10 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-emerald/30 hover:shadow-[0_16px_40px_rgba(10,37,64,0.08)]">' +
                  '<p class="text-xs font-semibold uppercase tracking-wider text-emerald">' + escapeHtml(dateLabel) + '</p>' +
                  '<h3 class="mt-3 text-lg font-semibold text-navy transition group-hover:text-emerald"><a href="' + href + '">' + escapeHtml(post.title || '') + '</a></h3>' +
                  '<p class="mt-3 flex-1 text-sm leading-relaxed text-muted">' + escapeHtml(post.excerpt || '') + '</p>' +
                  '<a href="' + href + '" class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-emerald transition group-hover:gap-2">Read more <span aria-hidden="true">→</span></a>' +
                  '</li>'
                );
              })
              .join('');
          });
        }
      }

      if (testimonials) {
        setText('testimonials.title', testimonials.title);
        if (Array.isArray(testimonials.items)) {
          document.querySelectorAll('[data-cms-testimonials]').forEach(function (wrap) {
            wrap.innerHTML = testimonials.items
              .map(function (item) {
                return (
                  '<li class="reveal is-visible group rounded-2xl border border-navy/10 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-emerald/30 hover:shadow-[0_16px_40px_rgba(10,37,64,0.08)]">' +
                  '<p class="text-sm leading-relaxed text-muted sm:text-base">“' + escapeHtml(item.quote || '') + '”</p>' +
                  '<p class="mt-5 text-sm font-semibold text-navy">' + escapeHtml(item.name || '') + '</p>' +
                  '<p class="text-xs text-muted">' + escapeHtml(item.role || '') + '</p>' +
                  '</li>'
                );
              })
              .join('');
          });
        }
      }

      // WhatsApp / phone links for floating buttons
      document.querySelectorAll('[data-cms-whatsapp]').forEach(function (el) {
        if (site.whatsapp && String(site.whatsapp).indexOf('X') === -1) {
          el.setAttribute('href', 'https://wa.me/' + String(site.whatsapp).replace(/\D/g, ''));
          el.setAttribute('target', '_blank');
          el.setAttribute('rel', 'noopener noreferrer');
        }
      });
      document.querySelectorAll('[data-cms-phone]').forEach(function (el) {
        if (site.phone && String(site.phone).indexOf('X') === -1) {
          el.setAttribute('href', 'tel:' + String(site.phone).replace(/\s/g, ''));
        }
      });
    } catch (e) {
      // Keep build-time content if live JSON is unavailable
      console.warn('Live content not applied', e);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
