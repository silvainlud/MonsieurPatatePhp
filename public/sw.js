self.addEventListener('install', async() => {
    console.log('[Service Worker] Installation');
    await self.skipWaiting();
});

self.addEventListener("push", (event) => {
    const data = event.data ? event.data.json() : {};
    event.waitUntil(self.registration.showNotification(data.title, data));
});

self.addEventListener('fetch', event => {
    // event.respondWith(
    //     caches.match(event.request).then(response => {
    //         return response || fetch(event.request);
    //     })
    // );
});