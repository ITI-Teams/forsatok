import { Injectable, inject } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

export interface Toast {
  message: string;
  type: 'success' | 'error' | 'warning' | 'info';
  duration?: number;
}

@Injectable({
  providedIn: 'root'
})
export class ToastService {
  private toastSubject = new BehaviorSubject<Toast | null>(null);
  public readonly toast$ = this.toastSubject.asObservable();

  show(toast: Toast): void {
    this.toastSubject.next(toast);

    if (toast.duration !== 0) {
      setTimeout(() => {
        this.hide();
      }, toast.duration || 3000);
    }
  }

  hide(): void {
    this.toastSubject.next(null);
  }

  success(message: string, duration?: number): void {
    this.show({ message, type: 'success', duration });
  }

  error(message: string, duration?: number): void {
    this.show({ message, type: 'error', duration });
  }

  warning(message: string, duration?: number): void {
    this.show({ message, type: 'warning', duration });
  }

  info(message: string, duration?: number): void {
    this.show({ message, type: 'info', duration });
  }
}

// Optional: Create a convenient inject function
export function useToast() {
  return inject(ToastService);
}
