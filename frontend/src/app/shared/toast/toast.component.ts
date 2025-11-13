import { Component, inject, Signal, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { toSignal } from '@angular/core/rxjs-interop';
import { ToastService, Toast } from '../../core/services/toast.service';

@Component({
  selector: 'app-toast',
  standalone: true,
  imports: [CommonModule],
  template: `
    @if (toast(); as toastData) {
      <div
        [class]="toastClasses()"
        class="fixed top-4 right-4 z-[9999] max-w-sm w-full p-4 rounded-lg shadow-lg border transform transition-all duration-300 ease-in-out animate-in slide-in-from-right">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <i [class]="getToastIcon(toastData)"></i>
            <span class="text-sm font-medium flex-1">{{ toastData.message }}</span>
          </div>
          <button
            (click)="hide()"
            class="flex-shrink-0 text-gray-500 hover:text-gray-700 transition-colors duration-200 ml-3">
            <i class="fa-solid fa-times"></i>
          </button>
        </div>
      </div>
    }
  `,
  styles: [`
    :host {
      display: block;
      position: fixed;
      top: 0;
      right: 0;
      z-index: 9999;
    }
  `]
})
export class ToastComponent {
  private toastService = inject(ToastService);

  toast = toSignal(this.toastService.toast$);

  toastClasses = computed(() => {
    const toast = this.toast();
    if (!toast) return '';

    const baseClasses = 'fixed top-4 right-4 z-[9999] max-w-sm w-full p-4 rounded-lg shadow-lg border transform transition-all duration-300 ease-in-out';
    const typeClasses = {
      success: 'bg-green-50 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-200 dark:border-green-700',
      error: 'bg-red-50 text-red-800 border-red-200 dark:bg-red-900 dark:text-red-200 dark:border-red-700',
      warning: 'bg-yellow-50 text-yellow-800 border-yellow-200 dark:bg-yellow-900 dark:text-yellow-200 dark:border-yellow-700',
      info: 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:border-blue-700'
    };

    return `${baseClasses} ${typeClasses[toast.type]}`;
  });

  getToastIcon(toast: Toast): string {
    const icons = {
      success: 'fa-solid fa-check-circle text-green-500 flex-shrink-0',
      error: 'fa-solid fa-exclamation-circle text-red-500 flex-shrink-0',
      warning: 'fa-solid fa-exclamation-triangle text-yellow-500 flex-shrink-0',
      info: 'fa-solid fa-info-circle text-blue-500 flex-shrink-0'
    };

    return icons[toast.type];
  }

  hide(): void {
    this.toastService.hide();
  }
}
