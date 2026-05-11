import { Routes } from '@angular/router';

export const queueRoutes: Routes = [
  {
    path: '',
    loadComponent: () =>
      import('./queue.component').then(m => m.QueueComponent)
  }
];