import { Component, Input } from '@angular/core';

@Component({
  selector: 'qs-spinner',
  template: `<div class="spinner" [class]="size"></div>`,
  styles: [`
    .spinner {
      border: 3px solid var(--color-border);
      border-top-color: var(--color-secondary);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }
    .sm { width: 16px; height: 16px; }
    .md { width: 24px; height: 24px; }
    .lg { width: 40px; height: 40px; }
    @keyframes spin { to { transform: rotate(360deg); } }
  `]
})
export class SpinnerComponent {
  @Input() size: 'sm' | 'md' | 'lg' = 'md';
}