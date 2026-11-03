// Simple high-performance in-memory cache with TTL (Time To Live)
class MemoryCache {
    constructor() {
        this.cache = new Map();
    }

    get(key) {
        const item = this.cache.get(key);
        if (!item) return null;
        if (Date.now() > item.expiry) {
            this.cache.delete(key);
            return null;
        }
        return item.value;
    }

    set(key, value, ttlSeconds = 60) {
        this.cache.set(key, {
            value,
            expiry: Date.now() + (ttlSeconds * 1000)
        });
    }

    del(key) {
        this.cache.delete(key);
    }

    flush() {
        this.cache.clear();
    }
}

const memoryCache = new MemoryCache();

function cacheMiddleware(ttlSeconds = 60) {
    return (req, res, next) => {
        // Only cache GET requests
        if (req.method !== 'GET') {
            return next();
        }

        const key = req.originalUrl || req.url;
        const cached = memoryCache.get(key);

        if (cached) {
            res.setHeader('X-Cache', 'HIT');
            return res.json(cached);
        }

        // Intercept res.json to store response in cache
        const originalJson = res.json.bind(res);
        res.json = (body) => {
            if (res.statusCode >= 200 && res.statusCode < 300) {
                memoryCache.set(key, body, ttlSeconds);
                res.setHeader('X-Cache', 'MISS');
            }
            return originalJson(body);
        };

        next();
    };
}

module.exports = {
    memoryCache,
    cacheMiddleware
};
