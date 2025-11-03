import { Component } from '@angular/core';
import { ThemeService } from '../../shared/services/theme-service';

@Component({
  selector: 'app-footer',
  imports: [],
  templateUrl: './footer.html',
  styleUrl: './footer.css',
})
export class Footer {
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
}
