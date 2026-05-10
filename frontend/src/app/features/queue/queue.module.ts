import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { QueueComponent } from './queue.component';

@NgModule({
  declarations: [QueueComponent],
  imports: [
    CommonModule,
    RouterModule.forChild([
      { path: '', component: QueueComponent }
    ])
  ]
})
export class QueueModule {}