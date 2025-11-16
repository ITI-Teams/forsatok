import { NgClass, NgFor, NgIf, CommonModule, DatePipe } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { HomeService } from '../../core/services/home.service';
import { ContactService } from '../../core/services/main-contact.service';
import { NotificationsService, Notification } from '../../core/services/notifications.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-candidate-dashboard',
  standalone: true,
  imports: [NgClass, NgFor, NgIf, CommonModule, DatePipe],
  templateUrl: './candidate-dashboard.html'
})
export class CandidateDashboard implements OnInit {
  activeTab: 'messages' | 'notifications' | 'savedJobs' = 'messages';

  candidateData: {
    messages: any[];
    notifications: Notification[];
    savedJobs: any[];
  } = {
    messages: [],
    notifications: [],
    savedJobs: []
  };

  currentPageMessages: number = 1;
  currentPageNotifications: number = 1;
  currentPageSaved: number = 1;
  itemsPerPage: number = 5;

  private notificationsSub?: Subscription;

  constructor(
    private homeService: HomeService,
    private contactService: ContactService,
    private notificationsService: NotificationsService
  ) {}

  ngOnInit() {
    this.loadMessages();
    this.subscribeNotifications();
  }

  ngOnDestroy() {
    this.notificationsSub?.unsubscribe();
  }

  setTab(tab: 'messages' | 'notifications' | 'savedJobs') {
    this.activeTab = tab;

    if (tab === 'messages') this.loadMessages();
    else if (tab === 'savedJobs') this.loadSavedJobs();
  }

  // Messages
  loadMessages() {
    this.contactService.getMessages().subscribe({
      next: (res: any) => {
        this.candidateData.messages = Array.isArray(res?.data?.data) ? res.data.data : [];
        this.currentPageMessages = 1;
      },
      error: err => {
        console.error('Error fetching messages:', err);
        this.candidateData.messages = [];
      }
    });
  }

  // Notifications
  private subscribeNotifications() {
    this.notificationsSub = this.notificationsService.notifications$.subscribe(
      (notifications: Notification[]) => {
        this.candidateData.notifications = Array.isArray(notifications) ? notifications : [];
        this.currentPageNotifications = 1;
      }
    );
  }

  // Saved jobs
  loadSavedJobs() {
    this.homeService.getSavedJobs().subscribe({
      next: (res: any) => {
        // Safe assignment with fallback to empty array
        this.candidateData.savedJobs = Array.isArray(res?.data) ? res.data : [];
        this.currentPageSaved = 1;
      },
      error: err => {
        console.error('Error fetching saved jobs:', err);
        this.candidateData.savedJobs = []; // Ensure it's always an array
      }
    });
  }

  // Safe pagination getters with array checks
  get paginationMessages() {
    const messages = Array.isArray(this.candidateData.messages) ? this.candidateData.messages : [];
    const start = (this.currentPageMessages - 1) * this.itemsPerPage;
    return messages.slice(start, start + this.itemsPerPage);
  }

  get paginationNotifications() {
    const notifications = Array.isArray(this.candidateData.notifications) ? this.candidateData.notifications : [];
    const start = (this.currentPageNotifications - 1) * this.itemsPerPage;
    return notifications.slice(start, start + this.itemsPerPage);
  }

  get paginationSavedJobs() {
    const savedJobs = Array.isArray(this.candidateData.savedJobs) ? this.candidateData.savedJobs : [];
    const start = (this.currentPageSaved - 1) * this.itemsPerPage;
    return savedJobs.slice(start, start + this.itemsPerPage);
  }

  nextPage(tab: 'messages' | 'notifications' | 'savedJobs') {
    if (tab === 'messages' && this.currentPageMessages < this.totalPages('messages')) this.currentPageMessages++;
    else if (tab === 'notifications' && this.currentPageNotifications < this.totalPages('notifications')) this.currentPageNotifications++;
    else if (tab === 'savedJobs' && this.currentPageSaved < this.totalPages('savedJobs')) this.currentPageSaved++;
  }

  prevPage(tab: 'messages' | 'notifications' | 'savedJobs') {
    if (tab === 'messages' && this.currentPageMessages > 1) this.currentPageMessages--;
    else if (tab === 'notifications' && this.currentPageNotifications > 1) this.currentPageNotifications--;
    else if (tab === 'savedJobs' && this.currentPageSaved > 1) this.currentPageSaved--;
  }

  totalPages(tab: 'messages' | 'notifications' | 'savedJobs') {
    let items: any[] = [];

    if (tab === 'messages') items = Array.isArray(this.candidateData.messages) ? this.candidateData.messages : [];
    else if (tab === 'notifications') items = Array.isArray(this.candidateData.notifications) ? this.candidateData.notifications : [];
    else if (tab === 'savedJobs') items = Array.isArray(this.candidateData.savedJobs) ? this.candidateData.savedJobs : [];

    return Math.ceil(items.length / this.itemsPerPage) || 1;
  }
}
