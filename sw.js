self.addEventListener("install", e => {
    e.waitUntil(
        caches.open("static").then(cache => {
            return cache.addAll(["index.php", "style.css","connect.php","script.js","print.php","word.php","logo512.png", "tfpdf/tfpdf.php","logo192.png"]);
        })
    );    
});

self.addEventListener("fetch", e => {
    e.respondWith(
        caches.match(e.request).then(response => {
            return response || fetch(e.request);
        })
    );
});