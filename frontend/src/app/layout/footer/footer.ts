import { Component, HostListener } from '@angular/core';
import { ThemeService } from '../../shared/services/theme-service';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-footer',
  imports: [CommonModule, RouterLink],
  templateUrl: './footer.html',
  styleUrls: ['./footer.css'],
})
export class Footer {
  scrollPercent = 0;
  isAtTop = true;

  constructor(private themeService: ThemeService) { }

  get isDark() {
    return this.themeService.isDarkMode();
  }

  get logoSrc() {
    return this.isDark ? '/images/jobhub-logo-white.png' : '/images/jobhub-logo-black.png';
  }

  scrollToTop(): void {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  @HostListener('window:scroll', [])
  onWindowScroll() {
    const scrollTop = window.scrollY || document.documentElement.scrollTop;
    const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    this.scrollPercent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    this.isAtTop = scrollTop === 0;
  }
}
