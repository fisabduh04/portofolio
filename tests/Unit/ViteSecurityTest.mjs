import test from 'node:test';
import assert from 'node:assert/strict';
import { loadEnv, resolveConfig } from 'vite';

test('development assets allow the application origin and reject unrelated websites', async () => {
    const config = await resolveConfig({}, 'serve');
    const origins = config.server.cors.origin;
    const allowsOrigin = origin => origins.some(allowed =>
        allowed instanceof RegExp ? allowed.test(origin) : allowed === origin,
    );
    const appUrl = loadEnv('development', process.cwd(), 'APP_URL').APP_URL;

    assert.equal(config.server.host, '127.0.0.1');
    assert.equal(allowsOrigin('http://localhost:8000'), true);
    if (appUrl) {
        assert.equal(allowsOrigin(new URL(appUrl).origin), true);
    }
    assert.equal(allowsOrigin('https://attacker.example'), false);
    assert.equal(allowsOrigin('http://localhost.attacker.example'), false);
});
