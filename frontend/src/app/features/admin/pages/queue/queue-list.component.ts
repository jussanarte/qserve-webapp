import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { QueueService } from '../../../../core/services/queue.service';
import { Queue, QueueStatus } from '../../../../core/models/queue.model';

@Component({
  selector: 'app-queue-list',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, TranslateModule],
  templateUrl: './queue-list.component.html',
  styleUrls: ['./queue-list.component.scss']
})
export class QueueListComponent implements OnInit {
  queues: Queue[]       = [];
  loading               = true;
  showModal             = false;
  editingQueue: Queue | null = null;
  form: FormGroup;
  saving                = false;
  error                 = '';

  constructor(private qs: QueueService, private fb: FormBuilder) {
    this.form = this.fb.group({
      name:             ['', [Validators.required, Validators.minLength(3)]],
      description:      [''],
      status:           ['closed'],
      max_capacity:     [50, [Validators.required, Validators.min(1)]],
      avg_service_time: [5,  [Validators.required, Validators.min(1)]],
    });
  }

  ngOnInit(): void { this.load(); }

  load(): void {
    this.loading = true;
    this.qs.getAll().subscribe({ next: q => { this.queues = q; this.loading = false; }, error: () => { this.loading = false; } });
  }

  openCreate(): void {
    this.editingQueue = null;
    this.form.reset({ status: 'closed', max_capacity: 50, avg_service_time: 5 });
    this.showModal = true;
    this.error = '';
  }

  openEdit(q: Queue): void {
    this.editingQueue = q;
    this.form.patchValue(q);
    this.showModal = true;
    this.error = '';
  }

  save(): void {
    if (this.form.invalid) return;
    this.saving = true;
    const action = this.editingQueue
      ? this.qs.update(this.editingQueue.id, this.form.value)
      : this.qs.create(this.form.value);

    action.subscribe({
      next: () => { this.showModal = false; this.saving = false; this.load(); },
      error: (err) => { this.error = err.error?.message ?? 'Erro'; this.saving = false; }
    });
  }

  changeStatus(q: Queue, status: QueueStatus): void {
    this.qs.changeStatus(q.id, status).subscribe(() => this.load());
  }

  delete(q: Queue): void {
    if (!confirm(`Eliminar a fila "${q.name}"?`)) return;
    this.qs.delete(q.id).subscribe({ next: () => this.load(), error: (err) => alert(err.error?.message) });
  }
}
