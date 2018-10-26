// Service worker for /blogs V5.

// We need the cacheKey accessible across all event listeners so we can read and save to it on the fly.
const cacheKey = 'bbc-blogs-v5-' + makeCacheKey();

// Self === window.self === window because lovely javascript.
// See: https://stackoverflow.com/questions/16875767/difference-between-this-and-self-in-javascript
// Basically this adds a one-time function that is called WHEN FIRST INSTALLED, and thus does not run on every request
// The old version of the service worker is still running at this point so we cannot delete the old cache
// or do anything else that might interefere with it.
self.addEventListener('install', (event) => {
    // event.waitUntil extends the lifetime of the install event
    // until the passed promise resolves successfully. If the promise rejects,
    // the installation is considered a failure and this service worker is abandoned.
    // If an older version is running, it stays active.
    event.waitUntil(
        caches.open(cacheKey).then( (cache) => {
            return cache.addAll([
                'https://sandbox.bbc.co.uk:446/assets/css/main.css',
                'https://sandbox.bbc.co.uk:446/blogs/offline'
            ]);
        })
    );
    console.log('Cached all correctly using cache key - ' + cacheKey);
});

// Activate event is when the service worker is activated after being installed (Which is once all pages using
// old version of the SW are closed (if updating, otherwise it's instantly after install for first time)
// Here is where you would look for and delete any old caches to avoid messing up the currently-running workers cache.
//
// During activation, other events such as fetch are put into a queue,
// so a long activation could potentially block page loads.
// Keep your activation as lean as possible, only using it for things you
// couldn't do while the old version was active.
self.addEventListener('activate', (event) => {
    console.log('Worker activated! Cache key is: ' + cacheKey);

    // Loop through all existing caches and delete them!
    caches.keys().then(function (names) {
        console.log('Going to delete old caches now!');
        console.log('Found a total of... ' + names.length);
        for (let name of names) {
            // We don't want to delete our current cache!
            if (name != cacheKey) {
                console.log('Deleting... ' + name);
                caches.delete(name);
            } else {
                console.log('Not deleting the following cache...' + name);
            }
        }
        console.log('All done with deleting old caches!');
    });
    // We need to expire old caches here
});

// This is the fetch  event. Every request passes through here. In this event we check if it's already cached.
// If it is, we return it from the cache, if it isn't, we return it and then cache it.
// Investigation is needed into if this is the best practice, and as to wether or not this will result in stale data.
// Maybe we need to expire the cache or update it in the background.
// We may need to change our caching strategy to a "Cache then network" approach.
// Due to stupid garbage collection we are only allowed to read the response/request once. .clone() is used to fix this.
// We also need to be sure we don't use too much storage. The only limit is the angry user wondering why all their disk
// space has gone to "BBC Blogs". There is no storage limit by browsers.
self.addEventListener('fetch', function (event) {
    console.log('Fetch event fired!');
    event.respondWith(
        caches.open(cacheKey).then(function (cache) {
            return cache.match(event.request).then(function (response) {
                return response || fetch(event.request).then(function (response) {
                    cache.put(event.request, response.clone());
                    return response;
                });
            }).catch(() => {
                return caches.match('/offline');
            });
        })
    );
});

function makeCacheKey() {
    let text = "";
    let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 10; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }

    return text;
}
