import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { Queue, Ticket, ApiResponse } from '../models/queue.model';

@Injectable({ providedIn: 'root' })
export class QueueService {
  private api = environment.apiUrl;

  constructor(private http: HttpClient) {}

  getAll(): Observable<Queue[]> {
    return this.http.get<ApiResponse<Queue[]>>(`${this.api}/queues`)
      .pipe(map(r => r.data));
  }

  getById(id: number): Observable<Queue> {
    return this.http.get<ApiResponse<Queue>>(`${this.api}/queues/${id}`)
      .pipe(map(r => r.data));
  }

  create(data: Partial<Queue>): Observable<Queue> {
    return this.http.post<ApiResponse<Queue>>(`${this.api}/queues`, data)
      .pipe(map(r => r.data));
  }

  update(id: number, data: Partial<Queue>): Observable<Queue> {
    return this.http.put<ApiResponse<Queue>>(`${this.api}/queues/${id}`, data)
      .pipe(map(r => r.data));
  }

  changeStatus(id: number, status: string): Observable<Queue> {
    return this.http.patch<ApiResponse<Queue>>(`${this.api}/queues/${id}/status`, { status })
      .pipe(map(r => r.data));
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.api}/queues/${id}`);
  }

  joinQueue(queueId: number): Observable<Ticket> {
    return this.http.post<ApiResponse<Ticket>>(`${this.api}/tickets`, { queue_id: queueId })
      .pipe(map(r => r.data));
  }

  myTickets(): Observable<Ticket[]> {
    return this.http.get<ApiResponse<Ticket[]>>(`${this.api}/tickets/mine`)
      .pipe(map(r => r.data));
  }

  callNext(queueId: number): Observable<Ticket | null> {
    return this.http.post<ApiResponse<Ticket | null>>(`${this.api}/tickets/call-next`, { queue_id: queueId })
      .pipe(map(r => r.data));
  }

  updateTicketStatus(ticketId: number, status: string): Observable<void> {
    return this.http.patch<void>(`${this.api}/tickets/${ticketId}/status`, { status });
  }

  getQueueTickets(queueId: number): Observable<Ticket[]> {
    return this.http.get<ApiResponse<Ticket[]>>(`${this.api}/queues/${queueId}/tickets`)
      .pipe(map(r => r.data));
  }
}