console.log('Service worker loaded correctly');

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

    setupWorkbox();
});

// Fetch event is fired on every page navigation or refresh, here is where you would get the
// cached files and return them to the browser
// self.addEventListener('fetch', (event) => {
//     console.log('You navigated to a page! Well done on being a good internet user!');
// });
// This is commented out for now as workbox attaches an event listener to it too, and you can only attach it once...

// Activate event is when the service worker is activated after being installed (Which is once all pages using
// old version of the SW are closed (if updating, otherwise it's instantly after install for first time)
// Here is where you would look for and delete any old caches to avoid messing up the currently-running workers cache.
self.addEventListener('activate', (event) => {
    console.log('Service worker.... activated! o7');
});

function setupWorkbox() {
    workbox.routing.registerRoute(
        // Cache all JS
        new RegExp('.*\.js'),
        // Use cache but update in the background ASAP
        workbox.strategies.staleWhileRevalidate({
            // Use a custom cache name
            cacheName: 'js-cache',
        })
    );
    workbox.routing.registerRoute(
        // Cache all CSS
        /.*\.css/,
        // Use cache but update in the background ASAP
        workbox.strategies.staleWhileRevalidate({
            // Use a custom cache name
            cacheName: 'css-cache',
        })
    );
    workbox.routing.registerRoute(
        // Cache all Images
        /.*\.(?:png|jpg|jpeg|svg|gif)/,
        // Use cache but update in the background ASAP
        workbox.strategies.staleWhileRevalidate({
            // Use a custom cache name
            cacheName: 'image-cache',
            plugins: [
                new workbox.expiration.Plugin({
                    // Cache only 20 images
                    maxEntries: 20,
                    // Cache for a maximum of a week
                    maxAgeSeconds: 7 * 24 * 60 * 60,
                })
            ],
        })
    );
}
