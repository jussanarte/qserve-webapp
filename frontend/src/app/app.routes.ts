import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth.guard';
import { roleGuard } from './core/guards/role.guard';

export const routes: Routes = [
  {
    path: '',
    pathMatch: 'full',
    loadComponent: () =>
      import('./features/auth/components/login/login.component').then(m => m.LoginComponent)
  },
  {
    path: 'auth/login',
    loadComponent: () =>
      import('./features/auth/components/login/login.component').then(m => m.LoginComponent)
  },
  {
    path: 'auth/register',
    loadComponent: () =>
      import('./features/auth/components/register/register.component').then(m => m.RegisterComponent)
  },
  {
    path: 'queue',
    canActivate: [authGuard],
    loadChildren: () =>
      import('./features/queue/queue.routes').then(m => m.queueRoutes)
  },
  {
    path: 'attendant',
    canActivate: [authGuard, roleGuard],
    data: { roles: ['admin', 'attendant'] },
    loadChildren: () =>
      import('./features/attendant/attendant.routes').then(m => m.attendantRoutes)
  },
  {
    path: 'admin',
    canActivate: [authGuard, roleGuard],
    data: { roles: ['admin'] },
    loadChildren: () =>
      import('./features/admin/admin.routes').then(m => m.adminRoutes)
  },
  { path: '**', redirectTo: '/queue' }
];
