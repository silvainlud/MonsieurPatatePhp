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

self.addEventListener("notificationclick", (event) => {
    event.notification.close();
    event.waitUntil(openUrl("https://mpatate.silvain.eu/"));
});

/**
 * Ouvre l'url ou focus la page qui est déjà ouverte sur cette URL
 * @param {string} url
 **/
async function openUrl(url)
{
    const windowClients = await self.clients.matchAll({
        type: "window",
        includeUncontrolled: true,
    });
    for (let i = 0; i < windowClients.length; i++) {
        const client = windowClients[i];
        if (client.url === url && "focus" in client) {
            return client.focus();
        }
    }
    if (self.clients.openWindow) {
        return self.clients.openWindow(url);
    }
    return null;
}