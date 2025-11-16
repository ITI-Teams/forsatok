import { Component, OnInit } from '@angular/core';
import { CommonModule, DatePipe } from '@angular/common';
import { NotificationsService, Notification } from '../../core/services/notifications.service';

@Component({
  selector: 'app-notifications',
  standalone: true,
  imports: [
    CommonModule,
    DatePipe
  ],
  templateUrl: './notifications.html',
  styleUrls: ['./notifications.css']
})
export class Notifications implements OnInit {
  notifications: Notification[] = [];

  constructor(public notifService: NotificationsService) {}

  ngOnInit() {
    this.notifService.notifications$.subscribe(notifs => this.notifications = notifs);
  }

  markAsRead(id: string) {
    this.notifService.markAsRead(id);
  }

  markAllRead() {
    this.notifService.markAllRead();
  }
  get unreadCount(): number {
    return this.notifications.filter(n => !n.read_at).length;
  }
}
