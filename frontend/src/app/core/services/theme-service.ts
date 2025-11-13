import { Injectable, Renderer2, RendererFactory2 } from '@angular/core';
import { StorageService } from './storage-service';

@Injectable({
  providedIn: 'root'
})
export class ThemeService {
  private renderer: Renderer2;
  private readonly darkClass = 'my-app-dark';
  private readonly themeKey = 'theme';
  private htmlElement = document.documentElement;

  constructor(
    private rendererFactory: RendererFactory2,
    private storage: StorageService
  ) {
    this.renderer = this.rendererFactory.createRenderer(null, null);
    this.loadTheme();
  }

  // ✅ Toggle between dark/light
  toggleTheme(): void {
    if (this.isDarkMode()) {
      this.disableDarkMode();
    } else {
      this.enableDarkMode();
    }
  }

  // ✅ Enable dark mode
  enableDarkMode(): void {
    this.renderer.addClass(this.htmlElement, this.darkClass);
    this.storage.setItem(this.themeKey, 'dark');
  }

  // ✅ Disable dark mode
  disableDarkMode(): void {
    this.renderer.removeClass(this.htmlElement, this.darkClass);
    this.storage.setItem(this.themeKey, 'light');
  }

  // ✅ Check current mode
  isDarkMode(): boolean {
    return this.htmlElement.classList.contains(this.darkClass);
  }

  // ✅ Load saved theme from storage
  private loadTheme(): void {
    const savedTheme = this.storage.getItem<string>(this.themeKey);
    if (savedTheme === 'dark') {
      this.enableDarkMode();
    } else {
      this.disableDarkMode();
    }
  }
}
