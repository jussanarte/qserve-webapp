import { Injectable, signal } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class ThemeService {
  // Signal para estado do tema (true = dark)
  darkMode = signal<boolean>(localStorage.getItem('theme') === 'dark');

  constructor() {
    this.applyTheme();
  }

  toggleTheme() {
    this.darkMode.set(!this.darkMode());
    localStorage.setItem('theme', this.darkMode() ? 'dark' : 'light');
    this.applyTheme();
  }

  private applyTheme() {
    if (this.darkMode()) {
      document.body.classList.add('dark-mode');
    } else {
      document.body.classList.remove('dark-mode');
    }
  }
}