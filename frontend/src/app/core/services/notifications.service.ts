import { Injectable, NgZone } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import Pusher from 'pusher-js';
import { BehaviorSubject } from 'rxjs';

export interface Notification {
  id: string;
  type: string;
  data: any;
  read_at: string | null;
  created_at: string;
}

@Injectable({
  providedIn: 'root'
})
export class NotificationsService {
  private notificationsSubject = new BehaviorSubject<Notification[]>([]);
  notifications$ = this.notificationsSubject.asObservable();

  // ⬅⬅ هنا عرفنا ngZone
  constructor(private http: HttpClient, private ngZone: NgZone) {
    this.initPusher();
    this.loadNotifications();
  }

  loadNotifications() {
    this.http.get<{ data: Notification[] }>(`${environment.apiUrl}/notifications`)
      .subscribe(res => {
        this.notificationsSubject.next(res.data);
      });
  }

  markAsRead(id: string) {
    this.http.post(`${environment.apiUrl}/notifications/${id}/read`, {}).subscribe(() => {
      const updated = this.notificationsSubject.value.map(n =>
        n.id === id ? { ...n, read_at: new Date().toISOString() } : n
      );
      this.notificationsSubject.next(updated);
    });
  }

  markAllRead() {
    this.http.post(`${environment.apiUrl}/notifications/read-all`, {}).subscribe(() => {
      const updated = this.notificationsSubject.value.map(n =>
        ({ ...n, read_at: new Date().toISOString() })
      );
      this.notificationsSubject.next(updated);
    });
  }

  private initPusher() {
    const storedUser = localStorage.getItem('user');
    if (!storedUser) return;

    const user = JSON.parse(storedUser);
    const userId = user.id;

    const pusher = new Pusher(environment.pusherKey, {
      cluster: environment.pusherCluster,
      forceTLS: true,
      authEndpoint: environment.apiUrl + '/broadcasting/auth',
      auth: {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      }
    });

    // IMPORTANT — Your Laravel channel
    const channel = pusher.subscribe(`private-App.Domains.Users.Models.User.${userId}`);

    channel.bind(
      'Illuminate\\Notifications\\Events\\BroadcastNotificationCreated',
      (data: any) => {
        this.ngZone.run(() => {
          const current = [data.notification, ...this.notificationsSubject.value];
          this.notificationsSubject.next(current);
        });
      }
    );
  }
}
