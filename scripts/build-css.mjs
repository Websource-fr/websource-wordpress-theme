#!/usr/bin/env node
/**
 * Per-page CSS build of Websource Theme.
 *
 *   npm run build   -> assets/css/theme.css (base) + assets/css/pages/<type>.css + assets/css/editor.css
 *   npm run watch   -> rebuilds when a source, a template or the configuration changes
 *
 * How it works:
 *  1. Tailwind v4 generates utilities by scanning ONLY the templates of the page (scripts/pages.config.mjs)
 *     and compiles the component sources of that page (assets/src/…, @apply resolved).
 *  2. Everything already in theme.css is removed from the page files (deduplication), as well as the theme
 *     variables (--color-brand-*…), which must stay driven by theme.css and the `websource_theme_tokens` filter.
 */
import { execFileSync } from 'node:child_process';
import { existsSync, mkdirSync, readdirSync, readFileSync, rmSync, statSync, writeFileSync, watch } from 'node:fs';
import { dirname, join, relative, resolve, sep } from 'node:path';
import { fileURLToPath } from 'node:url';
import { gzipSync } from 'node:zlib';
import postcss from 'postcss';
import { base, pages } from './pages.config.mjs';

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const SRC = join(ROOT, 'assets', 'src');
const OUT = join(ROOT, 'assets', 'css');
const OUT_PAGES = join(OUT, 'pages');
// Work folder INSIDE the theme: @import "tailwindcss" must resolve through node_modules.
const WORK = join(ROOT, 'node_modules', '.cache', 'wsx-css');
const TAILWIND = join(ROOT, 'node_modules', '.bin', 'tailwindcss');

const posix = (p) => p.split(sep).join('/');
const rel = (from, to) => posix(relative(from, to));
const read = (f) => readFileSync(f, 'utf8');

function tailwind(inputFile, outputFile) {
  execFileSync(TAILWIND, ['-i', inputFile, '-o', outputFile, '--minify'], { cwd: ROOT, stdio: ['ignore', 'pipe', 'pipe'] });
}

/** Generates then compiles the Tailwind entry of a page (or of the base). */
function compile(label, def, isBase, warnings) {
  const entry = join(WORK, `${label}.entry.css`);
  const out = join(WORK, `${label}.tw.css`);
  let tokens = `@import "${rel(WORK, join(SRC, 'tokens.css'))}";`;
  if (isBase) {
    // In the base file every token is emitted (static); in a page only the ones in use (then removed).
    writeFileSync(join(WORK, 'tokens.static.css'), read(join(SRC, 'tokens.css')).replace('@theme {', '@theme static {'));
    tokens = '@import "./tokens.static.css";';
  }
  const sources = [];
  for (const t of def.templates) {
    const file = join(ROOT, t);
    if (!existsSync(file)) { warnings.add(`template not found: ${t}`); continue; }
    sources.push(`@source "${rel(WORK, file)}";`);
  }
  const imports = [];
  for (const c of def.css) {
    const file = join(SRC, c);
    if (!existsSync(file)) { warnings.add(`CSS source not found: ${c}`); continue; }
    // The component files are imported OUTSIDE any cascade layer: WordPress core, WooCommerce and plugins ship
    // unlayered CSS (global styles of theme.json, blocks, select2…), which would otherwise always win over the theme.
    // Only Tailwind's theme / preflight / utilities and the reset of base.css are layered.
    imports.push(`@import "${rel(WORK, file)}";`);
  }
  writeFileSync(entry, [
    '@layer theme, base, components, utilities;',
    '@import "tailwindcss/theme.css" layer(theme);',
    isBase ? '@import "tailwindcss/preflight.css" layer(base);' : '',
    '@import "tailwindcss/utilities.css" layer(utilities) source(none);',
    tokens,
    ...imports,
    ...sources,
    // WooCommerce class names that are also Tailwind utilities (grid-column) and must not be generated.
    '@source not inline("{col-1,col-2}");',
  ].join('\n'));
  tailwind(entry, out);
  return read(out);
}

const hasChildRules = (n) => n.nodes?.some((c) => c.type === 'rule' || c.type === 'atrule');

/** Signatures (context + node) of every “leaf” node of a stylesheet. */
function signatures(root) {
  const set = new Set();
  const visit = (container, ctx) => {
    container.each((n) => {
      if (n.type === 'comment') return;
      if (n.type === 'atrule' && hasChildRules(n)) visit(n, `${ctx}@${n.name} ${n.params}{`);
      else set.add(ctx + n.toString());
    });
  };
  visit(root, '');
  return set;
}

/** Removes from `root` the nodes already present in `known`. */
function removeKnown(root, known) {
  const visit = (container, ctx) => {
    for (const n of [...container.nodes]) {
      if (n.type === 'comment') continue;
      if (n.type === 'atrule' && hasChildRules(n)) {
        visit(n, `${ctx}@${n.name} ${n.params}{`);
        if (!n.nodes.length) n.remove();
      } else if (known.has(ctx + n.toString())) {
        n.remove();
      }
    }
  };
  visit(root, '');
}

/** Theme variables already provided by theme.css (or belonging to the tokens): removed from the pages. */
function stripThemeVars(root, baseRoot, tokenNames) {
  const baseVars = new Map();
  baseRoot.walkAtRules('layer', (layer) => {
    if (layer.params !== 'theme') return;
    layer.walkDecls((d) => baseVars.set(d.prop, d.value));
  });
  root.walkAtRules('layer', (layer) => {
    if (layer.params !== 'theme') return;
    layer.walkDecls((d) => {
      if (tokenNames.has(d.prop) || baseVars.get(d.prop) === d.value) d.remove();
    });
    layer.walkRules((r) => { if (!r.nodes.length) r.remove(); });
    if (!layer.nodes.length) layer.remove();
  });
}

const fmt = (b) => `${(b / 1024).toFixed(1).padStart(6)} KB`;
const gz = (s) => gzipSync(Buffer.from(s)).length;

async function build() {
  const t0 = Date.now();
  rmSync(WORK, { recursive: true, force: true });
  mkdirSync(WORK, { recursive: true });
  mkdirSync(OUT_PAGES, { recursive: true });
  // Remove page files that are no longer configured.
  for (const f of readdirSync(OUT_PAGES)) {
    if (f.endsWith('.css') && !(f.slice(0, -4) in pages)) rmSync(join(OUT_PAGES, f));
  }

  const warnings = new Set();
  const tokenNames = new Set([...read(join(SRC, 'tokens.css')).matchAll(/(--[\w-]+)\s*:/g)].map((m) => m[1]));

  // --- theme.css (base)
  const baseCss = compile('base', base, true, warnings);
  writeFileSync(join(OUT, 'theme.css'), baseCss);
  const baseRoot = postcss.parse(baseCss);
  const known = signatures(baseRoot);
  const rows = [{ name: 'theme.css', own: baseCss.length, raw: baseCss }];

  // --- editor.css: what the block editor needs on top of theme.css (kept tiny)
  const editorCss = '.editor-styles-wrapper{font-family:var(--font-sans);color:var(--color-ink);background:var(--color-page)}'
    + '.editor-styles-wrapper :is(h1,h2,h3,h4,h5,h6){font-family:var(--font-head)}'
    + '.editor-styles-wrapper .wp-block{max-width:52rem}.editor-styles-wrapper .wp-block[data-align=wide]{max-width:80rem}.editor-styles-wrapper .wp-block[data-align=full]{max-width:none}';
  writeFileSync(join(OUT, 'editor.css'), editorCss + '\n');

  // --- pages
  for (const [name, def] of Object.entries(pages)) {
    const root = postcss.parse(compile(name, def, false, warnings));
    stripThemeVars(root, baseRoot, tokenNames);
    removeKnown(root, known);
    root.walkComments((c) => c.remove());
    // Drop the layers left empty by the deduplication.
    root.walkAtRules('layer', (layer) => { if (layer.nodes && !layer.nodes.length) layer.remove(); });
    const css = root.toString().replace(/\n+/g, '');
    writeFileSync(join(OUT_PAGES, `${name}.css`), css + '\n');
    rows.push({ name: `pages/${name}.css`, own: css.length, raw: css });
  }

  rmSync(WORK, { recursive: true, force: true });

  // --- report
  const baseSize = rows[0].own;
  console.log('\nCSS per page (loaded = theme.css + page file)\n');
  console.log('file'.padEnd(26), 'own'.padStart(10), 'gzip'.padStart(10), 'loaded'.padStart(10), 'gzip'.padStart(10));
  for (const r of rows) {
    const loaded = r.name === 'theme.css' ? r.own : r.own + baseSize;
    const loadedGz = r.name === 'theme.css' ? gz(r.raw) : gz(r.raw) + gz(rows[0].raw);
    console.log(r.name.padEnd(26), fmt(r.own).padStart(10), fmt(gz(r.raw)).padStart(10), fmt(loaded).padStart(10), fmt(loadedGz).padStart(10));
  }
  for (const w of warnings) console.warn(`  ! ${w}`);
  console.log(`\nDone in ${((Date.now() - t0) / 1000).toFixed(1)} s\n`);
}

if (process.argv.includes('--watch')) {
  let timer = null;
  const run = () => build().catch((e) => console.error(e.stderr?.toString() || e));
  const trigger = () => { clearTimeout(timer); timer = setTimeout(run, 250); };
  await run();
  for (const dir of [SRC, join(ROOT, 'template-parts'), join(ROOT, 'woocommerce'), join(ROOT, 'src'), join(ROOT, 'scripts')]) {
    if (existsSync(dir) && statSync(dir).isDirectory()) watch(dir, { recursive: true }, trigger);
  }
  console.log('Watching (Ctrl+C to stop)…');
} else {
  await build().catch((e) => {
    console.error(e.stderr?.toString() || e);
    process.exit(1);
  });
}
