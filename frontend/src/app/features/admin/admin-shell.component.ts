import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { TranslateModule } from '@ngx-translate/core';

@Component({
  selector: 'app-admin-shell',
  standalone: true,
  imports: [CommonModule, RouterModule, TranslateModule],
  template: `
    <div class="admin-layout">
      <aside class="sidebar">
        <nav>
          <a routerLink="dashboard" routerLinkActive="active">{{ 'NAV.DASHBOARD' | translate }}</a>
          <a routerLink="queues" routerLinkActive="active">{{ 'NAV.QUEUES' | translate }}</a>
          <a routerLink="staff" routerLinkActive="active">{{ 'NAV.STAFF' | translate }}</a>
          <a routerLink="reports" routerLinkActive="active">{{ 'NAV.REPORTS' | translate }}</a>
        </nav>
      </aside>
      <main class="content">
        <router-outlet />
      </main>
    </div>
  `,
  styles: [`
    .admin-layout { display: flex; min-height: calc(100vh - 56px); }
    .sidebar {
      width: 200px; background: var(--color-accent); padding: 24px 0;
      flex-shrink: 0;
      nav { display: flex; flex-direction: column; gap: 4px; padding: 0 12px; }
      a {
        padding: 10px 14px; border-radius: var(--radius-sm);
        color: var(--color-nav-muted); text-decoration: none;
        font-size: 14px; font-weight: 500; transition: all 0.15s;
        &:hover, &.active { color: var(--color-primary); background: var(--color-nav-active); }
      }
    }
    .content { flex: 1; padding: 24px; overflow-y: auto; }
  `]
})
export class AdminShellComponent {}
