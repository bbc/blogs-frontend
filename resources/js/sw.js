console.log('Service worker loaded correctly');

// Self === window.self === window because lovely javascript.
// See: https://stackoverflow.com/questions/16875767/difference-between-this-and-self-in-javascript
// Basically this adds a one-time function that is called WHEN FIRST INSTALLED, and thus does not run on every request
self.addEventListener('install', () => {
    console.log('Attempting to install Workbox...');
    // Import a development framework for service workers from Google to make general life easier
    importScripts('https://storage.googleapis.com/workbox-cdn/releases/3.4.1/workbox-sw.js');

    if (workbox) {
        console.log('Workbox is loaded 🎉');
    } else {
        console.log('Workbox didn\'t load :c');
    }
});

self.addEventListener('fetch', () => {
    console.log('You navigated to a page! Well done on being a good internet user!');
});

