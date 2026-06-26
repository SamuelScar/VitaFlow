import { createRequire } from 'node:module';

const require = createRequire(import.meta.url);
const { ChartJSNodeCanvas } = require('chartjs-node-canvas');

function readStdin() {
    return new Promise((resolve, reject) => {
        let input = '';

        process.stdin.setEncoding('utf8');
        process.stdin.on('data', (chunk) => {
            input += chunk;
        });
        process.stdin.on('end', () => resolve(input));
        process.stdin.on('error', reject);
    });
}

try {
    const payload = JSON.parse(await readStdin());
    const renderer = new ChartJSNodeCanvas({
        width: payload.width || 1000,
        height: payload.height || 360,
        backgroundColour: 'white',
    });

    const buffer = await renderer.renderToBuffer(payload.configuration, 'image/png');
    process.stdout.write(buffer);
} catch (error) {
    process.stderr.write(`${error.message}\n`);
    process.exit(1);
}