import './bootstrap';
import '../css/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';


const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY || 'DEFAULT_APP_KEY';
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'DEFAULT_APP_CLUSTER';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: pusherKey,
    cluster: pusherCluster,
    forceTLS: true
});

document.addEventListener('DOMContentLoaded', () => {
    const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
    if (!userId) return;

    window.Echo.private(`App.Domains.Users.Models.User.${userId}`)
    .listen('.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', (e) => {
        if (window.livewire) {
            window.livewire.emit('notificationReceived', e.notification);
        }
    });
});
