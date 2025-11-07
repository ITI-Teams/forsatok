import { Component, HostListener, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, Router } from '@angular/router';
import { ThemeService } from '../../shared/services/theme-service';
import { AuthService } from '../../core/services/auth.service';
import { take } from 'rxjs/operators';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [CommonModule, RouterLink,],
  templateUrl: './header.html',
  styleUrls: ['./header.css']
})
export class Header implements OnInit{
  mobileMenuOpen = false;
  megaMenuOpen = false;
  openMegaId: string | null = null;
  openDropdown: string | null = null;
  isLoggedIn = false;
  hasNotifications = false;

  constructor(
    private themeService: ThemeService,
    private router: Router,
    private auth: AuthService
  ) {}

  ngOnInit() {
    this.isLoggedIn = this.auth.hasToken();

    this.auth.isLoggedIn$().subscribe(status => {
      this.isLoggedIn = status;
    });
  }

  logout() {
    this.auth.logout();
    this.isLoggedIn = false;
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

  // helper for icons
  get userIcon() {
    return 'assets/images/user-placeholder.svg'; // default icon
  }
}
