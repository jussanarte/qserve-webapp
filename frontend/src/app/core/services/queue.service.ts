import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from '../../../environment/environment';
import { Queue, Ticket } from '../models/queue.model';
import { AuthService } from './auth.service';

@Injectable({ providedIn: 'root' })
export class QueueService {
  private api = environment.apiUrl;

  constructor(private http: HttpClient, private auth: AuthService) {}

  getAll(): Observable<Queue[]> {
    return this.http.get<{ data: Queue[] }>(`${this.api}/queues`)
      .pipe(map(r => r.data));
  }

  getById(id: number): Observable<Queue> {
    return this.http.get<{ data: Queue }>(`${this.api}/queues/${id}`)
      .pipe(map(r => r.data));
  }

  create(data: Partial<Queue>): Observable<Queue> {
    return this.http.post<{ data: Queue }>(`${this.api}/queues`, data)
      .pipe(map(r => r.data));
  }

  update(id: number, data: Partial<Queue>): Observable<Queue> {
    return this.http.put<{ data: Queue }>(`${this.api}/queues/${id}`, data)
      .pipe(map(r => r.data));
  }

  changeStatus(id: number, status: string): Observable<Queue> {
    return this.http.patch<{ data: Queue }>(`${this.api}/queues/${id}/status`, { status })
      .pipe(map(r => r.data));
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.api}/queues/${id}`);
  }

  joinQueue(queueId: number): Observable<Ticket> {
    return this.http.post<{ data: Ticket }>(`${this.api}/tickets`, { queue_id: queueId })
      .pipe(map(r => r.data));
  }

  myTickets(): Observable<Ticket[]> {
    return this.http.get<{ data: Ticket[] }>(`${this.api}/tickets/mine`)
      .pipe(map(r => r.data));
  }

  callNext(queueId: number): Observable<Ticket | null> {
    return this.http.post<{ data: Ticket | null }>(`${this.api}/tickets/call-next`, { queue_id: queueId })
      .pipe(map(r => r.data));
  }

  updateTicketStatus(ticketId: number, status: string): Observable<void> {
    return this.http.patch<void>(`${this.api}/tickets/${ticketId}/status`, { status });
  }

  getQueueTickets(queueId: number): Observable<Ticket[]> {
    return this.http.get<{ data: Ticket[] }>(`${this.api}/queues/${queueId}/tickets`)
      .pipe(map(r => r.data));
  }
}