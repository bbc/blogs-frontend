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
        setupWorkbox(true);
    } else {
        console.log('Workbox didn\'t load :c');
        setupWorkbox(false);
    }
});

// Fetch event is fired on every page navigation or refresh, here is where you would get the
// cached files and return them to the browser
self.addEventListener('fetch', (event) => {
    console.log('You navigated to a page! Well done on being a good internet user!');
});

// Activate event is when the service worker is activated after being installed (Which is once all pages using
// old version of the SW are closed (if updating, otherwise it's instantly after install for first time)
// Here is where you would look for and delete any old caches to avoid messing up the currently-running workers cache.
self.addEventListener('activate', (event) => {
    console.log('Service worker.... activated! o7');
});

function setupWorkbox(wb_exists) {
    if (wb_exists) {
        console.log('Ran it because WB Exists...');
    } else {
        console.log('Nope! No workbox.');
    }
    // Register a route in workbox for all Javascript files
    workbox.routing.registerRoute(
        new RegExp('.*\.js'),
        workbox.strategies.networkFirst()
    );
}

