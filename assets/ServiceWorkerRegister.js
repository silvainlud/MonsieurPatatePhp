export async function registerServiceWorker()
{
    const registration = await navigator.serviceWorker.register("/sw.js");
    let subscription = await registration.pushManager.getSubscription();

    await registration.update()

    // L'utilisateur n'est pas déjà abonné, on l'abonne au notification push
    if (!subscription) {
        subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: await getPublicKey(),
        });
    }

    await saveSubscription(subscription);
}

async function getPublicKey()
{
    const {key} = await fetch("/push/key", {
        headers: {
            Accept: "application/json",
        },
    }).then((r) => r.json());
    return key;
}

/**
 * @param {PushSubscription} subscription
 * @returns {Promise<void>}
 */
async function saveSubscription(subscription)
{
    await fetch("/push/subscribe", {
        method: "post",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify(subscription.toJSON()),
    });
}