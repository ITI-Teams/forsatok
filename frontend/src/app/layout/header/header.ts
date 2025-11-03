import { Component, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { ThemeService } from '../../shared/services/theme-service';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [CommonModule, RouterLink, RouterLinkActive],
  templateUrl: './header.html',
  styleUrls: ['./header.css']
})
export class Header {
  mobileMenuOpen = false;
  megaMenuOpen = false;
  openMegaId: string | null = null;
  openDropdown: string | null = null;

  isLoggedIn = true;
  hasNotifications = false;

  constructor(private themeService: ThemeService) { }

  toggleMobileMenu() {
    this.mobileMenuOpen = !this.mobileMenuOpen;
    if (this.mobileMenuOpen) {
      this.megaMenuOpen = false;
      this.openMegaId = null;
      this.openDropdown = null;
    }
  }

  closeMobileMenu() {
    this.mobileMenuOpen = false;
  }

  toggleDropdown(id: string) {
    this.openDropdown = this.openDropdown === id ? null : id;
  }

  toggleMegaMenu(id: string) {
    this.openMegaId = this.openMegaId === id ? null : id;
  }

  closeAll() {
    this.mobileMenuOpen = false;
    this.openMegaId = null;
    this.megaMenuOpen = false;
    this.openDropdown = null;
  }

  toggleDarkMode() {
    this.themeService.toggleTheme();
  }

  get isDark() {
    return this.themeService.isDarkMode();
  }

  get logoSrc() {
    return this.isDark ? '/images/jobhub-logo-white.png' : '/images/jobhub-logo-black.png';
  }

  logout() {
    console.log('User logged out');
    this.isLoggedIn = false;
    this.openDropdown = null;
  }

  // close menus on ESC
  @HostListener('document:keydown.escape')
  onEsc() {
    this.closeAll();
  }

  // close menus on click outside
  @HostListener('document:click', ['$event'])
  onDocClick(e: MouseEvent) {
    const target = e.target as HTMLElement;
    if (target.closest('.site-header')) return;
    this.closeAll();
  }

  // close mobile menu if window resized
  @HostListener('window:resize')
  onResize() {
    if (window.innerWidth >= 1024) {
      this.closeMobileMenu();
    }
  }

  // helper for icons
  get userIcon() {
    return 'assets/images/user-placeholder.svg'; // default icon
  }
}
