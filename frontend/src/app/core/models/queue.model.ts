export type QueueStatus  = 'open' | 'paused' | 'closed';
export type TicketStatus = 'waiting' | 'called' | 'served' | 'cancelled';

export interface Queue {
  id: number;
  name: string;
  description: string;
  status: QueueStatus;
  max_capacity: number;
  avg_service_time: number;
  waiting_count: number;
  created_by_name: string;
}

export interface Ticket {
  id: number;
  queue_id: number;
  queue_name: string;
  ticket_number: string;
  status: TicketStatus;
  position?: number;
  estimated_wait?: number;
  created_at: string;
  called_at?: string;
  served_at?: string;
}

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}