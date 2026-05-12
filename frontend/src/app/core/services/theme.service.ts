import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ThemeService {
  private theme$ = new BehaviorSubject<'light' | 'dark'>(this.loadTheme());

  constructor() { this.apply(this.theme$.value); }

  toggle(): void {
    this.setTheme(this.theme$.value === 'light' ? 'dark' : 'light');
  }

  setTheme(theme: 'light' | 'dark'): void {
    this.theme$.next(theme);
    localStorage.setItem('qserve-theme', theme);
    this.apply(theme);
  }

  getTheme$(): Observable<'light' | 'dark'> { return this.theme$.asObservable(); }
  getTheme(): 'light' | 'dark' { return this.theme$.value; }

  private apply(theme: 'light' | 'dark'): void {
    document.documentElement.setAttribute('data-theme', theme);
  }

  private loadTheme(): 'light' | 'dark' {
    const saved = localStorage.getItem('qserve-theme');
    if (saved === 'light' || saved === 'dark') return saved;
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }
}
