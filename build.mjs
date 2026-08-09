import { cp, mkdir, rm } from 'node:fs/promises';

const staticFiles = [
  'index.html',
  'style.css',
  'app.js',
  'VICTORIA_LOGO.svg',
  'sakura_bg.png',
  'program_self_funded.png',
  'program_scholarship.png',
  'program_tokutei.png'
];

await rm('dist', { recursive: true, force: true });
await mkdir('dist/client', { recursive: true });
await mkdir('dist/server', { recursive: true });

await Promise.all(staticFiles.map((file) => cp(file, `dist/client/${file}`)));
await cp('worker.js', 'dist/server/index.js');

console.log(`Built ${staticFiles.length} static assets and the hosting worker.`);
