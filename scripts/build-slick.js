const fs = require('fs');
const { minify } = require('terser');
(async () => {
    const result = await minify(fs.readFileSync('assets/app/libs/slick/slick.js', 'utf8'), { format: { comments: /@license/ } });
    fs.writeFileSync('assets/app/libs/slick/slick.min.js', '/*! Slick 1.8.1 | MIT | Tourfic ignores data-slick configuration */\n' + result.code + '\n');
})().catch(error => { console.error(error); process.exitCode = 1; });
