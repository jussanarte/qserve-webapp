import { Routes } from '@angular/router';

export const adminRoutes: Routes = [
  {
    path: '',
    loadComponent: () => import('./admin-shell.component').then(m => m.AdminShellComponent),
    children: [
      { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
      {
        path: 'dashboard',
        loadComponent: () => import('./pages/dashboard/dashboard.component').then(m => m.DashboardComponent)
      },
      {
        path: 'queues',
        loadComponent: () => import('./pages/queue/queue-list.component').then(m => m.QueueListComponent)
      },
      {
        path: 'staff',
        loadComponent: () => import('./pages/staff/staff-list.component').then(m => m.StaffListComponent)
      },
      {
        path: 'reports',
        loadComponent: () => import('./pages/reports/reports.component').then(m => m.ReportsComponent)
      },
    ]
  }
];
