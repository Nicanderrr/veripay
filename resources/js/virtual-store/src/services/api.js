export const api = {
    get(path, options = {}) {
        return request(path, { method: 'GET', ...options });
    },
    post(path, body, options = {}) {
        return request(path, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
            ...options,
        });
    },
};

async function request(path, options = {}) {
    const redirectOnAuth = options.redirectOnAuth !== false;
    delete options.redirectOnAuth;

    const headers = {
        Accept: 'application/json',
        ...(options.headers || {}),
    };

    if (!['GET', 'HEAD'].includes(options.method || 'GET')) {
        await fetch('/sanctum/csrf-cookie', { credentials: 'include' });
        const token = decodeURIComponent(getCookie('XSRF-TOKEN') || '');
        headers['X-XSRF-TOKEN'] = token;
    }

    const response = await fetch(path, {
        credentials: 'include',
        ...options,
        headers,
    });

    if ([401, 403, 419].includes(response.status)) {
        if (redirectOnAuth) window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
        throw new Error('Please sign in to continue.');
    }

    const data = await response.json();
    if (!response.ok) throw new Error(data.message || 'Request failed');
    return data;
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}
