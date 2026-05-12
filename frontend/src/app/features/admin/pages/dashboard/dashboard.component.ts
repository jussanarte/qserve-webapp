import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TranslateModule } from '@ngx-translate/core';
import { HttpClient } from '@angular/common/http';
import { Subscription, interval, switchMap, startWith } from 'rxjs';
import { environment } from '../../../../../environments/environment';

interface DashboardStats {
  total_users: number;
  active_queues: number;
  tickets_today: number;
  avg_service_time: number;
  tickets_by_status: { waiting: number; called: number; served: number; cancelled: number };
  tickets_by_hour: number[];
  queue_summaries: { id: number; name: string; waiting: number; served_today: number }[];
}

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, TranslateModule],
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit, OnDestroy {
  stats: DashboardStats | null = null;
  private sub!: Subscription;

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    this.sub = interval(30000).pipe(
      startWith(0),
      switchMap(() => this.http.get<{ data: DashboardStats }>(`${environment.apiUrl}/dashboard/stats`))
    ).subscribe({ next: r => this.stats = r.data, error: () => {} });
  }

  get chartBars(): { height: number; hour: string }[] {
    if (!this.stats) return [];
    const max = Math.max(...this.stats.tickets_by_hour, 1);
    return this.stats.tickets_by_hour.map((v, i) => ({
      height: Math.round((v / max) * 100),
      hour: `${i}h`
    }));
  }

  barX(index: number): number {
    return index * 24 + 8;
  }

  barY(height: number): number {
    return 128 - Math.max(height, 2);
  }

  barHeight(height: number): number {
    return Math.max(height, 2);
  }

  ngOnDestroy(): void { this.sub?.unsubscribe(); }
}
