import { Component, HostListener, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, Router } from '@angular/router';
import { ThemeService } from '../../core/services/theme-service';
import { AuthService } from '../../core/services/auth.service';
import { NotificationsService } from '../../core/services/notifications.service';
import {environment} from '../../environments/environment';

@Component({
  selector: 'app-header-private',
  standalone: true,
  imports: [CommonModule, RouterLink,],
  templateUrl: './header-private.html',
  styleUrls: ['./header-private.css']
})
export class HeaderPrivate implements OnInit {
  mobileMenuOpen = false;
  megaMenuOpen = false;
  openMegaId: string | null = null;
  openDropdown: string | null = null;
  isLoggedIn = false;
  currentUser: any = null;
  notifications: any[] = [];
  hasNotifications = false;

  constructor(
    private themeService: ThemeService,
    private router: Router,
    private auth: AuthService,
    private notify: NotificationsService
  ) { }

  ngOnInit() {
    this.isLoggedIn = this.auth.hasToken();
    if (this.isLoggedIn) {
      this.loadUserData();
      this.subscribeNotifications();
    }
    this.auth.isLoggedIn$().subscribe(status => {
      this.isLoggedIn = status;
      if (status) {
        this.loadUserData();
        this.subscribeNotifications();
      } else {
        this.currentUser = null;
        this.notifications = [];
      }
    });
  }
  private subscribeNotifications() {
    if (!this.isLoggedIn) return;
    this.notify.notifications$.subscribe(list => {
      this.notifications = list;
      this.hasNotifications = list.some(n => !n.read_at);
    });
  }
  private checkAuthStatus() {
    this.isLoggedIn = this.auth.hasToken();
    if (this.isLoggedIn) {
      this.loadUserData();
      this.subscribeNotifications();
    }
  }
  private loadUserData() {
    const userData = this.auth.getUser();
    if (userData) {
      this.currentUser = userData;
    }
  }

  getUserAvatar(): string {
    if (!this.currentUser?.avatar) {
      return '/images/avatars/avatar.svg';
    }

    if (this.currentUser.avatar.startsWith('http')) {
      return this.currentUser.avatar;
    }

    return `${environment.imageUrl}/storage/${this.currentUser.avatar}`;
  }
  getUserName(): string {
    return this.currentUser?.name || 'User';
  }
  getUserEmail(): string {
    return this.currentUser?.email || 'user@example.com';
  }

  logout() {
    this.auth.logout();
    this.isLoggedIn = false;
    this.currentUser = null;
    this.router.navigate(['/login']);
  }

  isActive(route: string): boolean {
    return this.router.url === route || this.router.url === route + '/' || this.router.url.startsWith(route + '/');
  }

  toggleMobileMenu() {
    this.mobileMenuOpen = !this.mobileMenuOpen;
    if (this.mobileMenuOpen) {
      this.megaMenuOpen = false;
      this.openMegaId = null;
      this.openDropdown = null;
    }
  }

  closeMobileMenu() { this.mobileMenuOpen = false; }

  toggleDropdown(id: string) { this.openDropdown = this.openDropdown === id ? null : id; }

  toggleMegaMenu(id: string) { this.openMegaId = this.openMegaId === id ? null : id; }

  closeAll() {
    this.mobileMenuOpen = false;
    this.openMegaId = null;
    this.megaMenuOpen = false;
    this.openDropdown = null;
  }

  toggleDarkMode() { this.themeService.toggleTheme(); }

  get isDark() { return this.themeService.isDarkMode(); }

  get logoSrc() { return this.isDark ? '/images/logo2.png' : '/images/logo1.png'; }

  @HostListener('document:keydown.escape') onEsc() { this.closeAll(); }

  @HostListener('document:click', ['$event'])
  onDocClick(e: MouseEvent) {
    const target = e.target as HTMLElement;
    if (target.closest('.site-header')) return;
    this.closeAll();
  }

  @HostListener('window:resize') onResize() {
    if (window.innerWidth >= 1024) this.closeMobileMenu();
  }
  markAsRead(id: string) {
    this.notify.markAsRead(id);
  }

  markAllAsRead() {
    this.notify.markAllRead();
  }
}
