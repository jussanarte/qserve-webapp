import { Routes } from '@angular/router';

export const attendantRoutes: Routes = [
  {
    path: '',
    loadComponent: () =>
      import('./attendant.component').then(m => m.AttendantComponent)
  }
];