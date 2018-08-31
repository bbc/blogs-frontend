// Service worker for /blogs V5.

// Self === window.self === window because lovely javascript.
// See: https://stackoverflow.com/questions/16875767/difference-between-this-and-self-in-javascript
// Basically this adds a one-time function that is called WHEN FIRST INSTALLED, and thus does not run on every request
self.addEventListener('install', (event) => {
    console.log('Attempting to install Workbox...');
    // Import a development framework for service workers from Google to make general life easier
    importScripts('https://storage.googleapis.com/workbox-cdn/releases/3.4.1/workbox-sw.js');

    if (workbox) {
        console.log('Workbox is loaded 🎉');
    } else {
        console.log('Workbox didn\'t load :c');
    }

    event.waitUntil(
        caches.open('bbc-blogs-v5-' + makeCacheKey()).then( (cache) => {
            return cache.addAll([
                'blogs/',
                'blogs/offline'
            ]);
        })
    );
});

// Activate event is when the service worker is activated after being installed (Which is once all pages using
// old version of the SW are closed (if updating, otherwise it's instantly after install for first time)
// Here is where you would look for and delete any old caches to avoid messing up the currently-running workers cache.
self.addEventListener('activate', (event) => {
    // Workbox handles the expiring of old caches for us!
});

function makeCacheKey() {
    let text = "";
    let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 5; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}
