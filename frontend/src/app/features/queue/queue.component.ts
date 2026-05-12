import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TranslateModule } from '@ngx-translate/core';
import { Subscription, interval, switchMap, startWith } from 'rxjs';
import { QueueService } from '../../core/services/queue.service';
import { Queue, Ticket } from '../../core/models/queue.model';
import { TicketQrComponent } from '../../core/ticket-qr/ticket-qr.component';

@Component({
  selector: 'app-queue',
  standalone: true,
  imports: [CommonModule, TranslateModule, TicketQrComponent],
  templateUrl: './queue.component.html',
  styleUrls: ['./queue.component.scss']
})
export class QueueComponent implements OnInit, OnDestroy {
  queues: Queue[]          = [];
  myTickets: Ticket[]      = [];
  loading                  = true;
  joiningId: number | null = null;
  selectedTicket: Ticket | null = null;
  showQr = false;
  private sub!: Subscription;

  constructor(private queueService: QueueService) {}

  ngOnInit(): void {
    this.sub = interval(5000).pipe(
      startWith(0),
      switchMap(() => this.queueService.getAll())
    ).subscribe({ next: q => { this.queues = q; this.loading = false; }, error: () => { this.loading = false; } });

    this.loadMyTickets();
  }

  loadMyTickets(): void {
    this.queueService.myTickets().subscribe({ next: t => this.myTickets = t, error: () => {} });
  }

  join(queueId: number): void {
    this.joiningId = queueId;
    this.queueService.joinQueue(queueId).subscribe({
      next: () => { this.loadMyTickets(); this.joiningId = null; },
      error: (err) => { alert(err.error?.message ?? 'Erro'); this.joiningId = null; }
    });
  }

  hasActiveTicket(queueId: number): boolean {
    return this.myTickets.some(t => t.queue_id === queueId && ['waiting','called'].includes(t.status));
  }

  getActiveTicket(queueId: number): Ticket | undefined {
    return this.myTickets.find(t => t.queue_id === queueId && ['waiting','called'].includes(t.status));
  }

  openQr(ticket: Ticket): void {
    this.selectedTicket = ticket;
    this.showQr = true;
  }

  closeQr(): void {
    this.showQr = false;
    this.selectedTicket = null;
  }

  ngOnDestroy(): void { this.sub?.unsubscribe(); }
}
