import { Injectable, Renderer2, RendererFactory2 } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class ThemeService {
  private renderer: Renderer2;
  private darkClass = 'my-app-dark';
  private htmlElement = document.documentElement;

  constructor(private rendererFactory: RendererFactory2) {
    this.renderer = this.rendererFactory.createRenderer(null, null);
    this.loadTheme();
  }

  toggleTheme(): void {
    if (this.isDarkMode()) {
      this.disableDarkMode();
    } else {
      this.enableDarkMode();
    }
  }

  enableDarkMode(): void {
    this.renderer.addClass(this.htmlElement, this.darkClass);
    localStorage.setItem('theme', 'dark');
  }

  disableDarkMode(): void {
    this.renderer.removeClass(this.htmlElement, this.darkClass);
    localStorage.setItem('theme', 'light');
  }

  isDarkMode(): boolean {
    return this.htmlElement.classList.contains(this.darkClass);
  }

  private loadTheme(): void {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
      this.enableDarkMode();
    } else {
      this.disableDarkMode();
    }
  }
}
