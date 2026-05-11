import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TranslateModule } from '@ngx-translate/core';
import { Subscription, interval, switchMap, startWith } from 'rxjs';
import { QueueService } from '../../core/services/queue.service';
import { Queue, Ticket } from '../../core/models/queue.model';

@Component({
  selector: 'app-attendant',
  standalone: true,
  imports: [CommonModule, TranslateModule],
  templateUrl: './attendant.component.html',
  styleUrls: ['./attendant.component.scss']
})
export class AttendantComponent implements OnInit, OnDestroy {
  queues: Queue[]             = [];
  selectedQueue: Queue | null = null;
  tickets: Ticket[]           = [];
  calledTicket: Ticket | null = null;
  private sub!: Subscription;

  constructor(private queueService: QueueService) {}

  ngOnInit(): void {
    this.queueService.getAll().subscribe(q => this.queues = q);
  }

  selectQueue(queue: Queue): void {
    this.selectedQueue = queue;
    this.sub?.unsubscribe();
    this.sub = interval(5000).pipe(
      startWith(0),
      switchMap(() => this.queueService.getQueueTickets(queue.id))
    ).subscribe(t => this.tickets = t);
  }

  callNext(): void {
    if (!this.selectedQueue) return;
    this.queueService.callNext(this.selectedQueue.id).subscribe(t => {
      this.calledTicket = t;
    });
  }

  markServed(): void {
    if (!this.calledTicket) return;
    this.queueService.updateTicketStatus(this.calledTicket.id, 'served').subscribe(() => {
      this.calledTicket = null;
    });
  }

  markNoShow(): void {
    if (!this.calledTicket) return;
    this.queueService.updateTicketStatus(this.calledTicket.id, 'cancelled').subscribe(() => {
      this.calledTicket = null;
    });
  }

  get waitingTickets(): Ticket[] {
    return this.tickets.filter(t => t.status === 'waiting');
  }

  ngOnDestroy(): void { this.sub?.unsubscribe(); }
}