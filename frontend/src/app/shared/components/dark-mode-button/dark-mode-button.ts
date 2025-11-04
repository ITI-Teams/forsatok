import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { StorageService } from '../../services/storage-service';

@Component({
  selector: 'app-dark-mode-button',
  imports: [CommonModule],
  templateUrl: './dark-mode-button.html',
  styleUrl: './dark-mode-button.css',
})
export class DarkModeButton {
  isDark = false;

  constructor(private storage: StorageService) { }

  ngOnInit() {
    const savedTheme = this.storage.getItem<string>('theme') || 'light';
    this.isDark = savedTheme === 'dark';
    this.applyTheme();
  }

  toggleTheme() {
    this.isDark = !this.isDark;
    this.storage.setItem('theme', this.isDark ? 'dark' : 'light');
    this.applyTheme();
  }

  private applyTheme() {
    const html = document.documentElement;
    if (this.isDark) {
      html.classList.add('my-app-dark');
    } else {
      html.classList.remove('my-app-dark');
    }
  }
}
