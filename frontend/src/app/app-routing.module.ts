import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './core/guards/auth.guard';
import { RoleGuard } from './core/guards/role.guard';

const routes: Routes = [
  { path: '', redirectTo: '/queue', pathMatch: 'full' },
  {
    path: 'auth',
    loadChildren: () => import('./features/auth/auth.module').then(m => m.AuthModule)
  },
  {
    path: 'queue',
    canActivate: [AuthGuard],
    loadChildren: () => import('./features/queue/queue.module').then(m => m.QueueModule)
  },
  {
    path: 'attendant',
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['admin', 'attendant'] },
    loadChildren: () => import('./features/attendant/attendant.module').then(m => m.AttendantModule)
  },
  {
    path: 'admin',
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['admin'] },
    loadChildren: () => import('./features/admin/admin.module').then(m => m.AdminModule)
  },
  { path: '**', redirectTo: '/queue' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {}