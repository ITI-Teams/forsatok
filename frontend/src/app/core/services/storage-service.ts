import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class StorageService {
  private getStorage(useSession: boolean): Storage {
    return useSession ? sessionStorage : localStorage;
  }

  setItem(key: string, value: any, useSession = false): void {
    const storage = this.getStorage(useSession);
    storage.setItem(key, JSON.stringify(value));
  }

  getItem<T>(key: string, useSession = false): T | null {
    const storage = this.getStorage(useSession);
    const item = storage.getItem(key);
    return item ? (JSON.parse(item) as T) : null;
  }

  removeItem(key: string, useSession = false): void {
    const storage = this.getStorage(useSession);
    storage.removeItem(key);
  }

  clear(useSession = false): void {
    const storage = this.getStorage(useSession);
    storage.clear();
  }

  hasItem(key: string, useSession = false): boolean {
    const storage = this.getStorage(useSession);
    return storage.getItem(key) !== null;
  }
}
