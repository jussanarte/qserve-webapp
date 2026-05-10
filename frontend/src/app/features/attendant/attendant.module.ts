import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AttendantComponent } from './attendant.component';

@NgModule({
  declarations: [AttendantComponent],
  imports: [
    CommonModule,
    RouterModule.forChild([
      { path: '', component: AttendantComponent }
    ])
  ]
})
export class AttendantModule {}